<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Registration</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#1B365D] font-sans text-white">
    <div class="flex min-h-screen flex-col">
        <x-itsm-header
            :home-route="route('admin.itsm.registration')"
            active="registration"
            :nav-items="[
                ['label' => 'Registration', 'route' => route('admin.itsm.registration'), 'key' => 'registration'],
                ['label' => 'Client Management', 'route' => route('admin.itsm.clients'), 'key' => 'clients'],
                ['label' => 'Service Desk', 'route' => route('admin.itsm.service-desk'), 'key' => 'service-desk'],
            ]"
        />

        <main class="relative flex flex-1 items-center justify-center overflow-hidden px-6 py-12">
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[64rem] -translate-x-1/2 -translate-y-1/2 opacity-10 blur-sm">

            <section class="relative z-10 w-full max-w-3xl">
                <h1 class="mb-12 text-center text-5xl font-light">Register <span class="font-semibold italic">a new</span> company</h1>

                @if ($errors->any())
                    <div class="mb-6 rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.itsm.registration.store') }}" method="POST" class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
                    @csrf

                    <label class="block">
                        <span class="mb-2 block text-sm font-light text-white">Company Name</span>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Type here.." class="h-11 w-full rounded-sm border-0 bg-white px-4 text-sm text-slate-900 outline-none placeholder:italic placeholder:text-slate-400">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-light text-white">Industry</span>
                        <select name="industry" class="h-11 w-full rounded-sm border-0 bg-white px-4 text-sm text-slate-500 outline-none">
                            <option value="" disabled selected hidden>Please Select</option>
                            <option value="tech" @selected(old('industry') === 'tech')>Technology</option>
                            <option value="finance" @selected(old('industry') === 'finance')>Finance</option>
                            <option value="retail" @selected(old('industry') === 'retail')>Retail</option>
                            <option value="manufacturing" @selected(old('industry') === 'manufacturing')>Manufacturing</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-light text-white">Company E-mail</span>
                        <input type="email" name="company_email" value="{{ old('company_email') }}" placeholder="sample@company.com" class="h-11 w-full rounded-sm border-0 bg-white px-4 text-sm text-slate-900 outline-none placeholder:italic placeholder:text-slate-400">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-sm font-light text-white">Phone No.</span>
                        <input type="text" name="phone_no" value="{{ old('phone_no') }}" placeholder="Type here.." class="h-11 w-full rounded-sm border-0 bg-white px-4 text-sm text-slate-900 outline-none placeholder:italic placeholder:text-slate-400">
                    </label>

                    <label class="block md:col-span-2 md:max-w-[calc(50%-1rem)]">
                        <span class="mb-2 block text-sm font-light text-white">Admin Name</span>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Type here.." class="h-11 w-full rounded-sm border-0 bg-white px-4 text-sm text-slate-900 outline-none placeholder:italic placeholder:text-slate-400">
                    </label>

                    <div class="pt-20 text-center md:col-span-2">
                        <button type="submit" class="rounded-md bg-white px-10 py-3 text-2xl font-bold text-[#0B1E3D] shadow-lg transition hover:-translate-y-0.5 hover:bg-slate-100">Register</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
