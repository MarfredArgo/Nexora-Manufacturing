<?php

namespace Modules\OrderFulfillment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\OrderFulfillment\Models\Order;
use Modules\OrderFulfillment\Models\ReturnItem;

class ReturnController extends Controller
{
    /**
     * Reasons that mean this ReturnItem exists because an admin cancelled
     * an order (OrderController::cancel / ShippingController::cancel via
     * CancelsShipmentToReturn), not because a customer requested a return.
     * Kept in sync with the ADMIN_CANCEL_REASONS list in return_blade.php,
     * which uses the same reasons to hide the Accept button.
     */
    private const ADMIN_CANCEL_REASONS = [
        'Cancelled while shipping',
        'Cancelled before shipping',
    ];

    /**
     * Statuses that count as this ReturnItem being fully settled — nothing
     * left to review or wait on.
     */
    private const TERMINAL_STATUSES = ['Refunded', 'Completed'];

    public function index()
    {
        // Orders that were cancelled after they'd already left for delivery
        // ("Cancelled while shipping") start out as In Transit to Warehouse /
        // Pending. Once they've had a day to make it back, auto-close them out
        // the same way ShippingController::index() promotes SHIPPED shipments.
        //
        // Looped (rather than a single mass ->update()) so each row can be
        // synced back onto its Order individually — syncOrderStatus() needs
        // each return's own order_id and reason to decide what, if anything,
        // to do to that order.
        ReturnItem::where('status', 'In Transit to Warehouse')
            ->where('updated_at', '<=', now()->subDay())
            ->get()
            ->each(function (ReturnItem $return) {
                $return->update([
                    'status'     => 'Completed',
                    'resolution' => 'Returned to Inventory',
                ]);

                $this->syncOrderStatus($return);
            });

        $returns = ReturnItem::all();

        $pendingReturns = ReturnItem::where('status', 'NEW')->count();

        $refundedToday = ReturnItem::whereDate(
            'updated_at',
            today()
        )->where('status', 'Refunded')
         ->count();

        return view('order-fulfillment::return', compact(
            'returns',
            'pendingReturns',
            'refundedToday'
        ));
    }

    /**
     * AJAX: accept a customer-initiated return request — moves it from NEW
     * (pending review) to Inspecting. Admin-cancellation returns are never
     * created at NEW (they start at 'In Transit to Warehouse'), so this
     * only ever applies to genuine return requests, matching why
     * return_blade.php hides the Accept button for the admin-cancel reasons.
     *
     * POST /returns/{id}/accept
     */
    public function accept($id): JsonResponse
    {
        $return = ReturnItem::find($id);

        if (!$return) {
            return response()->json([
                'success' => false,
                'message' => 'Return not found.',
            ], 404);
        }

        if ($return->status !== 'NEW') {
            return response()->json([
                'success' => false,
                'message' => 'Return is already ' . $return->status . '.',
            ], 409);
        }

        $return->update([
            'status'     => 'Inspecting',
            'resolution' => 'In Review',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'    => true,
            'status'     => $return->status,
            'resolution' => $return->resolution,
        ]);
    }

    /**
     * AJAX: move a return to any of its allowed statuses (Inspecting,
     * Refunded, Completed, ...). Generic on purpose so future Returns-tab
     * actions (a "Mark refunded" button, etc.) can reuse this one endpoint
     * instead of each getting their own bespoke route + controller method.
     *
     * POST /returns/{id}/status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $return = ReturnItem::find($id);

        if (!$return) {
            return response()->json([
                'success' => false,
                'message' => 'Return not found.',
            ], 404);
        }

        $validated = $request->validate([
            'status'     => 'required|string|in:NEW,Inspecting,In Transit to Warehouse,Refunded,Completed',
            'resolution' => 'nullable|string|max:255',
        ]);

        $return->update([
            'status'     => $validated['status'],
            'resolution' => $validated['resolution'] ?? $return->resolution,
            'updated_at' => now(),
        ]);

        $this->syncOrderStatus($return);

        return response()->json([
            'success'    => true,
            'status'     => $return->status,
            'resolution' => $return->resolution,
        ]);
    }

    /**
     * Reflect a settled return back onto its parent Order — the same idea
     * as CancelsShipmentToReturn, just running in the other direction and
     * at a different point in the lifecycle.
     */
    private function syncOrderStatus(ReturnItem $return): void
    {
        // Admin-initiated cancellations already flipped the Order to
        // CANCELLED at cancel time (OrderController::cancel /
        // ShippingController::cancel). That's correct and final — the
        // stock physically making it back to the warehouse doesn't change
        // what the order's outcome was, so there's nothing further to sync.
        if (in_array($return->reason, self::ADMIN_CANCEL_REASONS, true)) {
            return;
        }

        // A genuine customer return only reaches here once it's fully
        // settled (refunded or the item's back in inventory). The order
        // was sitting at DELIVERED up to this point — now that the return
        // is closed out, flip it to RETURNED so Dashboard/Orders/Shipping
        // all agree the order didn't end as a normal delivery.
        if (in_array($return->status, self::TERMINAL_STATUSES, true)) {
            Order::where('id', $return->order_id)->update([
                'status'     => 'RETURNED',
                'updated_at' => now(),
            ]);
        }
    }
}