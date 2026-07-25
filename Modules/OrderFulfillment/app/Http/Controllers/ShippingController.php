<?php

namespace Modules\OrderFulfillment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\OrderFulfillment\Models\Order;
use Modules\OrderFulfillment\Models\Shipment;
use Modules\HR\Models\DeliveryDriver;
use Modules\OrderFulfillment\Helpers\OrderStatus;
use Modules\OrderFulfillment\Models\OrderItem;
use Modules\OrderFulfillment\Http\Controllers\Concerns\CancelsShipmentToReturn;

class ShippingController extends Controller
{
    use CancelsShipmentToReturn;

    public function index()
    {
        // Promote anything that's been sitting at SHIPPED for 24+ hours to
        // READY_TO_SHIP. Runs here instead of a scheduled command, so it
        // only recalculates when someone actually loads this page.
        Shipment::where('status', 'SHIPPED')
            ->whereNotNull('shipped_at')
            ->where('shipped_at', '<=', now()->subDay())
            ->update(['status' => 'READY_TO_SHIP']);

        // Promote anything that's been OUT_FOR_DELIVERY for 1+ hour to
        // DELIVERED. Same "recalculate on page load" pattern as the
        // promotion above — the difference is DELIVERED also needs to free
        // up the driver (a plain mass ->update() can't do that per-row), so
        // this loops each shipment individually instead.
        // Some existing installations predate this timestamp column.  Do
        // not make the whole Shipping screen unavailable while the additive
        // migration is being deployed; the normal shipping workflow remains
        // usable and the timer resumes automatically once it is present.
        if (Schema::connection('order_fulfillment')->hasColumn('shipments', 'out_for_delivery_at')) {
            Shipment::where('status', 'OUT_FOR_DELIVERY')
                ->whereNotNull('out_for_delivery_at')
                ->where('out_for_delivery_at', '<=', now()->subHour())
                ->get()
                ->each(function (Shipment $shipment) {
                    DB::transaction(function () use ($shipment) {
                        // Mirrors onto the parent Order via Shipment::booted()'s
                        // `updated` hook, same as every other status change here.
                        $shipment->update(['status' => 'DELIVERED']);

                        if ($shipment->delivery_man_id) {
                            DeliveryDriver::where('id', $shipment->delivery_man_id)
                                ->update(['availability' => DeliveryDriver::STATUS_AVAILABLE]);
                        }
                    });
                });
        }

        $shipments = Shipment::select(
            'shipment_id',
            'order_id',
            'customer_name',
            'status',
            'due_date',
            'address',
            'tracking_number',
            'courier',
            'amount',
            'delivery_man_id',
            'updated_at'
        )
        ->whereIn('status', [
            'SHIPPED',
            'READY_TO_SHIP',
            'OUT_FOR_DELIVERY',
            'DELAYED',
            'DELIVERED',
        ])
        ->get();

        // Pull every line item for the orders behind these shipments in one
        // query, then attach a plain-array 'items' + 'items_count' to each
        // shipment so the Items column and the item-breakdown modals (order
        // detail + assign-driver) can both be driven from the same data
        // that's already being @json()'d out to the page.
        $orderIds = $shipments->pluck('order_id')->filter()->unique()->values();

        $itemsByOrder = OrderItem::whereIn('order_id', $orderIds)
            ->get(['order_id', 'product_name', 'qty', 'product_amount'])
            ->groupBy('order_id');

        $shipments->each(function (Shipment $shipment) use ($itemsByOrder) {
            $orderItems = $itemsByOrder->get($shipment->order_id, collect());

            $shipment->items = $orderItems->map(function (OrderItem $item) {
                return [
                    'product_name'   => $item->product_name,
                    'qty'            => (int) $item->qty,
                    'product_amount' => (float) $item->product_amount,
                    'line_total'     => (float) $item->line_total,
                ];
            })->values()->toArray();

            $shipment->items_count = $orderItems->count();
        });

        // "In shipping" = every order that has left packing but hasn't been
        // delivered yet, across its whole lifetime (not just today) — so it
        // covers SHIPPED, READY_TO_SHIP, OUT_FOR_DELIVERY, and DELAYED.
        // Previously this only counted status == 'SHIPPED' updated today,
        // which undercounted anything sitting in the other in-transit
        // statuses (e.g. showed 5 instead of 7 with 7 shipped orders).
        $inShipping = Order::whereIn('status', ['SHIPPED', 'READY_TO_SHIP', 'OUT_FOR_DELIVERY', 'DELAYED'])
            ->count();

        $inTransit = Order::where('status', 'OUT_FOR_DELIVERY')->count();

        $delayed = Order::where('status', 'DELAYED')->count();

        $delivered = Order::where('status', 'DELIVERED')->count();

        // Delivery rate = delivered orders as a share of ALL orders.
        // Must use the same formula as the Dashboard and Orders tabs
        // (delivered / totalOrders), not delivered / (delivered + delayed)
        // — the latter ignores everything still sitting in packing or
        // in transit, so it can read 100% while most orders haven't
        // actually been delivered yet.
        $totalOrders = Order::count();

        $onTimeRate = $totalOrders > 0
            ? round(($delivered / $totalOrders) * 100)
            : 0;

        // Delivery alerts panel: recently-changed shipments, newest first.
        // Same idea as the dashboard's $alerts — just built from $shipments
        // instead of a separate query.
        $deliveryAlerts = $shipments->sortByDesc('updated_at')->take(10)->map(function ($s) {
            // OrderStatus::label() returns the shouty all-caps form used for
            // the status pills (e.g. "OUT FOR DELIVERY"). That reads fine on
            // a small badge but not sitting in a sentence, so title-case it
            // here for the alert feed only — pills elsewhere are untouched.
            $label = ucwords(strtolower(OrderStatus::label($s->status)));

            return (object) [
                'id'      => $s->shipment_id,
                'icon'    => '🔔',
                'message' => $s->shipment_id . ' is now ' . $label,
            ];
        })->values();

        return view('order-fulfillment::shipping', compact(
            'shipments',
            'inShipping',
            'inTransit',
            'delayed',
            'delivered',
            'onTimeRate',
            'deliveryAlerts'
        ));
    }

