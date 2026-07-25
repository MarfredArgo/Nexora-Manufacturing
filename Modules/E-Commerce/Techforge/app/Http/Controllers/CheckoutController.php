<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Cart;
use Modules\Ecommerce\Models\Order;
use App\Services\ErpIntegrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Support\EcommerceClientContext;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!Auth::guard('ecommerce')->check()) {
            session()->put('redirect_after_auth', route('ecommerce.checkout.index'));
            return redirect()->route('ecommerce.login');
        }

        $cart = Cart::with('items')->where('user_id', Auth::guard('ecommerce')->id())->first();
        
        $cartItems = [];
        if ($cart) {
            foreach ($cart->items as $item) {
                $cartItems[] = [
                    'id' => $item->product_id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'image_url' => $item->image_url,
                    'product_type' => $item->product_type,
                    'configuration' => $item->configuration,
                ];
            }
        }

        if (count($cartItems) === 0) {
            return redirect()->route('ecommerce.cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cartItems)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        // Simple default shipping fee
        $shipping = 150; 
        $discount = 0;
        $total = $subtotal + $shipping - $discount;

        return view('ecommerce::checkout', compact('cartItems', 'subtotal', 'shipping', 'discount', 'total'));
    }

    public function process(Request $request)
    {
        if (!Auth::guard('ecommerce')->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'shippingMethod' => 'required|string',
            'paymentMethod' => 'required|string',
        ]);

        $cart = Cart::with('items')->where('user_id', Auth::guard('ecommerce')->id())->first();
        if (!$cart || $cart->items->count() === 0) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        $subtotal = $cart->items->sum(function($item) {
            return $item->price * $item->quantity;
        });

        $shippingFee = $request->shippingMethod === 'express' ? 300 : ($request->shippingMethod === 'pickup' ? 0 : 150);
        $total = $subtotal + $shippingFee;

        $clientId = app(EcommerceClientContext::class)->clientId();
        if (! $clientId) {
            return response()->json(['success' => false, 'message' => 'Storefront client could not be resolved.'], 422);
        }

        try {
        $order = DB::connection('ecommerce')->transaction(function () use ($cart, $request, $subtotal, $shippingFee, $total, $clientId) {
        // Create Order
        $order = Order::create([
            'user_id' => Auth::guard('ecommerce')->id(),
            'status' => 'processing',
            'total' => $total,
            'shipping_fee' => $shippingFee,
            'payment_method' => $request->paymentMethod,
            'payment_status' => $request->paymentMethod === 'cod' ? 'unpaid' : 'paid',
            'shipping_address' => [
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'zip' => $request->zip,
            ],
            'tracking_number' => 'TF-' . strtoupper(\Illuminate\Support\Str::random(8)),
        ]);

        // Create Order Items
        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_type' => $item->product_type,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'configuration' => $item->configuration,
            ]);
        }

        app(ErpIntegrationService::class)->propagateEcommerceOrder($clientId, $order, $order->items);

        // Clear Cart only after every required ERP record has been created.
        $cart->items()->delete();

        return $order;
        });
        } catch (\RuntimeException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('ecommerce.checkout.success', $order->id)
        ]);
    }

    public function success($id)
    {
        $order = Order::with('items')->where('user_id', Auth::guard('ecommerce')->id())->findOrFail($id);
        return view('ecommerce::checkout-success', compact('order'));
    }
}
