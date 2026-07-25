<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\Cart;
use Modules\Ecommerce\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
            'quantity' => 'integer|min:1',
            'image_url' => 'nullable|string',
            'product_type' => 'nullable|string',
            'configuration' => 'nullable|string',
        ]);

        $productId = (string) $validated['product_id'];
        $quantity = $request->input('quantity', 1);
        $name = $request->input('name', 'Product');
        $price = $request->input('price', 0);
        $imageUrl = $request->input('image_url', '');
        $productType = $request->input('product_type', 'generic');
        $configuration = $request->input('configuration');

        // Rely on the frontend payload for name and price since we have multiple distinct product tables

        if (Auth::guard('ecommerce')->check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::guard('ecommerce')->id()]);
            $item = $cart->items()->where('product_id', $productId)->first();

            if ($item) {
                $item->quantity += $quantity;
                $item->save();
            } else {
                $cart->items()->create([
                    'product_id' => $productId,
                    'product_type' => $productType,
                    'name' => $name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'image_url' => $imageUrl,
                    'configuration' => $configuration
                ]);
            }
            
            $cartItems = $this->formatDbCartItems($cart);
            $totalItems = collect($cartItems)->sum('quantity');

        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantity;
            } else {
                $cart[$productId] = [
                    'id' => $productId,
                    'product_type' => $productType,
                    'name' => $name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'image_url' => $imageUrl,
                    'configuration' => $configuration,
                ];
            }

            session()->put('cart', $cart);
            $cartItems = array_values($cart);
            $totalItems = collect($cart)->sum('quantity');
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $totalItems,
            'cart_items' => $cartItems
        ]);
    }
    
    public function getCount()
    {
        if (Auth::guard('ecommerce')->check()) {
            $cart = Cart::where('user_id', Auth::guard('ecommerce')->id())->first();
            $totalItems = $cart ? $cart->items()->sum('quantity') : 0;
            $cartItems = $cart ? $this->formatDbCartItems($cart) : [];
        } else {
            $cart = session()->get('cart', []);
            $totalItems = collect($cart)->sum('quantity');
            $cartItems = array_values($cart);
        }

        return response()->json([
            'cart_count' => $totalItems,
            'cart_items' => $cartItems
        ]);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        if (Auth::guard('ecommerce')->check()) {
            $cart = Cart::where('user_id', Auth::guard('ecommerce')->id())->first();
            if ($cart) {
                $item = $cart->items()->where('product_id', $productId)->first();
                if ($item) {
                    $item->quantity = $quantity;
                    $item->save();
                }
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }

        return response()->json(['success' => true]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
        ]);

        $productId = $request->input('product_id');

        if (Auth::guard('ecommerce')->check()) {
            $cart = Cart::where('user_id', Auth::guard('ecommerce')->id())->first();
            if ($cart) {
                $cart->items()->where('product_id', $productId)->delete();
            }
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json(['success' => true]);
    }

    public function index()
    {
        if (Auth::guard('ecommerce')->check()) {
            $cartModel = Cart::with('items')->firstOrCreate(['user_id' => Auth::guard('ecommerce')->id()]);
            $cart = $cartModel->items->map(function($item) {
                return [
                    'id' => $item->product_id,
                    'product_type' => $item->product_type ?? 'generic',
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'image_url' => $item->image_url,
                    'configuration' => $item->configuration,
                ];
            })->toArray();
        } else {
            $cart = array_values(session()->get('cart', []));
        }
        
        $subtotal = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        $freeShippingThreshold = 100000;
        $shipping = ($subtotal >= $freeShippingThreshold || count($cart) === 0) ? 0 : 500;
        $discount = 0; 
        $total = $subtotal + $shipping - $discount;

        // Recommendations
        $recommendations = collect();
        $models = [
            \Modules\Ecommerce\Models\AccessoryKeyboard::class,
            \Modules\Ecommerce\Models\AccessoryHeadset::class,
            \Modules\Ecommerce\Models\AccessoryMouse::class,
            \Modules\Ecommerce\Models\AccessoryMousePad::class,
            \Modules\Ecommerce\Models\AccessoryMonitor::class,
        ];
        foreach ($models as $model) {
            try {
                $items = $model::inRandomOrder()->limit(2)->get();
                $recommendations = $recommendations->merge($items);
            } catch (\Exception $e) {}
        }
        $recommendations = $recommendations->shuffle()->take(8);

        return view('ecommerce::cart', compact('cart', 'subtotal', 'shipping', 'discount', 'total', 'freeShippingThreshold', 'recommendations'));
    }

    private function formatDbCartItems($cart)
    {
        return $cart->items()->get()->map(function($item) {
            return [
                'id' => $item->product_id,
                'product_type' => $item->product_type ?? 'generic',
                'name' => $item->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'image_url' => $item->image_url,
                'configuration' => $item->configuration,
            ];
        })->values()->toArray();
    }
}

