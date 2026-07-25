<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Models\Order;
use Modules\Ecommerce\Support\EcommerceClientContext;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('ecommerce')->user();

        return view('ecommerce::account.index', [
            'paymentMethods' => $user->paymentMethods()->orderBy('is_default', 'desc')->get(),
            'addresses' => $user->addresses()->orderBy('is_default', 'desc')->get(),
            'orders' => $this->ordersFor($user),
        ]);
    }

    public function orderHistory(Request $request)
    {
        return $this->index($request);
    }

    public function showOrder(Request $request, string $store, string $id)
    {
        $user = Auth::guard('ecommerce')->user();
        $order = $this->ordersFor($user)->firstWhere('id', $id);

        abort_unless($order, 404);

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    public function confirmReceived(Request $request, string $store, string $id)
    {
        $user = Auth::guard('ecommerce')->user();
        $order = Order::query()
            ->where('user_id', $user->id)
            ->whereKey($id)
            ->firstOrFail();

        $clientId = app(EcommerceClientContext::class)->clientId();
        $fulfillmentSchema = Schema::connection('order_fulfillment');

        if ($fulfillmentSchema->hasTable('shipments')) {
            $shipmentQuery = DB::connection('order_fulfillment')
                ->table('shipments')
                ->where('order_id', $order->id);
            if ($clientId !== null && $fulfillmentSchema->hasColumn('shipments', 'client_id')) {
                $shipmentQuery->where('client_id', $clientId);
            }

            $driverIds = $fulfillmentSchema->hasColumn('shipments', 'delivery_man_id')
                ? $shipmentQuery->pluck('delivery_man_id')->filter()->all()
                : [];
            $shipmentQuery->update(['status' => 'DELIVERED', 'updated_at' => now()]);

            $hrSchema = Schema::connection('hr');
            if ($driverIds !== [] && $hrSchema->hasTable('delivery_drivers')) {
                $drivers = DB::connection('hr')->table('delivery_drivers')->whereIn('id', $driverIds);
                if ($clientId !== null && $hrSchema->hasColumn('delivery_drivers', 'client_id')) {
                    $drivers->where('client_id', $clientId);
                }
                $drivers->update(['availability' => 'AVAILABLE', 'updated_at' => now()]);
            }
        }

        if ($fulfillmentSchema->hasTable('orders')) {
            $fulfillmentOrder = DB::connection('order_fulfillment')
                ->table('orders')
                ->where('id', $order->id);
            if ($clientId !== null && $fulfillmentSchema->hasColumn('orders', 'client_id')) {
                $fulfillmentOrder->where('client_id', $clientId);
            }
            $fulfillmentOrder->update(['status' => 'DELIVERED', 'updated_at' => now()]);
        }

        $order->update(['status' => 'delivered']);

        $response = [
            'success' => true,
            'status' => 'DELIVERED',
        ];

        if ($request->expectsJson()) {
            return response()->json($response);
        }

        return redirect()
            ->route('ecommerce.account.order-history')
            ->with('success', 'Order confirmed as received. Thank you.');
    }

    /**
     * Load an authenticated customer's orders and enrich them with the
     * fulfillment and shipment status for the same storefront client.
     */
    private function ordersFor($user)
    {
        $orders = Order::with('items')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $orderIds = $orders->pluck('id')->filter()->all();
        if ($orderIds === [] || ! Schema::connection('order_fulfillment')->hasTable('orders')) {
            return $orders;
        }

        $clientId = app(EcommerceClientContext::class)->clientId();
        $fulfillmentQuery = DB::connection('order_fulfillment')
            ->table('orders')
            ->whereIn('id', $orderIds);
        if ($clientId !== null && Schema::connection('order_fulfillment')->hasColumn('orders', 'client_id')) {
            $fulfillmentQuery->where('client_id', $clientId);
        }
        $fulfillmentOrders = $fulfillmentQuery->get()->keyBy('id');

        $shipments = collect();
        if (Schema::connection('order_fulfillment')->hasTable('shipments')) {
            $shipmentQuery = DB::connection('order_fulfillment')
                ->table('shipments')
                ->whereIn('order_id', $orderIds);
            if ($clientId !== null && Schema::connection('order_fulfillment')->hasColumn('shipments', 'client_id')) {
                $shipmentQuery->where('client_id', $clientId);
            }
            $shipments = $shipmentQuery->get()->keyBy('order_id');
        }

        return $orders->each(function (Order $order) use ($fulfillmentOrders, $shipments): void {
            $fulfillment = $fulfillmentOrders->get($order->id);
            $shipment = $shipments->get($order->id);

            $order->setAttribute('fulfillment_status', strtoupper($fulfillment->status ?? $shipment->status ?? $order->status ?? 'NEW'));
            $order->setAttribute('shipment_details', $shipment);
            $address = $order->shipping_address;
            if (is_string($address)) {
                $address = json_decode($address, true);
            }
            $order->setAttribute('shipping_address_parsed', $address);
            if (! $order->tracking_number && $shipment?->tracking_number) {
                $order->setAttribute('tracking_number', $shipment->tracking_number);
            }
        });
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('ecommerce')->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'gender' => 'nullable|in:male,female,other',
            'dob_year' => 'nullable|integer',
            'dob_month' => 'nullable|integer',
            'dob_day' => 'nullable|integer',
        ]);

        $user->name = $validated['name'] ?? null;
        $user->email = $validated['email'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->gender = $validated['gender'] ?? null;

        if (!empty($validated['dob_year']) && !empty($validated['dob_month']) && !empty($validated['dob_day'])) {
            try {
                $user->dob = Carbon::createFromDate($validated['dob_year'], $validated['dob_month'], $validated['dob_day'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Invalid date
            }
        }

        $user->save();

        return redirect()->route('ecommerce.account.profile')->with('success', 'Profile updated successfully!');
    }
}
