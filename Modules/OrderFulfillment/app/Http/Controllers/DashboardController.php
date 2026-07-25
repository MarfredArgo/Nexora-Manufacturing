<?php

namespace Modules\OrderFulfillment\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = DB::connection('order_fulfillment')->table('orders');

        // Keep the dashboard in the owning module database, and never let a
        // normal employee see another client's operational data. Root-admin
        // module testing retains its temporary all-client view.
        if (! (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin')) {
            $orders->where('client_id', session('employee_client_id'));
        }
        // All data below must remain on the dedicated Order Fulfillment
        // connection. The prior DB::table calls silently switched back to
        // ITSM's main database.
        $totalOrders         = (clone $orders)->count();
        $inPackingCount      = (clone $orders)->where('status', 'PACKING')->count();
        // "In shipping" = every non-delivered shipping status — dispatched
        // but not yet delivered (or delayed en route). Previously this only
        // counted literal status == 'SHIPPED', which undercounted anything
        // sitting in READY_TO_SHIP, OUT_FOR_DELIVERY, or DELAYED.
        $inShippingCount     = (clone $orders)
            ->whereIn('status', ['READY_TO_SHIP', 'SHIPPED', 'OUT_FOR_DELIVERY', 'DELAYED'])
            ->count();
        $deliveredCount      = (clone $orders)->where('status', 'DELIVERED')->count();
        $totalFulfilled      = $deliveredCount;
        $onTimeRate          = $totalOrders > 0 ? round(($deliveredCount / $totalOrders) * 100) : 0;

        // ---- Board columns ----
        // The ORDERS column acts as a running log of every order, so it keeps
        // showing an order even after it moves on to packing/shipped/etc.
        $newOrders       = (clone $orders)->orderByDesc('created_at')->get();
        $packingOrders   = (clone $orders)->where('status', 'PACKING')->get();
        // Widened per SETUP_NOTES item 3 (plus READY_TO_SHIP, found afterward):
        // once an order leaves PACKING it should keep showing here all the way
        // through delivery, not just for the literal 'SHIPPED' status — otherwise
        // a READY_TO_SHIP order shows in neither the PACKING nor SHIPPED column.
        $shippedOrders   = (clone $orders)
            ->whereIn('status', ['READY_TO_SHIP', 'SHIPPED', 'OUT_FOR_DELIVERY', 'DELIVERED'])
            ->orderByDesc('created_at')
            ->get();
        $cancelledOrders = (clone $orders)->where('status', 'CANCELLED')->get();

        // ---- Sidebar ----.
        // Alerts should only reflect brand-new orders, not the full order log above.
        $alerts = $newOrders->where('status', 'NEW')->values();

        // Activity feed: packing + shipped + cancelled orders together, newest first.
        $activity = $packingOrders
            ->map(function ($order) {
                $order->activity_icon    = '📦';
                $order->activity_message = "Order {$order->id} moved to packing";
                $order->activity_time    = $order->updated_at ?? $order->created_at ?? null;
                return $order;
            })
            ->concat($shippedOrders->map(function ($order) {
                $status = strtoupper($order->status);

                if ($status === 'DELIVERED') {
                    $order->activity_icon    = '✅';
                    $order->activity_message = "Order {$order->id} has been delivered";
                } elseif ($status === 'OUT_FOR_DELIVERY') {
                    $order->activity_icon    = '🚛';
                    $order->activity_message = "Order {$order->id} is out for delivery";
                } elseif ($status === 'READY_TO_SHIP') {
                    $order->activity_icon    = '📬';
                    $order->activity_message = "Order {$order->id} is ready for delivery";
                } else {
                    $order->activity_icon    = '🚚';
                    $order->activity_message = "Order {$order->id} has been shipped";
                }

                $order->activity_time = $order->updated_at ?? $order->created_at ?? null;
                return $order;
            }))
            ->concat($cancelledOrders->map(function ($order) {
                $order->activity_icon    = '❌';
                $order->activity_message = "Order {$order->id} has been cancelled";
                $order->activity_time    = $order->updated_at ?? $order->created_at ?? null;
                return $order;
            }))
            ->sortByDesc('activity_time')
            ->values();

        return view('order-fulfillment::dashboard', compact(
            'totalOrders',
            'inPackingCount',
            'inShippingCount',
            'deliveredCount',
            'totalFulfilled',
            'onTimeRate',
            'newOrders',
            'packingOrders',
            'shippedOrders',
            'cancelledOrders',
            'alerts',
            'activity'
        ));
    }
}
