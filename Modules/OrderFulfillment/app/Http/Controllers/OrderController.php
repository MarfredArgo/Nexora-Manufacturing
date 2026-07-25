<?php

namespace Modules\OrderFulfillment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\OrderFulfillment\Models\Order;
use Modules\OrderFulfillment\Models\OrderItem;
use Modules\OrderFulfillment\Models\PackingMaterial;
use Modules\OrderFulfillment\Models\Shipment;
use Modules\OrderFulfillment\Http\Controllers\Concerns\CancelsShipmentToReturn;

class OrderController extends Controller
{
    use CancelsShipmentToReturn;

    /**
     * Single order detail page. Moved here from the abstract base
     * Controller, where it didn't belong (every controller inherited
     * an order-specific method) and was broken anyway (Order class
     * was never imported there).
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $this->attachLineItems(collect([$order]));

        return view('orders.show', compact('order'));
    }

    /**
     * Orders page (order.blade.php)
     */
    public function index()
    {
        $orders       = Order::orderByDesc('created_at')->get();
        $this->attachLineItems($orders);
        $total        = Order::count();
        $inPacking    = Order::where('status', 'PACKING')->count();
        // "In shipping" = every non-delivered shipping status — dispatched
        // but not yet delivered (or delayed en route). Previously this only
        // counted literal status == 'SHIPPED', undercounting anything
        // sitting in READY_TO_SHIP, OUT_FOR_DELIVERY, or DELAYED.
        $inShipping   = Order::whereIn('status', ['READY_TO_SHIP', 'SHIPPED', 'OUT_FOR_DELIVERY', 'DELAYED'])->count();
        $delivered    = Order::where('status', 'DELIVERED')->count();
        $onTimeRate   = $total > 0 ? round(($delivered / $total) * 100) : 0;
        // The Orders template uses these names for its stat cards.
        $totalOrders       = $total;
        $inPackingCount    = $inPacking;
        $inShippingCount   = $inShipping;
        $totalFulfilled    = $delivered;

        // The board on order.blade.php renders three separate columns, each
        // backed by its own query — not a client-side filter over $orders.
        $newOrders     = Order::where('status', 'NEW')->orderByDesc('created_at')->get();
        $packingOrders = Order::where('status', 'PACKING')->orderByDesc('created_at')->get();
        // Everything that has REACHED shipping or later, so an order doesn't
        // vanish from this column the moment it advances past SHIPPED.
        $shippedOrders = Order::whereIn('status', ['SHIPPED', 'OUT_FOR_DELIVERY', 'DELIVERED'])
            ->orderByDesc('created_at')
            ->get();

        // Sidebar "Alerts" panel — newly received orders.
        $alerts = $newOrders;

        // Any status other than NEW represents a change that happened after
        // the order was placed, so surface all of them here (not just a
        // hardcoded subset) — that's what keeps this feed in sync with
        // whatever the order's current status actually is.
        $activity = Order::where('status', '!=', 'NEW')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(function ($o) {
                $status = strtoupper($o->status);

                $activityMap = [
                    'PACKING'          => ['📦', "Order {$o->id} moved to packing"],
                    'READY_TO_SHIP'    => ['📦', "Order {$o->id} is ready for delivery"],
                    'OUT_FOR_DELIVERY' => ['🚚', "Order {$o->id} is out for delivery"],
                    'SHIPPED'          => ['🚚', "Order {$o->id} has been shipped"],
                    'DELIVERED'        => ['✅', "Order {$o->id} has been delivered"],
                    'CANCELLED'        => ['❌', "Order {$o->id} has been cancelled"],
                    'RETURNED'         => ['↩️', "Order {$o->id} was returned by the customer"],
                ];

                [$icon, $message] = $activityMap[$status] ?? ['🔄', "Order {$o->id} status changed to " . strtolower(str_replace('_', ' ', $status))];

                $o->activity_icon    = $icon;
                $o->activity_message = $message;

                return $o;
            });

        return view('order-fulfillment::order', compact(
            'orders', 'totalOrders', 'inPacking', 'inPackingCount', 'inShipping', 'inShippingCount', 'totalFulfilled', 'onTimeRate',
            'newOrders', 'packingOrders', 'shippedOrders', 'alerts', 'activity'
        ));
    }

