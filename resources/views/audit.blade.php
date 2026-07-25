<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Compliance Tracking - Audits</title>
    <link class="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-[#1B365D] font-sans text-white">
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

        <!-- Main widescreen layout container -->
        <main class="relative flex-1 overflow-hidden px-8 py-6 xl:px-12">
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[72rem] -translate-x-1/2 -translate-y-1/2 opacity-5 blur-sm">

            <section class="relative z-10 mx-auto w-full max-w-[1760px] space-y-5">
                
                <!-- Compliance Tracking Header (Sleek, Wide Banner) -->
                <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-6 text-slate-950 shadow-sm">
                    <h1 class="text-4xl font-bold tracking-tight">Compliance Tracking</h1>
                </div>

                <!-- Main Content Workspace Console -->
                <div class="flex flex-col min-h-[78vh] overflow-hidden rounded-[2rem] bg-[#C9D6E4] pb-10 shadow-2xl text-slate-900">
                    
                    <!-- Subtabs Bar (Navigation links) -->
                    <div class="flex w-full border-b border-slate-300/80 bg-white pt-4 text-sm font-semibold text-slate-500">
                        <a href="{{ route('client.itsm.compliance') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="clipboard-check" class="h-4.5 w-4.5"></i> Compliance Requirements
                        </a>
                        <a href="{{ route('client.itsm.audit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-[#132B52] pb-3.5 text-[#132B52]">
                            <i data-lucide="shield-alert" class="h-4.5 w-4.5"></i> Audits & Inspections
                        </a>
                        <a href="{{ route('client.itsm.permit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="file-badge" class="h-4.5 w-4.5"></i> Permits & Licenses
                        </a>
                        <a href="{{ route('client.itsm.risk.assessment') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="alert-triangle" class="h-4.5 w-4.5"></i> Risk Assessment
                        </a>
                        <a href="{{ route('client.itsm.document') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="folder" class="h-4.5 w-4.5"></i> Documents
                        </a>
                    </div>

                    <!-- Inner Console Section -->
                    <div class="px-10 py-6 space-y-6">

                        <!-- Metrics Summary Strip -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Metric 1 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Upcoming Audits</span>
                                <span class="block text-4xl font-extrabold text-[#132B52] my-1">{{ $upcomingCount }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">Awaiting execution</span>
                            </div>
                            <!-- Metric 2 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Total Inspections</span>
                                <span class="block text-4xl font-extrabold text-[#132B52] my-1">{{ $totalInspections }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">Historical database logs</span>
                            </div>
                            <!-- Metric 3 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Active Failure Alerts</span>
                                <span class="block text-4xl font-extrabold text-red-600 my-1">{{ $failedCount }}</span>
                                <span class="text-[10px] font-semibold text-red-500 font-medium">Action required</span>
                            </div>
                        </div>

                        <!-- Main Table Container -->
                        <div class="overflow-hidden rounded-2xl border border-slate-300/40 bg-white shadow-md">
                            
                            <!-- Control Panel Strip Inside Content Area -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
                                <!-- Action Trigger -->
                                <button onclick="toggleScheduleModal(true)" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                                    <span class="text-sm leading-none">+</span> Schedule Audit
                                </button>
                                
                                <div class="flex items-center gap-5">
                                    <!-- Search Input Form -->
                                    <form action="{{ route('client.itsm.audit') }}" method="GET" class="relative">
                                        @if(request('status'))
                                            <input type="hidden" name="status" value="{{ request('status') }}">
                                        @endif
                                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                            <i data-lucide="search" class="h-4 w-4"></i>
                                        </span>
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search" class="w-64 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-9 pr-4 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                    </form>

                                    <!-- Filter Tool with Dropdown Overlay -->
                                    <div class="relative inline-block text-left">
                                        <button onclick="toggleFilterDropdown()" class="flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-600 transition focus:outline-none">
                                            <i data-lucide="filter" class="h-4 w-4"></i>
                                            <span>{{ $currentStatus }}</span>
                                        </button>
                                        
                                        <!-- Custom Filter Dropdown Overlay -->
                                        <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-40 rounded-xl bg-white shadow-xl border border-slate-100 z-50 overflow-hidden">
                                            <div class="py-1 text-slate-700 text-xs">
                                                <a href="{{ route('client.itsm.audit', ['status' => 'All', 'search' => request('search')]) }}" class="block px-4 py-2.5 hover:bg-slate-50 transition {{ $currentStatus === 'All' ? 'bg-slate-100 font-bold' : '' }}">All</a>
                                                <a href="{{ route('client.itsm.audit', ['status' => 'Scheduled', 'search' => request('search')]) }}" class="block px-4 py-2.5 hover:bg-slate-50 transition {{ $currentStatus === 'Scheduled' ? 'bg-slate-100 font-bold' : '' }}">Scheduled</a>
                                                <a href="{{ route('client.itsm.audit', ['status' => 'Completed', 'search' => request('search')]) }}" class="block px-4 py-2.5 hover:bg-slate-50 transition {{ $currentStatus === 'Completed' ? 'bg-slate-100 font-bold' : '' }}">Completed</a>
                                                <a href="{{ route('client.itsm.audit', ['status' => 'Failed', 'search' => request('search')]) }}" class="block px-4 py-2.5 hover:bg-slate-50 transition {{ $currentStatus === 'Failed' ? 'bg-slate-100 font-bold' : '' }}">Failed</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- List / Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                            <th class="px-6 py-4 font-semibold text-center">Audit ID & Title</th>
                                            <th class="px-6 py-4 font-semibold">Target Scope</th>
                                            <th class="px-6 py-4 font-semibold">Auditor</th>
                                            <th class="px-6 py-4 font-semibold">Date</th>
                                            <th class="px-6 py-4 font-semibold">Score/Rating</th>
                                            <th class="px-6 py-4 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @forelse($audits as $audit)
                                            <tr class="bg-white hover:bg-slate-50/50 transition">
                                                <td class="px-6 py-4 font-bold text-slate-900 text-center">{{ $audit['id'] }}</td>
                                                <td class="px-6 py-4 text-slate-600">{{ $audit['scope'] }}</td>
                                                <td class="px-6 py-4 text-slate-600">{{ $audit['auditor'] }}</td>
                                                <td class="px-6 py-4 text-slate-600">{{ $audit['date'] }}</td>
                                                <td class="px-6 py-4 {{ str_contains($audit['score'], 'Fail') ? 'font-semibold text-red-600' : ($audit['score'] !== '—' ? 'font-semibold text-slate-800' : 'text-slate-600') }}">{{ $audit['score'] }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $audit['status_class'] }}">
                                                        {{ $audit['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="bg-white">
                                                <td colspan="6" class="px-6 py-10 text-center text-slate-400 font-medium">
                                                    No audits matching the filters were found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>

    <!-- Dynamic Schedule Audit Modal Overlay -->
    <div id="scheduleModal" class="hidden fixed inset-0 bg-slate-950/40 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl text-slate-900 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-[#132B52]">Schedule New Audit</h3>
                <button onclick="toggleScheduleModal(false)" class="text-slate-400 hover:text-slate-600 transition">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            
            <form action="{{ route('client.itsm.audit') }}" method="POST" class="space-y-4 text-xs font-semibold text-slate-700">
                @csrf
                <div class="space-y-1.5">
                    <label class="block">Audit Title</label>
                    <input type="text" name="title" required placeholder="e.g. ISO 27001 Readiness" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div class="space-y-1.5">
                    <label class="block">Target Scope</label>
                    <input type="text" name="scope" required placeholder="e.g. IT & Infrastructure" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div class="space-y-1.5">
                    <label class="block">Auditor Name</label>
                    <input type="text" name="auditor" required placeholder="e.g. Sarah Jenkins" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div class="space-y-1.5">
                    <label class="block">Target Date</label>
                    <input type="date" name="date" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="toggleScheduleModal(false)" class="rounded-full bg-slate-100 px-5 py-2 hover:bg-slate-200 transition text-slate-700 font-bold">Cancel</button>
                    <button type="submit" class="rounded-full bg-[#1A73E8] px-5 py-2 text-white shadow-md hover:bg-blue-700 transition font-bold">Schedule</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        lucide.createIcons();

        // Control functionality for Filter Dropdown Menu
        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside the boundary box
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('filterDropdown');
            if (!e.target.closest('#filterDropdown') && !e.target.closest('button[onclick="toggleFilterDropdown()"]')) {
                dropdown.classList.add('hidden');
            }
        });

        // Control functionality for Modal popup layer
        function toggleScheduleModal(show) {
            const modal = document.getElementById('scheduleModal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>