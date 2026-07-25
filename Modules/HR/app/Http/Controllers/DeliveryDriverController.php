<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\HR\Models\DeliveryDriver;
use Modules\HR\Models\Employee;

class DeliveryDriverController extends Controller
{
    public function index()
    {
        $drivers = DeliveryDriver::with('employee')
            ->orderBy('availability')
            ->orderBy('created_at')
            ->get();

        $employeeIds = $drivers->pluck('employee_id');
        $employees = Employee::query()
            ->whereNotIn('id', $employeeIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name', 'department', 'position']);

        return view('drivers.index', compact('drivers', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer'],
            'courier_provider' => ['required', 'string', 'max:100'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'plate_number' => ['nullable', 'string', 'max:50'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        DeliveryDriver::create([
            'employee_id' => $employee->id,
            'courier_provider' => DeliveryDriver::normalizeCourier($data['courier_provider']),
            'vehicle_type' => $data['vehicle_type'] ?: null,
            'plate_number' => $data['plate_number'] ?: null,
            'is_active' => true,
            'availability' => DeliveryDriver::STATUS_AVAILABLE,
        ]);

        return back()->with('success', "{$employee->first_name} {$employee->last_name} is now available for delivery assignment.");
    }

    public function update(Request $request, DeliveryDriver $driver): RedirectResponse
    {
        $data = $request->validate([
            'courier_provider' => ['required', 'string', 'max:100'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
        ]);

        $driver->update([
            'courier_provider' => DeliveryDriver::normalizeCourier($data['courier_provider']),
            'vehicle_type' => $data['vehicle_type'] ?: null,
            'plate_number' => $data['plate_number'] ?: null,
            'is_active' => (bool) $data['is_active'],
        ]);

        return back()->with('success', 'Driver profile updated.');
    }
}
