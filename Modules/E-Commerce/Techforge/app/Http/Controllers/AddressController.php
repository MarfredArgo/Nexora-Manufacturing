<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'detailed_address' => 'required|string|max:255',
            'address_label' => 'nullable|in:home,work,other',
            'custom_label' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = Auth::guard('ecommerce')->user();

        // Check max 5 addresses to prevent spam
        if ($user->addresses()->count() >= 5) {
            if ($request->ajax()) return response()->json(['error' => 'You can only save up to 5 delivery addresses.'], 422);
            return back()->with('error', 'You can only save up to 5 delivery addresses.');
        }

        $isDefault = $request->has('is_default') ? true : false;
        
        // If it's their first address, force it as default
        if ($user->addresses()->count() === 0) {
            $isDefault = true;
        }

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'full_name' => $validated['full_name'],
            'phone_number' => $validated['phone_number'],
            'region' => $validated['region'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'barangay' => $validated['barangay'],
            'postal_code' => $validated['postal_code'],
            'detailed_address' => $validated['detailed_address'],
            'label' => $validated['address_label'] ?? $request->input('label', 'home'),
            'custom_label' => $validated['custom_label'] ?? null,
            'is_default' => $isDefault,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        if ($request->ajax()) return response()->json(['success' => 'Address added successfully!']);
        return back()->with('success', 'Address added successfully!');
    }

    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== Auth::guard('ecommerce')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'detailed_address' => 'required|string|max:255',
            'address_label' => 'nullable|in:home,work,other',
            'custom_label' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $isDefault = $request->has('is_default') ? true : false;
        
        // If they checked set as default
        if ($isDefault) {
            Auth::guard('ecommerce')->user()->addresses()->update(['is_default' => false]);
        } else {
            // Cannot unset default if it's the only one, or if they are unsetting the current default (must have at least one default)
            if ($address->is_default) {
                $isDefault = true; // Force keep as default
            }
        }

        $address->update([
            'full_name' => $validated['full_name'],
            'phone_number' => $validated['phone_number'],
            'region' => $validated['region'],
            'province' => $validated['province'],
            'city' => $validated['city'],
            'barangay' => $validated['barangay'],
            'postal_code' => $validated['postal_code'],
            'detailed_address' => $validated['detailed_address'],
            'label' => $validated['address_label'] ?? $request->input('label', 'home'),
            'custom_label' => $validated['custom_label'] ?? null,
            'is_default' => $isDefault,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        if ($request->ajax()) return response()->json(['success' => 'Address updated successfully!']);
        return back()->with('success', 'Address updated successfully!');
    }

    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== Auth::guard('ecommerce')->id()) {
            abort(403);
        }

        $address->delete();

        // If we deleted the default, set another one to default if exists
        if ($address->is_default) {
            $newDefault = Auth::guard('ecommerce')->user()->addresses()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        if (request()->ajax()) return response()->json(['success' => 'Address removed.']);
        return back()->with('success', 'Address removed.');
    }

    public function setDefault($id)
    {
        $address = Address::findOrFail($id);
        if ($address->user_id !== Auth::guard('ecommerce')->id()) {
            abort(403);
        }

        Auth::guard('ecommerce')->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        if (request()->ajax()) return response()->json(['success' => 'Default address updated.']);
        return back()->with('success', 'Default address updated.');
    }
}
