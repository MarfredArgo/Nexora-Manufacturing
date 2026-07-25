<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Drivers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#18386d] font-sans text-white">
    @include('partials.navbar')

    <main class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.2em] text-[#66A6FF]">Human Resources</p>
                <h1 class="mt-2 text-3xl font-bold">Delivery Drivers</h1>
                <p class="mt-2 max-w-2xl text-sm text-[#C9DAF8]">Assign an existing employee to a courier and vehicle. Order Fulfillment only reads these HR profiles; it no longer maintains a separate driver list.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-400/30 bg-emerald-950/40 px-4 py-3 text-emerald-200">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-400/30 bg-red-950/40 px-4 py-3 text-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="mb-8 rounded-2xl border border-white/10 bg-[#132B52] p-6 shadow-xl">
            <h2 class="text-lg font-bold">Add an employee as a driver</h2>
            @if ($employees->isEmpty())
                <p class="mt-3 text-sm text-[#C9DAF8]">All employees are already assigned as drivers, or there are no active employees to add.</p>
            @else
                <form action="{{ route('hr.drivers.store') }}" method="POST" class="mt-5 grid gap-4 md:grid-cols-4">
                    @csrf
                    <label class="block text-sm font-medium text-[#C9DAF8] md:col-span-2">
                        Employee
                        <select name="employee_id" required class="mt-1 w-full rounded-lg border border-white/15 bg-[#0B1E3D] px-3 py-2.5 text-white">
                            <option value="">Select an employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} — {{ $employee->employee_id }}{{ $employee->position ? " ({$employee->position})" : '' }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-[#C9DAF8]">
                        Courier
                        <select name="courier_provider" required class="mt-1 w-full rounded-lg border border-white/15 bg-[#0B1E3D] px-3 py-2.5 text-white">
                            <option value="JNT">J&amp;T Express</option>
                            <option value="FLASH">Flash Express</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-[#C9DAF8]">
                        Vehicle type
                        <input name="vehicle_type" placeholder="Motorcycle" class="mt-1 w-full rounded-lg border border-white/15 bg-[#0B1E3D] px-3 py-2.5 text-white placeholder:text-slate-400">
                    </label>
                    <label class="block text-sm font-medium text-[#C9DAF8] md:col-span-2">
                        Plate number
                        <input name="plate_number" placeholder="ABC 1234" class="mt-1 w-full rounded-lg border border-white/15 bg-[#0B1E3D] px-3 py-2.5 text-white placeholder:text-slate-400">
                    </label>
                    <div class="flex items-end md:col-span-2">
                        <button type="submit" class="w-full rounded-lg bg-[#2D7EFF] px-4 py-2.5 font-semibold text-white hover:bg-[#1d68e8]">Add delivery driver</button>
                    </div>
                </form>
            @endif
        </section>

        <section class="overflow-hidden rounded-2xl border border-white/10 bg-[#132B52] shadow-xl">
            <div class="border-b border-white/10 px-6 py-5"><h2 class="text-lg font-bold">Driver directory</h2></div>
            @if ($drivers->isEmpty())
                <p class="px-6 py-10 text-center text-[#C9DAF8]">No driver profiles yet. Add an existing employee above.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] text-left text-sm">
                        <thead class="bg-[#0B1E3D] text-xs uppercase tracking-wider text-[#93ABD3]"><tr><th class="px-6 py-4">Employee</th><th class="px-6 py-4">Courier</th><th class="px-6 py-4">Vehicle</th><th class="px-6 py-4">Availability</th><th class="px-6 py-4">Active</th><th class="px-6 py-4"></th></tr></thead>
                        <tbody class="divide-y divide-white/10">
                        @foreach ($drivers as $driver)
                            <tr>
                                <td class="px-6 py-4 font-semibold">{{ $driver->employee->first_name }} {{ $driver->employee->last_name }}<span class="mt-1 block text-xs font-normal text-[#93ABD3]">{{ $driver->employee->employee_id }}</span></td>
                                <td class="px-6 py-4">{{ $driver->courier_provider }}</td>
                                <td class="px-6 py-4">{{ $driver->vehicle_type ?: '—' }}<span class="mt-1 block text-xs text-[#93ABD3]">{{ $driver->plate_number ?: 'No plate recorded' }}</span></td>
                                <td class="px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $driver->availability === 'AVAILABLE' ? 'bg-emerald-400/15 text-emerald-300' : 'bg-amber-400/15 text-amber-200' }}">{{ str_replace('_', ' ', $driver->availability) }}</span></td>
                                <td class="px-6 py-4">{{ $driver->is_active ? 'Yes' : 'No' }}</td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('hr.drivers.update', $driver) }}" method="POST" class="flex gap-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="courier_provider" value="{{ $driver->courier_provider }}">
                                        <input type="hidden" name="vehicle_type" value="{{ $driver->vehicle_type }}">
                                        <input type="hidden" name="plate_number" value="{{ $driver->plate_number }}">
                                        <input type="hidden" name="is_active" value="{{ $driver->is_active ? 0 : 1 }}">
                                        <button class="rounded-lg border border-white/15 px-3 py-1.5 text-xs hover:bg-white/10">{{ $driver->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