    /**
     * Available drivers for this shipment's courier — feeds the
     * "Assign Driver" modal.
     *
     * GET /shipping/{shipmentId}/drivers
     */
    public function drivers(string $shipmentId)
    {
        $shipment = Shipment::where('shipment_id', $shipmentId)->firstOrFail();

        $drivers = DeliveryDriver::with('employee')
            ->available()
            ->forCourier($shipment->courier)
            ->get()
            ->sortBy(fn (DeliveryDriver $driver) => strtolower(trim("{$driver->employee->first_name} {$driver->employee->last_name}")))
            ->values()
            ->map(fn (DeliveryDriver $driver) => [
                'id' => $driver->id,
                'name' => trim("{$driver->employee->first_name} {$driver->employee->last_name}"),
                'vehicle_type' => $driver->vehicle_type ?: 'Vehicle not specified',
                'plate_number' => $driver->plate_number ?: '—',
            ]);

        return response()->json($drivers);
    }

    /**
     * Assign a driver to a shipment: the shipment moves to OUT_FOR_DELIVERY
     * and the driver flips to UNAVAILABLE until the shipment is delivered.
     *
     * POST /shipping/{shipmentId}/assign-driver
     */
    public function assignDriver(Request $request, string $shipmentId)
    {
        // The HR-owned delivery_drivers table lives on a separate connection,
        // so the validation rule must be built from the model instead of
        // relying on Laravel's default database connection.
        // Resolving it off the model itself (rather than hardcoding a
        // connection name here) keeps this in sync automatically if that
        // ever changes.
        $driverModel = new DeliveryDriver();
        $driverTable = ($driverModel->getConnectionName() ? $driverModel->getConnectionName() . '.' : '') . $driverModel->getTable();

        $validated = $request->validate([
            // HR delivery_drivers uses the normal numeric primary key. The
            // former fulfillment-local directory used string IDs, so keep
            // this explicitly aligned with the new source of truth.
            'driver_id' => "required|integer|exists:{$driverTable},id",
        ]);

        $shipment = Shipment::where('shipment_id', $shipmentId)->firstOrFail();

        $driver = DeliveryDriver::available()
            ->where('id', $validated['driver_id'])
            ->first();

        if (! $driver) {
            return response()->json([
                'message' => 'That driver is no longer available. Please pick another.',
            ], 422);
        }

        DB::transaction(function () use ($shipment, $driver) {
            $changes = [
                'delivery_man_id'     => $driver->id,
                'status'              => 'OUT_FOR_DELIVERY',
            ];

            if (Schema::connection('order_fulfillment')->hasColumn('shipments', 'out_for_delivery_at')) {
                $changes['out_for_delivery_at'] = now();
            }

            $shipment->update($changes);

            $driver->update(['availability' => DeliveryDriver::STATUS_UNAVAILABLE]);
        });

        return response()->json([
            'message' => "{$driver->name} assigned to {$shipment->shipment_id}",
            'status' => $shipment->status,
        ]);
    }

    /**
     * Cancel a shipment: it disappears from the Shipping page and a matching
     * record is created on the Returns page instead.
     *
     * POST /shipping/{shipmentId}/cancel
     */
    public function cancel(string $shipmentId)
    {
        $shipment = Shipment::where('shipment_id', $shipmentId)->firstOrFail();

        $status = strtoupper($shipment->status);

        // Once a driver has it (OUT_FOR_DELIVERY) or it's already arrived
        // (DELIVERED), there's nothing left to pull back. Same rule
        // OrderController::cancel enforces from the Orders tab.
        if (in_array($status, $this->nonCancellableShipmentStatuses())) {
            return response()->json([
                'message' => 'This order is ' . strtolower(str_replace('_', ' ', $status)) . ' and can no longer be cancelled.',
            ], 422);
        }

        // Setting status to CANCELLED both removes it from the shipping
        // index (which only pulls SHIPPED/READY_TO_SHIP/OUT_FOR_DELIVERY/
        // DELAYED/DELIVERED) and, via Shipment::booted()'s `updated`
        // hook, mirrors the same status onto the parent Order.
        $this->cancelShipmentToReturn($shipment, 'Cancelled while shipping');

        return response()->json([
            'message' => 'Order cancelled and moved to Returns.',
        ]);
    }
}
