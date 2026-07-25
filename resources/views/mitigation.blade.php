<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Risk Management - Mitigation Plans</title>
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
            <!-- Background Watermark brand logo -->
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[72rem] -translate-x-1/2 -translate-y-1/2 opacity-5 blur-sm">

            <div class="relative z-10 mx-auto flex w-full max-w-[1760px] gap-6 items-stretch h-[calc(100vh-120px)]">
                
                <!-- Left Sidebar Navigation Panel -->
                <aside class="w-64 shrink-0 rounded-[2rem] bg-white p-6 shadow-2xl text-slate-900 flex flex-col justify-between h-full">
                    <div class="flex flex-col gap-2 font-semibold text-sm text-slate-600">
                        <a href="{{ route('client.itsm.risk') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
                            <i data-lucide="file-text" class="h-4.5 w-4.5"></i> Risk Register
                        </a>
                        <a href="{{ route('client.itsm.risk.mitigation') }}" class="flex items-center gap-3 rounded-xl bg-slate-100 px-4 py-3 text-[#132B52] font-bold transition">
                            <i data-lucide="list-checks" class="h-4.5 w-4.5"></i> Mitigation Plans
                        </a>
                        <a href="{{ route('client.itsm.risk.incident') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
                            <i data-lucide="alert-circle" class="h-4.5 w-4.5"></i> Incident Report
                        </a>
                        <a href="{{ route('client.itsm.risk.analytics') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
                            <i data-lucide="bar-chart-3" class="h-4.5 w-4.5"></i> Risk Analytics
                        </a>
                    </div>
                </aside>

                <!-- Right Side Console Viewspace -->
                <section class="flex-1 flex flex-col gap-5 h-full overflow-hidden">
                    
                    <!-- Content Title Header Box -->
                    <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-5 text-slate-950 shadow-sm shrink-0">
                        <h1 class="text-4xl font-bold tracking-tight">Risk Management</h1>
                    </div>

                    <!-- Inner Console Container Workspace -->
                    <div class="flex flex-col flex-1 overflow-hidden rounded-[2rem] bg-[#C9D6E4] shadow-2xl text-slate-900">
                        
                        <!-- Inner Canvas Workspace -->
                        <div class="flex flex-col flex-1 overflow-hidden px-6 py-5 space-y-4">

                            <!-- Top Metrics Summary Strip Container -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 shrink-0">
                                <!-- Metric 1: Active Plans -->
                                <div class="rounded-xl border border-slate-300/40 bg-white/70 p-3.5 text-center shadow-sm">
                                    <span class="block text-xs font-bold text-slate-700">Active Plans in Progress</span>
                                    <span class="block text-2xl font-extrabold text-[#132B52] my-0.5">{{ $activeCount }}</span>
                                    <span class="text-[10px] font-semibold text-slate-500">Monitored operations runtime</span>
                                </div>
                                <!-- Metric 2: Mitigation Budget -->
                                <div class="rounded-xl border border-slate-300/40 bg-white/70 p-3.5 text-center shadow-sm">
                                    <span class="block text-xs font-bold text-slate-700">Total Mitigation Budget</span>
                                    <span class="block text-2xl font-extrabold text-[#132B52] my-0.5">₱ {{ number_format($totalBudget, 2) }}</span>
                                    <span class="text-[10px] font-semibold text-slate-500">Allocated financial assets</span>
                                </div>
                                <!-- Metric 3: Critical Overdue Plans -->
                                <div class="rounded-xl border border-slate-300/40 bg-white/70 p-3.5 text-center shadow-sm">
                                    <span class="block text-xs font-bold text-slate-700">Plans under Draft State</span>
                                    <span class="block text-2xl font-extrabold text-amber-600 my-0.5">{{ $overdueCount }}</span>
                                    <span class="text-[10px] font-semibold text-amber-600">Awaiting management deployment</span>
                                </div>
                            </div>

                            <!-- Maximized White Table Canvas -->
                            <div class="flex-1 flex flex-col overflow-hidden rounded-2xl border border-slate-300/40 bg-white shadow-md">
                                
                                <!-- Unified Actions, Search & Filters Ribbon Bar -->
                                <div class="px-6 py-4 border-b border-slate-100 shrink-0">
                                    <form action="{{ route('client.itsm.risk.mitigation') }}" method="GET" id="searchForm" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        
                                        <!-- LEFT SIDE: Trigger for Opening the Modal Form -->
                                        <div>
                                            <button type="button" onclick="toggleModal('createPlanModal', true)" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                                                <i data-lucide="plus" class="h-4 w-4"></i>
                                                <span>Create Plan</span>
                                            </button>
                                        </div>

                                        <!-- Hidden Input to Persist Status Filter on Text Search Submit -->
                                        <input type="hidden" name="status_filter" id="hiddenStatusFilter" value="{{ request('status_filter', '') }}">

                                        <!-- RIGHT SIDE: Search, Filter Dropdown, and Clear Controls -->
                                        <div class="flex items-center gap-4 ml-auto">
                                            
                                            <!-- Search Box -->
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                                    <i data-lucide="search" class="h-4.5 w-4.5"></i>
                                                </span>
                                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or owner..." class="w-64 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-11 pr-5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                            </div>

                                            <!-- FILTER COMBINED BUTTON WITH DYNAMIC TEXT -->
                                            <div class="relative inline-block text-left">
                                                <button type="button" onclick="toggleFilterDropdown()" class="flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-600 transition cursor-pointer py-1.5 px-3 rounded-lg hover:bg-slate-100 border border-slate-200 bg-slate-50">
                                                    <i data-lucide="filter" class="h-4 w-4 text-slate-500"></i>
                                                    <span id="activeFilterText" class="text-[#132B52] font-extrabold">
                                                        {{ request('status_filter') ? request('status_filter') : 'All' }}
                                                    </span>
                                                </button>
                                                
                                                <!-- Floating Choices Dropdown -->
                                                <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white shadow-xl z-50 py-1 text-slate-800">
                                                    <button type="button" onclick="applyStatusFilter('')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2">All Statuses</button>
                                                    <button type="button" onclick="applyStatusFilter('Draft')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-amber-500"></span> Draft</button>
                                                    <button type="button" onclick="applyStatusFilter('In Progress')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-blue-500"></span> In Progress</button>
                                                    <button type="button" onclick="applyStatusFilter('Completed')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-green-500"></span> Completed</button>
                                                </div>
                                            </div>

                                            @if(request('status_filter') || request('search'))
                                                <!-- Clear Filters Control Link -->
                                                <a href="{{ route('client.itsm.risk.mitigation') }}" class="flex items-center gap-1.5 text-xs font-bold text-red-500 hover:text-red-700 transition ml-1">
                                                    <i data-lucide="filter-x" class="h-4 w-4"></i>
                                                    <span>Clear</span>
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>

                                <!-- Fully Scrollable Data List -->
                                <div class="flex-1 overflow-y-auto">
                                    <table class="w-full border-collapse">
                                        <thead class="sticky top-0 bg-white z-10 shadow-[0_1px_0_0_rgba(226,232,240,1)]">
                                            <tr class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                                                <th class="px-6 py-3.5 font-semibold text-center">Plan ID & Title</th>
                                                <th class="px-6 py-3.5 font-semibold text-center">Linked Risk</th>
                                                <th class="px-6 py-3.5 font-semibold text-center">Owner</th>
                                                <th class="px-6 py-3.5 font-semibold text-center">Budget</th>
                                                <th class="px-6 py-3.5 font-semibold text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-xs">
                                            @forelse ($mitigations as $plan)
                                                <tr class="bg-white hover:bg-slate-50/50 transition h-12 text-slate-800">
                                                    <td class="px-6 py-3 text-left font-medium">
                                                        <span class="text-slate-400 block text-[10px]">MP-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                        <span class="font-bold text-slate-900">{{ $plan->title }}</span>
                                                    </td>
                                                    <td class="px-6 py-3 font-semibold text-slate-600 text-center">
                                                        {{ $plan->risk->title ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-6 py-3 text-slate-600 font-medium text-center">{{ $plan->owner }}</td>
                                                    <td class="px-6 py-3 font-bold text-slate-900 text-center">₱ {{ number_format($plan->budget, 2) }}</td>
                                                    <td class="px-6 py-3 text-center">
                                                        @if($plan->status == 'Completed')
                                                            <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold text-[10px]">Completed</span>
                                                        @elseif($plan->status == 'In Progress')
                                                            <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-bold text-[10px]">In Progress</span>
                                                        @else
                                                            <span class="inline-block px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">Draft</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <!-- Centered placeholder system block layout -->
                                                <tr class="bg-white h-12">
                                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium italic">
                                                        No mitigation record maps parsed matching parameters.
                                                    </td>
                                                </tr>
                                                @for ($i = 0; $i < 5; $i++)
                                                    <tr class="bg-white h-12">
                                                        <td colspan="5"></td>
                                                    </tr>
                                                @endfor
                                            @endforelse
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

    <!-- CREATE PLAN MODAL ELEMENT BOX -->
    <div id="createPlanModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs transition-opacity">
        <div class="w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl text-slate-900 mx-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                <h3 class="text-xl font-extrabold text-[#132B52]">Deploy Mitigation Plan</h3>
                <button onclick="toggleModal('createPlanModal', false)" class="text-slate-400 hover:text-slate-600 transition">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form action="{{ route('client.itsm.risk.mitigation.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Title Field -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Plan Title</label>
                    <input type="text" name="title" required placeholder="Describe mitigation operational goal" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>

                <!-- Linked Risk Options Field -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Linked Threat / Risk Context</label>
                    <select name="risk_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="" disabled selected>Select registered framework threat options...</option>
                        @foreach($risks as $risk)
                            <option value="{{ $risk->id }}">{{ $risk->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Owner & Budget Properties Form Layout Wrap Group -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Accountable Owner</label>
                        <input type="text" name="owner" required placeholder="Personnel name" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Allocated Budget (₱)</label>
                        <input type="number" step="0.01" name="budget" required placeholder="0.00" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Process Runtime State Filter Dropdown -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Deployment Status State</label>
                    <select name="status" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Draft">Draft</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                <!-- Submissions Button Controllers Group Panel -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                    <button type="button" onclick="toggleModal('createPlanModal', false)" class="rounded-full border border-slate-200 bg-white px-5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                        Deploy Infrastructure Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        lucide.createIcons();

        // Modal Open/Close Toggle Control System Functions
        function toggleModal(modalId, show) {
            const modalElement = document.getElementById(modalId);
            if (show) {
                modalElement.classList.remove('hidden');
            } else {
                modalElement.classList.add('hidden');
            }
        }

        // Custom Dropdown Filtering Logic Systems
        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('hidden');
        }

        function applyStatusFilter(value) {
            document.getElementById('hiddenStatusFilter').value = value;
            document.getElementById('searchForm').submit();
        }

        // Event listener para isara ang dropdown kapag nag-click sa labas nito
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('filterDropdown');
            const button = dropdown ? dropdown.previousElementSibling : null;
            if (dropdown && button && !dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>