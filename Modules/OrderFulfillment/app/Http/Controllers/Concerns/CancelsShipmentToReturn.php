<?php

namespace Modules\OrderFulfillment\Http\Controllers\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\HR\Models\DeliveryDriver;
use Modules\OrderFulfillment\Models\Order;
use Modules\OrderFulfillment\Models\OrderItem;
use Modules\OrderFulfillment\Models\ReturnItem;
use Modules\OrderFulfillment\Models\Shipment;

/**
 * Cancelling an order that's already left packing isn't a simple status
 * flip — it needs to disappear from the Shipping tab and show up on the
 * Returns tab instead. Both entry points that can trigger this
 * (OrderController::cancel, from the Orders tab, and
 * ShippingController::cancel, from the Shipping tab) share this trait so
 * they can't drift apart and produce two different outcomes for the same
 * action.
 */
trait CancelsShipmentToReturn
{
    /**
     * Statuses a shipment can no longer be cancelled from: once it's out
     * for delivery the driver already has it, and DELIVERED means it's
     * done. Both entry points share this list.
     */
    protected function nonCancellableShipmentStatuses(): array
    {
        return ['OUT_FOR_DELIVERY', 'DELIVERED'];
    }

    /**
     * Move a shipment to Returns: creates the matching ReturnItem, frees up
     * the assigned driver (if any), sets the parent Order to CANCELLED, and
     * deletes the shipment row itself — the Order is what stays behind as
     * the record of the cancellation, the Shipment row has no further use
     * once Returns owns it.
     *
     * The Order status has to be set explicitly here (rather than relying
     * on Shipment::booted()'s `updated` hook, which normally mirrors
     * Shipment status onto Order) because that hook only fires on update,
     * and this shipment is being deleted, not updated.
     */
    protected function cancelShipmentToReturn(Shipment $shipment, string $reason): void
    {
        DB::transaction(function () use ($shipment, $reason) {
            $orderItems = OrderItem::where('order_id', $shipment->order_id)
                ->get(['product_name', 'qty', 'product_amount']);

            // line_total isn't a real order_items column — it's derived
            // (qty * product_amount) — so sum it in PHP, same as everywhere
            // else this total is needed.
            $refundAmount = $orderItems->sum(function (OrderItem $item) {
                return $item->qty * $item->product_amount;
            });

            ReturnItem::create([
                'id'            => (string) Str::uuid(),
                'order_id'      => $shipment->order_id,
                'customer_name' => $shipment->customer_name,
                // returns.product_name is varchar(255) — an order with
                // several line items can easily blow past that once every
                // product_name is joined together (e.g. an 8-item order
                // failed here with "value too long for type character
                // varying(255)", which rolled back the whole cancel and
                // left the shipment un-cancellable). Str::limit keeps this
                // well under the column limit regardless of item count.
                'product_name'  => Str::limit(
                    $orderItems->pluck('product_name')->implode(', ') ?: 'N/A',
                    240
                ),
                'reason'        => $reason,
                // Nothing to "review" here — admin already decided to cancel
                // it, it just needs to physically make its way back to the
                // warehouse. ReturnController::index() auto-promotes this to
                // Completed / Returned to Inventory 24h later.
                'status'        => 'In Transit to Warehouse',
                'resolution'    => 'Pending',
                'due_date'      => $shipment->due_date,
                'address'       => $shipment->address,
                'refund_amount' => $refundAmount,
            ]);

            // Free up the driver, if one was already assigned, same as a
            // normal delivery completion would.
            if ($shipment->delivery_man_id) {
                DeliveryDriver::where('id', $shipment->delivery_man_id)
                    ->update(['availability' => DeliveryDriver::STATUS_AVAILABLE]);
            }

            // Set explicitly — deleting the shipment below means the
            // Shipment 'updated' hook that normally mirrors status onto
            // Order won't run. Order's primary key is 'id' (see Order.php),
            // matched against Shipment::order_id, same as
            // Shipment::booted()'s own hook does.
            Order::where('id', $shipment->order_id)
                ->update(['status' => 'CANCELLED']);

            $shipment->delete();
        });
    }
}
