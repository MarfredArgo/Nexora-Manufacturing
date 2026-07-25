<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Risk Management - Incident Report</title>
    <link class="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-screen overflow-hidden bg-[#1B365D] font-sans text-white">
    <div class="flex h-full flex-col">
        <!-- Main Application Header -->
        <x-itsm-header
            :home-route="route('client.itsm.employees')"
            active="risk"
            :nav-items="[
                ['label' => 'User Management', 'route' => route('client.itsm.employees'), 'key' => 'employees'],
                ['label' => 'Service Desk', 'route' => route('client.itsm.service-desk'), 'key' => 'service-desk'],
                ['label' => 'Compliance Tracking', 'route' => route('client.itsm.compliance'), 'key' => 'compliance'],
                ['label' => 'Risk Management', 'route' => route('client.itsm.risk'), 'key' => 'risk'],
            ]"
        />

        <!-- Main Workspace Container -->
        <main class="relative flex-1 overflow-hidden px-8 py-6 xl:px-12">
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[72rem] -translate-x-1/2 -translate-y-1/2 opacity-5 blur-sm">

            <div class="relative z-10 mx-auto flex w-full max-w-[1760px] gap-6 items-stretch h-[calc(100vh-120px)]">
                
                <!-- Left Sidebar Navigation Panel -->
                <aside class="w-64 shrink-0 rounded-[2rem] bg-white p-6 shadow-2xl text-slate-900 flex flex-col justify-between h-full">
                    <div class="flex flex-col gap-2 font-semibold text-sm text-slate-600">
                        <a href="{{ route('client.itsm.risk') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
                            <i data-lucide="file-text" class="h-4.5 w-4.5"></i> Risk Register
                        </a>
                        <a href="{{ route('client.itsm.risk.mitigation') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
                            <i data-lucide="list-checks" class="h-4.5 w-4.5"></i> Mitigation Plans
                        </a>
                        <a href="{{ route('client.itsm.risk.incident') }}" class="flex items-center gap-3 rounded-xl bg-slate-100 px-4 py-3 text-[#132B52] font-bold transition">
                            <i data-lucide="alert-circle" class="h-4.5 w-4.5"></i> Incident Report
                        </a>
                        <a href="{{ route('client.itsm.risk.analytics') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
                            <i data-lucide="bar-chart-3" class="h-4.5 w-4.5"></i> Risk Analytics
                        </a>
                    </div>
                </aside>

                <!-- Right Side Console Viewspace -->
                <section class="flex-1 flex flex-col gap-5 h-full overflow-hidden">
                    
                    <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-5 text-slate-950 shadow-sm shrink-0 flex justify-between items-center">
                        <h1 class="text-4xl font-bold tracking-tight">Risk Management</h1>
                        
                        <div class="flex items-center gap-2">
                            @if(session('success'))
                                <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs px-4 py-2 rounded-xl font-semibold shadow-sm">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="bg-rose-100 border border-rose-300 text-rose-800 text-xs px-4 py-2 rounded-xl font-semibold shadow-sm">
                                    Validation Error Encountered.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Inner Console Container Workspace -->
                    <div class="flex flex-col flex-1 overflow-hidden rounded-[2rem] bg-[#C9D6E4] shadow-2xl text-slate-900">
                        <div class="flex flex-col flex-1 overflow-hidden px-6 py-5 space-y-4">

                            <!-- Top Metrics Strip -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 shrink-0">
                                <div class="rounded-xl border border-slate-300/40 bg-white/70 p-3.5 text-center shadow-sm">
                                    <span class="block text-xs font-bold text-slate-700">Open Critical Incidents</span>
                                    <span class="block text-2xl font-extrabold text-[#132B52] my-0.5">{{ $criticalCount }}</span>
                                    <span class="text-[10px] font-semibold {{ $criticalCount > 0 ? 'text-rose-600 animate-pulse' : 'text-slate-500' }}">
                                        {{ $criticalCount > 0 ? 'Requires immediate triage' : 'System status normal' }}
                                    </span>
                                </div>
                                <div class="rounded-xl border border-slate-300/40 bg-white/70 p-3.5 text-center shadow-sm">
                                    <span class="block text-xs font-bold text-slate-700">Average Time to Resolution</span>
                                    <span class="block text-2xl font-extrabold text-[#132B52] my-0.5">{{ $avgResolutionTime }}</span>
                                    <span class="text-[10px] font-semibold text-emerald-600">Within defined SLA limits</span>
                                </div>
                                <div class="rounded-xl border border-slate-300/40 bg-white/70 p-3.5 text-center shadow-sm">
                                    <span class="block text-xs font-bold text-slate-700">Total Incidents Monitored</span>
                                    <span class="block text-2xl font-extrabold text-[#132B52] my-0.5">{{ $totalThisMonth }}</span>
                                    <span class="text-[10px] font-semibold text-slate-500">Active historical ledger logs</span>
                                </div>
                            </div>

                            <!-- White Table Canvas -->
                            <div class="flex-1 flex flex-col overflow-hidden rounded-2xl border border-slate-300/40 bg-white shadow-md">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 border-b border-slate-100 shrink-0">
                                    <button onclick="toggleModal(true)" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                                        <span class="text-sm leading-none">+</span> Log Incident
                                    </button>
                                    
                                    <!-- Unified Filter and Search Block -->
                                    <form method="GET" action="{{ route('client.itsm.risk.incident') }}" id="searchForm" class="flex items-center gap-3">
                                        <!-- Hidden field para sa status persistence -->
                                        <input type="hidden" name="status" id="hiddenStatusFilter" value="{{ $currentStatus }}">

                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                                <i data-lucide="search" class="h-4 w-4"></i>
                                            </span>
                                            <input type="text" name="search" value="{{ $currentSearch }}" placeholder="Search ID, title, or reporter..." class="w-64 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-9 pr-4 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()" />
                                        </div>

                                        <!-- Custom Dropdown Component Layout -->
                                        <div class="relative inline-block text-left">
                                            <button type="button" onclick="toggleDropdownMenu()" id="dropdownBtn" class="flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-600 transition cursor-pointer py-1.5 px-3 rounded-lg hover:bg-slate-100 border border-slate-200 bg-slate-50">
                                                <span class="flex items-center gap-1.5">
                                                    <!-- ANDITO NA SI FILTER ICON! -->
                                                    <i data-lucide="filter" class="h-4 w-4 text-slate-500"></i>
                                                    {{ $currentStatus ?: 'All' }}
                                                </span>
                                            </button>

                                            <!-- Floating Menu Items Panel -->
                                            <div id="dropdownMenu" class="absolute right-0 z-30 mt-2 w-40 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5 focus:outline-none hidden transition-all scale-95 opacity-0 duration-100">
                                                <div class="py-1 text-slate-700 font-medium text-xs">
                                                    <button type="button" onclick="applyStatusFilter('')" class="flex w-full items-center px-4 py-2 hover:bg-slate-50 text-left transition {{ $currentStatus === '' ? 'bg-slate-50 font-bold text-blue-600' : '' }}">All Statuses</button>
                                                    <button type="button" onclick="applyStatusFilter('Open')" class="flex w-full items-center px-4 py-2 hover:bg-slate-50 text-left transition {{ $currentStatus === 'Open' ? 'bg-slate-50 font-bold text-blue-600' : '' }}">Open</button>
                                                    <button type="button" onclick="applyStatusFilter('Investigating')" class="flex w-full items-center px-4 py-2 hover:bg-slate-50 text-left transition {{ $currentStatus === 'Investigating' ? 'bg-slate-50 font-bold text-blue-600' : '' }}">Investigating</button>
                                                    <button type="button" onclick="applyStatusFilter('Resolved')" class="flex w-full items-center px-4 py-2 hover:bg-slate-50 text-left transition {{ $currentStatus === 'Resolved' ? 'bg-slate-50 font-bold text-blue-600' : '' }}">Resolved</button>
                                                </div>
                                            </div>
                                        </div>

                                        @if($currentSearch || $currentStatus)
                                            <a href="{{ route('client.itsm.risk.incident') }}" class="text-xs text-blue-600 font-bold hover:underline ml-1">Clear Filters</a>
                                        @endif
                                    </form>
                                </div>

                                <!-- Scrollable Table Structure -->
                                <div class="flex-1 overflow-y-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead class="sticky top-0 bg-white z-10 shadow-[0_1px_0_0_rgba(226,232,240,1)]">
                                            <tr class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                                <th class="px-6 py-3.5 text-center">Incident ID & Title</th>
                                                <th class="px-6 py-3.5 text-center">Severity</th>
                                                <th class="px-6 py-3.5 text-center">Date/Time</th>
                                                <th class="px-6 py-3.5 text-center">Reporter</th>
                                                <th class="px-6 py-3.5 text-center">Status</th>
                                                <th class="px-6 py-3.5 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                            @forelse ($incidents as $incident)
                                                <tr class="bg-white hover:bg-slate-50/50 transition h-14">
                                                    <td class="px-6 py-3 font-semibold">
                                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $incident['id'] }}</span>
                                                        <span class="text-slate-900 text-xs tracking-tight">{{ $incident['title'] }}</span>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        @switch($incident['severity'])
                                                            @case('Critical')
                                                                <span class="inline-block px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px]">Critical</span>
                                                                @break
                                                            @case('High')
                                                                <span class="inline-block px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 font-semibold text-[10px]">High</span>
                                                                @break
                                                            @case('Medium')
                                                                <span class="inline-block px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-semibold text-[10px]">Medium</span>
                                                                @break
                                                            @default
                                                                <span class="inline-block px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold text-[10px]">Low</span>
                                                        @endswitch
                                                    </td>
                                                    <td class="px-6 py-3 font-mono text-slate-500">{{ $incident['datetime'] }}</td>
                                                    <td class="px-6 py-3 font-medium text-slate-800">{{ $incident['reporter'] }}</td>
                                                    <td class="px-6 py-3 text-center">
                                                        @switch($incident['status'])
                                                            @case('Resolved')
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-bold text-[10px]">✓ Resolved</span>
                                                                @break
                                                            @case('Investigating')
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px] animate-pulse">⚲ Investigating</span>
                                                                @break
                                                            @default
                                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 font-bold text-[10px]">● Open</span>
                                                        @endswitch
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <form method="POST" action="{{ route('client.itsm.risk.incident.status', $incident['id']) }}">
                                                            @csrf
                                                            <input type="hidden" name="current_status_context" value="{{ $currentStatus }}">
                                                            <input type="hidden" name="current_search_context" value="{{ $currentSearch }}">
                                                            
                                                            <select name="status" onchange="if(this.value !== '') { this.form.submit(); }" class="bg-slate-50 border border-slate-200 text-[11px] rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                                <option value="">Update Status</option>
                                                                <option value="Open" {{ $incident['status'] === 'Open' ? 'selected' : '' }}>Open</option>
                                                                <option value="Investigating" {{ $incident['status'] === 'Investigating' ? 'selected' : '' }}>Investigating</option>
                                                                <option value="Resolved" {{ $incident['status'] === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                                                        No incident reports match your queries.
                                                    </td>
                                                </tr>
                                            @endforelse

                                            @for ($i = 0; $i < max(0, 6 - count($incidents)); $i++)
                                                <tr class="bg-white/40 h-14 pointer-events-none">
                                                    <td colspan="6"></td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal Layout -->
    <div id="incidentModal" class="fixed inset-0 z-50 hidden bg-slate-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl overflow-hidden text-slate-900 border border-slate-100 transform transition-all">
            <div class="bg-[#DDE4EC] px-8 py-5 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-950">Log New System Incident</h3>
                <button onclick="toggleModal(false)" class="text-slate-500 hover:text-slate-800 text-lg font-bold">×</button>
            </div>
            <form action="{{ route('client.itsm.risk.incident.store') }}" method="POST" class="p-8 space-y-4">
                @csrf
                <input type="hidden" name="current_status_context" value="{{ $currentStatus }}">
                <input type="hidden" name="current_search_context" value="{{ $currentSearch }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Incident Title / Core Summary</label>
                    <input type="text" name="title" required placeholder="e.g., Auth Server Gateway 502 Timeout" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Severity</label>
                    <select name="severity" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Low">Low (Minor glitch, fallback active)</option>
                        <option value="Medium">Medium (Performance degradation)</option>
                        <option value="High">High (Core feature down for users)</option>
                        <option value="Critical">Critical (Total system lockout / Data risk)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Technical Context Details</label>
                    <textarea name="description" rows="3" placeholder="Describe environmental factors, logs, or user impact paths..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                </div>
                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="toggleModal(false)" class="px-5 py-2 rounded-full text-xs font-bold text-slate-500 hover:bg-slate-100 transition">Cancel</button>
                    <button type="submit" class="px-6 py-2 rounded-full text-xs font-bold text-white bg-[#1A73E8] hover:bg-blue-700 transition shadow-md">Submit Log</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        lucide.createIcons();

        function toggleModal(show) {
            const modal = document.getElementById('incidentModal');
            if(show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function toggleDropdownMenu() {
            const container = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            const isHidden = container.classList.contains('hidden');

            if (isHidden) {
                container.classList.remove('hidden');
                setTimeout(() => {
                    container.classList.remove('scale-95', 'opacity-0');
                    container.classList.add('scale-100', 'opacity-100');
                }, 10);
                arrow.classList.add('rotate-180');
            } else {
                container.classList.remove('scale-100', 'opacity-100');
                container.classList.add('scale-95', 'opacity-0');
                arrow.classList.remove('rotate-180');
                setTimeout(() => {
                    container.classList.add('hidden');
                }, 100);
            }
            lucide.createIcons();
        }

        function applyStatusFilter(value) {
            document.getElementById('hiddenStatusFilter').value = value;
            document.getElementById('searchForm').submit();
        }

        window.addEventListener('click', function(e) {
            const btn = document.getElementById('dropdownBtn');
            const menu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            
            if (btn && !btn.contains(e.target) && menu && !menu.contains(e.target)) {
                menu.classList.remove('scale-100', 'opacity-100');
                menu.classList.add('scale-95', 'opacity-0');
                if(arrow) arrow.classList.remove('rotate-180');
                setTimeout(() => {
                    menu.classList.add('hidden');
                }, 100);
            }
        });
    </script>
</body>
</html>