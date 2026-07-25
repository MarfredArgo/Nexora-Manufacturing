<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;

use Modules\Ecommerce\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function storeCard(Request $request)
    {
        $validated = $request->validate([
            'card_type' => 'required|in:credit_card,debit_card',
            'card_number' => 'required|string',
            'expiry_date' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $value)) {
                    $fail('The expiry date must be in MM/YY format.');
                    return;
                }
                $parts = explode('/', $value);
                $month = (int)$parts[0];
                $year = (int)$parts[1];
                $currentYear = (int)date('y');
                $currentMonth = (int)date('m');

                if ($year < $currentYear || ($year === $currentYear && $month < $currentMonth)) {
                    $fail('The expiry date cannot be in the past.');
                }
                if ($year > $currentYear + 20) {
                    $fail('The expiry date is invalid.');
                }
            }],
            'cvv' => 'required|string',
            'cardholder_name' => 'required|string|max:255',
        ]);

        $user = Auth::guard('ecommerce')->user();
        if (!$user) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthenticated. Please log in.'], 401);
            return back()->with('error', 'Unauthenticated. Please log in.');
        }
        
        \Log::info('storeCard DEBUG', [
            'has_session' => $request->hasSession(),
            'session_id' => $request->session()->getId(),
            'ecommerce_login' => $request->session()->get('login_ecommerce_59ba36addc2b2f9401580f014c7f58ea4e30989d'),
            'user' => $user ? $user->id : null,
            'client_id' => app(\Modules\Ecommerce\Support\EcommerceClientContext::class)->clientId(),
            'cookies' => $request->cookies->all()
        ]);

        $cleanCardNumber = str_replace(' ', '', $validated['card_number']);

        // Determine Provider (Dynamic logic)
        $provider = 'Visa'; // Default
        if (str_starts_with($cleanCardNumber, '4')) {
            $provider = 'Visa';
            if (str_starts_with($cleanCardNumber, '4700')) $provider = 'GCash Card';
        } elseif (preg_match('/^5[1-5]/', $cleanCardNumber) || preg_match('/^2[2-7]/', $cleanCardNumber)) {
            $provider = 'Mastercard';
            if (str_starts_with($cleanCardNumber, '5200')) $provider = 'Maya Card';
        }

        // Mask card number
        $mask = substr($cleanCardNumber, -4);
        if (strlen($mask) < 4) $mask = '0000';

        // Check for max 10 payment methods
        if ($user->paymentMethods()->count() >= 10) {
            if ($request->ajax()) return response()->json(['error' => 'You can only have a maximum of 10 payment methods linked.'], 422);
            return back()->with('error', 'You can only have a maximum of 10 payment methods linked.');
        }

        // Check for duplicates across the database
        $exists = PaymentMethod::where('type', $validated['card_type'])
            ->where('provider', $provider)
            ->where('account_number_mask', $mask)
            ->exists();

        if ($exists) {
            if ($request->ajax()) return response()->json(['error' => 'This card is already linked to an account.'], 422);
            return back()->with('error', 'This card is already linked to an account.');
        }

        // Check if first payment method, if so make it default
        $isDefault = $user->paymentMethods()->count() === 0;

        $user->paymentMethods()->create([
            'type' => $validated['card_type'],
            'provider' => $provider,
            'account_name' => $validated['cardholder_name'],
            'account_number_mask' => $mask,
            'expiry_date' => $validated['expiry_date'],
            'is_default' => $isDefault,
        ]);

        if ($request->ajax()) return response()->json(['success' => 'Card added successfully!']);
        return back()->with('success', 'Card added successfully!');
    }

    public function storeBank(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string',
        ]);

        $user = Auth::guard('ecommerce')->user();
        if (!$user) {
            if ($request->ajax()) return response()->json(['error' => 'Unauthenticated. Please log in.'], 401);
            return back()->with('error', 'Unauthenticated. Please log in.');
        }

        $cleanAccountNumber = str_replace(' ', '', $validated['account_number']);
        $mask = substr($cleanAccountNumber, -4);
        if (strlen($mask) < 4) $mask = '0000';

        // Check for max 10 payment methods
        if ($user->paymentMethods()->count() >= 10) {
            if ($request->ajax()) return response()->json(['error' => 'You can only have a maximum of 10 payment methods linked.'], 422);
            return back()->with('error', 'You can only have a maximum of 10 payment methods linked.');
        }

        // Check for duplicates across the database
        $exists = PaymentMethod::where('type', 'bank_account')
            ->where('provider', $validated['provider'])
            ->where('account_number_mask', $mask)
            ->exists();

        if ($exists) {
            if ($request->ajax()) return response()->json(['error' => 'This bank account is already linked to an account.'], 422);
            return back()->with('error', 'This bank account is already linked to an account.');
        }

        $isDefault = $user->paymentMethods()->count() === 0;

        $user->paymentMethods()->create([
            'type' => 'bank_account',
            'provider' => $validated['provider'],
            'account_name' => $validated['account_name'],
            'account_number_mask' => $mask,
            'is_default' => $isDefault,
        ]);

        if ($request->ajax()) return response()->json(['success' => 'Bank account added successfully!']);
        return back()->with('success', 'Bank account added successfully!');
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $user = Auth::guard('ecommerce')->user();
        if (!$user || $paymentMethod->user_id !== $user->id) {
            abort(403);
        }

        $paymentMethod->delete();

        if (request()->ajax()) return response()->json(['success' => 'Payment method removed.']);
        return back()->with('success', 'Payment method removed.');
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $user = Auth::guard('ecommerce')->user();
        if (!$user || $paymentMethod->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'cardholder_name' => 'required|string|max:255',
            'expiry_date' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $value)) {
                    $fail('The expiry date must be in MM/YY format.');
                    return;
                }
                $parts = explode('/', $value);
                $month = (int)$parts[0];
                $year = (int)$parts[1];
                $currentYear = (int)date('y');
                $currentMonth = (int)date('m');

                if ($year < $currentYear || ($year === $currentYear && $month < $currentMonth)) {
                    $fail('The expiry date cannot be in the past.');
                }
                if ($year > $currentYear + 20) {
                    $fail('The expiry date is invalid.');
                }
            }],
        ]);

        $paymentMethod->update([
            'account_name' => $validated['cardholder_name'],
            'expiry_date' => $validated['expiry_date'],
        ]);

        if ($request->ajax()) return response()->json(['success' => 'Card updated successfully!']);
        return back()->with('success', 'Card updated successfully!');
    }

    public function setDefault($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $user = Auth::guard('ecommerce')->user();
        if (!$user || $paymentMethod->user_id !== $user->id) {
            abort(403);
        }

        Auth::guard('ecommerce')->user()->paymentMethods()->update(['is_default' => false]);
        $paymentMethod->update(['is_default' => true]);

        return back()->with('success', 'Default payment method updated.');
    }
}
