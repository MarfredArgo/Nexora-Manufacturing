<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Ecommerce\Models\User;

class AuthController extends Controller
{
    protected function mergeGuestCartToDb($user)
    {
        $sessionCart = session()->get('cart', []);
        if (!empty($sessionCart)) {
            $dbCart = \Modules\Ecommerce\Models\Cart::firstOrCreate(['user_id' => $user->id]);
            foreach ($sessionCart as $item) {
                $dbItem = $dbCart->items()->where('product_id', $item['id'])->first();
                if ($dbItem) {
                    $dbItem->quantity += $item['quantity'];
                    $dbItem->save();
                } else {
                    $dbCart->items()->create([
                        'product_id' => $item['id'],
                        'product_type' => $item['product_type'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'image_url' => $item['image_url'],
                        'configuration' => $item['configuration'] ?? null,
                    ]);
                }
            }
            session()->forget('cart');
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::guard('ecommerce')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            $this->mergeGuestCartToDb(Auth::guard('ecommerce')->user());

            if (session()->has('redirect_after_auth')) {
                return redirect(session()->pull('redirect_after_auth'));
            }
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:ecommerce.users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('ecommerce')->login($user, $request->has('remember'));
        $this->mergeGuestCartToDb($user);

        if (session()->has('redirect_after_auth')) {
            return redirect(session()->pull('redirect_after_auth'))->with('success', 'Account created successfully!');
        }

        return redirect()->intended(route('ecommerce.home'))
            ->with('success', 'Account created successfully. You are now signed in.');
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('ecommerce')->user();
        $dbCart = \Modules\Ecommerce\Models\Cart::with('items')->where('user_id', $user->id)->first();

        Auth::guard('ecommerce')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($dbCart && $dbCart->items->count() > 0) {
            $sessionCart = [];
            foreach ($dbCart->items as $item) {
                $sessionCart[$item->product_id] = [
                    'id' => $item->product_id,
                    'product_type' => $item->product_type,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'image_url' => $item->image_url,
                    'configuration' => $item->configuration,
                ];
            }
            $request->session()->put('cart', $sessionCart);
        }

        return redirect('/');
    }
}
