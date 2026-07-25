<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Ecommerce\Models\Cart;
use Modules\Ecommerce\Models\StorefrontListing;

class StorefrontListingController extends Controller
{
    public function show(string $store, string $listing)
    {
        $listing = $this->activeListing($listing);

        return view('ecommerce::listing-show', compact('listing'));
    }

    public function addToCart(string $store, string $listing): RedirectResponse
    {
        $listing = $this->activeListing($listing);

        if ($listing->available_quantity < 1) {
            return back()->with('error', 'This product is currently out of stock.');
        }

        $productId = 'listing-'.$listing->id;
        $payload = [
            'product_id' => $productId,
            'product_type' => 'bom_listing',
            'name' => $listing->name,
            'quantity' => 1,
            'price' => $listing->price,
            'image_url' => $listing->image_url,
            'configuration' => json_encode(['bom_id' => $listing->bom_id, 'listing_id' => $listing->id]),
        ];

        if (Auth::guard('ecommerce')->check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::guard('ecommerce')->id()]);
            $existing = $cart->items()->where('product_id', $productId)->first();
            $quantity = ($existing?->quantity ?? 0) + 1;

            if ($quantity > $listing->available_quantity) {
                return back()->with('error', 'Only '.$listing->available_quantity.' unit(s) are currently available.');
            }

            $existing ? $existing->update(['quantity' => $quantity]) : $cart->items()->create($payload);
        } else {
            $cart = session('cart', []);
            $quantity = ($cart[$productId]['quantity'] ?? 0) + 1;

            if ($quantity > $listing->available_quantity) {
                return back()->with('error', 'Only '.$listing->available_quantity.' unit(s) are currently available.');
            }

            $cart[$productId] = [
                'id' => $productId,
                'product_type' => $payload['product_type'],
                'name' => $payload['name'],
                'quantity' => $quantity,
                'price' => $payload['price'],
                'image_url' => $payload['image_url'],
                'configuration' => $payload['configuration'],
            ];
            session(['cart' => $cart]);
        }

        return redirect()->route('ecommerce.cart')->with('success', 'Product added to cart.');
    }

    /**
     * Resolve the listing only after the storefront client middleware has set
     * the active client context. Implicit route binding runs too early and
     * otherwise causes valid tenant listings to appear missing.
     */
    private function activeListing(string $listingId): StorefrontListing
    {
        $listing = StorefrontListing::query()->whereKey($listingId)->firstOrFail();

        abort_unless($listing->status === 'active', 404);

        return $listing;
    }
}
