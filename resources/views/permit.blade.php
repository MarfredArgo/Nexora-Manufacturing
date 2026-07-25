<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Compliance Tracking - Permits</title>
    <link class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition" rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-[#1B365D] font-sans text-white" x-data="{ openUploadModal: false }">
    <div class="flex min-h-screen flex-col">
        <x-itsm-header
            :home-route="route('client.itsm.employees')"
            active="compliance"
            :nav-items="[
                ['label' => 'Employee Management', 'route' => route('client.itsm.employees'), 'key' => 'employees'],
                ['label' => 'Service Desk', 'route' => route('client.itsm.service-desk'), 'key' => 'service-desk'],
                ['label' => 'Compliance Tracking', 'route' => route('client.itsm.compliance'), 'key' => 'compliance'],
                ['label' => 'Risk Management', 'route' => route('client.itsm.risk'), 'key' => 'risk'],
            ]"
        />

        <main class="relative flex-1 overflow-hidden px-8 py-6 xl:px-12">
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[72rem] -translate-x-1/2 -translate-y-1/2 opacity-5 blur-sm">

            <section class="relative z-10 mx-auto w-full max-w-[1760px] space-y-5">
                
                <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-6 text-slate-950 shadow-sm">
                    <h1 class="text-4xl font-bold tracking-tight">Compliance Tracking</h1>
                </div>

                <div class="flex flex-col min-h-[78vh] overflow-hidden rounded-[2rem] bg-[#C9D6E4] pb-10 shadow-2xl text-slate-900">
                    
                    <div class="flex w-full border-b border-slate-300/80 bg-white pt-4 text-sm font-semibold text-slate-500">
                        <a href="{{ route('client.itsm.compliance') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="clipboard-check" class="h-4.5 w-4.5"></i> Compliance Requirements
                        </a>
                        <a href="{{ route('client.itsm.audit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="shield-alert" class="h-4.5 w-4.5"></i> Audits & Inspections
                        </a>
                        <a href="{{ route('client.itsm.permit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-[#132B52] pb-3.5 text-[#132B52]">
                            <i data-lucide="file-badge" class="h-4.5 w-4.5"></i> Permits & Licenses
                        </a>
                        <a href="{{ route('client.itsm.risk.assessment') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="alert-triangle" class="h-4.5 w-4.5"></i> Risk Assessment
                        </a>
                        <a href="{{ route('client.itsm.document') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="folder" class="h-4.5 w-4.5"></i> Documents
                        </a>
                    </div>

                    <div class="px-10 py-6 space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Active Licenses</span>
                                <span class="block text-4xl font-extrabold text-[#132B52] my-1">{{ $activeCount }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">All inspections compliant</span>
                            </div>
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Recent Expiration</span>
                                <span class="block text-4xl font-extrabold text-[#132B52] my-1">{{ $expiredCount }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">No active breaches</span>
                            </div>
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Expiring Within 30 Days</span>
                                <span class="block text-4xl font-extrabold text-amber-600 my-1">{{ $expiringSoonCount }}</span>
                                <span class="text-[10px] font-semibold text-amber-600 font-medium">Action required soon</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-2">
                            <button @click="openUploadModal = true" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-blue-700 transition">
                                <span class="text-lg leading-none">+</span> Upload Permit
                            </button>
                            
                            <div class="flex items-center gap-5">
                                <form method="GET" action="{{ route('client.itsm.permit') }}" class="relative m-0 p-0">
                                    @if(request('status'))
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif
                                    <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                                        <i data-lucide="search" class="h-4 w-4"></i>
                                    </span>
                                    <input type="text" name="search" value="{{ $search }}" placeholder="Search" onchange="this.form.submit()" class="w-72 rounded-full border border-slate-300/60 bg-white/80 py-2 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </form>

                                <div class="relative" x-data="{ openFilter: false }">
                                    <button @click="openFilter = !openFilter" class="flex items-center gap-2 text-sm font-bold text-slate-900 hover:text-slate-700 transition">
                                        <i data-lucide="filter" class="h-4 w-4"></i>
                                        <span>{{ $currentStatus }}</span>
                                    </button>
                                    
                                    <div x-show="openFilter" @click.outside="openFilter = false" class="absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-20" x-cloak>
                                        <div class="py-1" role="none">
                                            <a href="{{ route('client.itsm.permit', ['status' => 'All', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium">All</a>
                                            <a href="{{ route('client.itsm.permit', ['status' => 'Active', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium">Active</a>
                                            <a href="{{ route('client.itsm.permit', ['status' => 'Expiring Soon', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium">Expiring Soon</a>
                                            <a href="{{ route('client.itsm.permit', ['status' => 'Expired', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium">Expired</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permits Grid Layout -->
                        @if($permits->isEmpty())
                            <!-- Clean Placeholder Empty State Viewport Wrapper -->
                            <div class="flex flex-col items-center justify-center py-16 px-4 border-2 border-dashed border-slate-400/40 rounded-2xl bg-[#E8EEF4]/40 text-center">
                                <div class="p-4 rounded-full bg-slate-300/50 mb-3 text-slate-600">
                                    <i data-lucide="file-text" class="h-10 w-10"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">No Permits Registered</h3>
                                <p class="text-xs text-slate-500 max-w-sm mt-1">There are currently no active permits available. Click on "+ Upload Permit" above to populate records.</p>
                            </div>
                        @else
                            <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                                @foreach ($permits as $permit)
                                    <article class="flex flex-col justify-between rounded-2xl border border-slate-200/50 bg-[#E8EEF4] p-6 shadow-md transition hover:shadow-lg">
                                        <div>
                                            <h2 class="text-xl font-bold leading-tight text-slate-950">{{ $permit['title'] }}</h2>
                                            <p class="text-xs font-semibold text-slate-500 mt-1">Issued by: {{ $permit['issuer'] }}</p>
                                        </div>

                                        <div class="mt-8 space-y-4">
                                            <div>
                                                <p class="text-[10px] font-semibold text-slate-400 mb-1.5">{{ $permit['expiry'] }}</p>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="h-2 w-2 rounded-full {{ $permit['status_color'] }}"></span>
                                                    <span class="text-xs font-semibold text-slate-700">{{ $permit['status'] }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-3 pt-2">
                                                <button class="rounded-md border border-slate-950 py-1.5 text-xs font-bold transition hover:bg-slate-950 hover:text-white">
                                                    View File
                                                </button>
                                                <button class="rounded-md border border-slate-950 py-1.5 text-xs font-bold transition hover:bg-slate-950 hover:text-white">
                                                    Renew
                                                </button>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Upload Permit Modal Container -->
    <div x-show="openUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" x-cloak>
        <div @click.outside="openUploadModal = false" class="w-full max-w-lg rounded-2xl bg-[#C9D6E4] p-6 shadow-2xl text-slate-900 border border-slate-300">
            <div class="flex items-center justify-between border-b border-slate-400/40 pb-3 mb-4">
                <h3 class="text-xl font-bold text-[#132B52]">Upload Compliance Permit</h3>
                <button @click="openUploadModal = false" class="text-slate-600 hover:text-slate-950 font-bold text-lg">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('client.itsm.permit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Permit Title</label>
                    <input type="text" name="title" required placeholder="e.g. Health Sanitation License" class="w-full rounded-lg border border-slate-300 bg-white/90 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-950" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Issuing Authority</label>
                    <input type="text" name="issuer" required placeholder="e.g. City Health Dept Office" class="w-full rounded-lg border border-slate-300 bg-white/90 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-950" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Expiration Date</label>
                        <input type="date" name="expiry_date" required class="w-full rounded-lg border border-slate-300 bg-white/90 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-950" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Initial Status</label>
                        <select name="status" class="w-full rounded-lg border border-slate-300 bg-white/90 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-950">
                            <option value="Active">Active</option>
                            <option value="Expiring Soon">Expiring Soon</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 border-t border-slate-400/40 pt-4 mt-6">
                    <button type="button" @click="openUploadModal = false" class="rounded-md border border-slate-950 px-4 py-2 text-xs font-bold transition hover:bg-slate-950 hover:text-white">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-md bg-[#1A73E8] px-4 py-2 text-xs font-bold text-white transition hover:bg-blue-700">
                        Submit Document
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>