    /**
     * Packing page (packing.blade.php)
     */
    public function packing()
    {
        $packingOrders    = Order::where('status', 'PACKING')->get();
        $readyToShipCount = Order::where('status', 'READY_TO_SHIP')->count();
        $packingErrorToday = 0; // TODO: hook up to a packing_errors table once one exists

        // packing_materials lives on the separate "inventory" connection,
        // not the default one — must go through the PackingMaterial model
        // (which declares that connection) rather than DB::table(), or this
        // silently queries the wrong database and returns nothing.
        $materials = PackingMaterial::all();

        return view('order-fulfillment::packing', compact(
            'packingOrders', 'readyToShipCount', 'packingErrorToday', 'materials'
        ));
    }

    /**
     * AJAX: mark an order as being prepared -> moves it to PACKING.
     * The order row itself is never deleted, only its status changes,
     * so it keeps showing on the Orders page while also now appearing
     * in the Packing column/queue on the dashboard and packing page.
     */
    public function prepare($id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (strtoupper($order->status) !== 'NEW') {
            return response()->json([
                'success' => false,
                'message' => 'Order is already ' . strtoupper($order->status) . '.',
            ], 409);
        }

        $order->update([
            'status'     => 'PACKING',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'status'  => 'PACKING',
        ]);
    }

    /**
     * AJAX: cancel an order.
     *
     * NEW / PACKING orders have no shipment yet, so this is just a status
     * flip to CANCELLED — same as before.
     *
     * READY_TO_SHIP / SHIPPED / DELAYED orders already have a live Shipment
     * record showing on the Shipping tab. For those, a plain status flip
     * here would leave that shipment stuck on the Shipping page forever, so
     * this instead runs the same shipment->Returns handoff that Shipping's
     * own cancel button uses: the shipment is marked CANCELLED (which pulls
     * it off the Shipping tab and, via Shipment::booted()'s `updated` hook,
     * mirrors CANCELLED onto the order too) and a matching row is created
     * on the Returns tab.
     *
     * Only OUT_FOR_DELIVERY and DELIVERED can no longer be cancelled — once
     * a driver has it, or it's already arrived, there's nothing left to
     * pull back.
     */
    public function cancel($id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $status = strtoupper($order->status);

        if ($status === 'CANCELLED') {
            return response()->json([
                'success' => false,
                'message' => 'Order is already cancelled.',
            ], 409);
        }

        if (in_array($status, $this->nonCancellableShipmentStatuses())) {
            return response()->json([
                'success' => false,
                'message' => 'Order has already been ' . strtolower(str_replace('_', ' ', $status)) . ' and can no longer be cancelled.',
            ], 409);
        }

        $shipment = Shipment::where('order_id', $order->id)
            ->whereNotIn('status', ['CANCELLED', 'DELIVERED'])
            ->first();

        if ($shipment) {
            $this->cancelShipmentToReturn($shipment, 'Cancelled before shipping');

            return response()->json([
                'success' => true,
                'status'  => 'CANCELLED',
            ]);
        }

        // No shipment yet (still NEW / PACKING) — nothing to move to
        // Returns, so just cancel the order directly.
        $order->update([
            'status'     => 'CANCELLED',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'status'  => 'CANCELLED',
        ]);
    }

    /**
     * Keep the Orders screen usable while upgrading existing installations.
     * Older fulfillment databases only store the summary fields on orders;
     * newer ones also have client-scoped order_items.  Supplying a synthetic
     * single line item for the former avoids querying a missing table and
     * preserves the same UI contract for both record formats.
     */
    private function attachLineItems(Collection $orders): void
    {
        if ($orders->isEmpty()) {
            return;
        }

        if (Schema::connection('order_fulfillment')->hasTable('order_items')) {
            $orders->load('items');
            return;
        }

        $orders->each(function (Order $order): void {
            $order->setRelation('items', collect([
                new OrderItem([
                    'order_id'       => $order->id,
                    'product_name'   => $order->product_name ?: 'Storefront order',
                    'qty'            => (int) ($order->qty ?: 1),
                    'product_amount' => (float) ($order->product_amount ?: 0),
                ]),
            ]));
        });
    }
}
