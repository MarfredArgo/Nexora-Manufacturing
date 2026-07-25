<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Risk Management - Risk Register</title>
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
            <!-- Subtle background watermark brand logo -->
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[72rem] -translate-x-1/2 -translate-y-1/2 opacity-5 blur-sm">

            <div class="relative z-10 mx-auto flex w-full max-w-[1760px] gap-6 items-stretch h-[calc(100vh-120px)]">
                
                <!-- Left Sidebar Navigation Panel -->
                <aside class="w-64 shrink-0 rounded-[2rem] bg-white p-6 shadow-2xl text-slate-900 flex flex-col justify-between h-full">
                    <div class="flex flex-col gap-2 font-semibold text-sm text-slate-600">
                        <a href="{{ route('client.itsm.risk') }}" class="flex items-center gap-3 rounded-xl bg-slate-100 px-4 py-3 text-[#132B52] font-bold transition">
                            <i data-lucide="file-text" class="h-4.5 w-4.5"></i> Risk Register
                        </a>
                        <a href="{{ route('client.itsm.risk.mitigation') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-slate-50 hover:text-slate-900 transition">
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
                        <div class="flex flex-col flex-1 overflow-hidden px-6 py-5">

                            <!-- Maximized White Grid Canvas -->
                            <div class="flex-1 flex flex-col overflow-hidden rounded-2xl border border-slate-300/40 bg-white shadow-md">
                                
                                <!-- Unified Actions, Search & Filters Ribbon Bar -->
                                <div class="px-6 py-4 border-b border-slate-100 shrink-0">
                                    <form action="{{ route('client.itsm.risk') }}" method="GET" id="searchForm" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        
                                        <!-- LEFT SIDE: Trigger for Opening the Modal Form -->
                                        <div>
                                            <button type="button" onclick="toggleRiskModal(true)" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                                                <i data-lucide="plus" class="h-4 w-4"></i>
                                                <span>New Risk</span>
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
                                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search risk title or category..." class="w-64 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-11 pr-5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
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
                                                    <button type="button" onclick="applyStatusFilter('')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2">All</button>
                                                    <button type="button" onclick="applyStatusFilter('Unmitigated')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-red-500"></span> Unmitigated</button>
                                                    <button type="button" onclick="applyStatusFilter('In Progress')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-orange-500"></span> In Progress</button>
                                                    <button type="button" onclick="applyStatusFilter('Mitigated')" class="w-full text-left px-4 py-2 text-xs hover:bg-slate-50 transition flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-green-500"></span> Mitigated</button>
                                                </div>
                                            </div>

                                            @if(request('status_filter') || request('search'))
                                                <!-- Clear Filters Control Link (Lalabas lang pag may active field) -->
                                                <a href="{{ route('client.itsm.risk') }}" class="flex items-center gap-1.5 text-xs font-bold text-red-500 hover:text-red-700 transition ml-1">
                                                    <i data-lucide="filter-x" class="h-4 w-4"></i>
                                                    <span>Clear</span>
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>

                                <!-- Fully Scrollable Risk Card Layout Workspace -->
                                <div class="flex-1 overflow-y-auto p-6">
                                    @if($risks->isEmpty())
                                        <div class="flex flex-col items-center justify-center h-full text-slate-400 py-12">
                                            <i data-lucide="shield-alert" class="h-12 w-12 mb-2 opacity-50"></i>
                                            <p class="text-sm font-medium">No risk records found.</p>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                            @foreach($risks as $risk)
                                                <div class="flex flex-col justify-between rounded-2xl border border-slate-200/60 bg-slate-50/50 p-6 shadow-sm hover:shadow-md transition text-left">
                                                    <div class="space-y-1">
                                                        <h3 class="text-base font-bold text-slate-900 line-clamp-2" title="{{ $risk->title }}">{{ $risk->title }}</h3>
                                                        <p class="text-[11px] font-semibold text-slate-400 tracking-wide uppercase">{{ $risk->category }}</p>
                                                    </div>
                                                    
                                                    <div class="my-6 space-y-3">
                                                        <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                                                            @php
                                                                $barColor = 'bg-green-500';
                                                                if(strtolower($risk->status) === 'unmitigated') $barColor = 'bg-red-500';
                                                                elseif(strtolower($risk->status) === 'in progress') $barColor = 'bg-orange-500';
                                                            @endphp
                                                            <div class="{{ $barColor }} h-full" style="width: {{ $risk->progress }}%"></div>
                                                        </div>
                                                        
                                                        <div class="flex items-center justify-start gap-1.5 text-xs font-bold text-slate-700">
                                                            @php
                                                                $dotColor = 'bg-green-500';
                                                                if(strtolower($risk->status) === 'unmitigated') $dotColor = 'bg-red-500';
                                                                elseif(strtolower($risk->status) === 'in progress') $dotColor = 'bg-orange-500';
                                                            @endphp
                                                            <span class="h-2 w-2 rounded-full {{ $dotColor }}"></span>
                                                            <span>{{ $risk->status }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-3">
                                                        <span class="block text-[10px] font-medium text-slate-400">Last Reviewed: {{ $risk->last_reviewed }}</span>
                                                        <a href="{{ route('client.itsm.risk.manage', $risk->id) }}" class="block w-full rounded-lg border border-slate-300 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition text-center">
                                                            Manage Risk
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                            </div>

                        </div>
                    </div>

                </section>
            </div>
        </main>
    </div>

    <!-- Creation Overlay Modal Box for Adding Entry -->
    <div id="riskModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl text-slate-900 mx-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-xl font-bold text-[#132B52]">Log New Risk Matrix</h2>
                <button type="button" onclick="toggleRiskModal(false)" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            
            <form action="{{ route('client.itsm.risk.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Risk Description / Title</label>
                    <input type="text" name="title" required placeholder="e.g. Database server hardware degradation" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-slate-50" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Category</label>
                    <input type="text" name="category" required placeholder="e.g. Infrastructure, Security" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-slate-50" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-slate-50">
                            <option value="Unmitigated">Unmitigated</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Mitigated">Mitigated</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Progress (%)</label>
                        <input type="number" name="progress" min="0" max="100" value="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-slate-50" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 mt-6">
                    <button type="button" onclick="toggleRiskModal(false)" class="rounded-lg px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 transition">Cancel</button>
                    <button type="submit" class="rounded-lg bg-[#1A73E8] px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition">Save Record</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        lucide.createIcons();

        // Control para sa Modal Form (Add New Risk)
        function toggleRiskModal(show) {
            const modal = document.getElementById('riskModal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        // Buksan/Isara ang Floating Dropdown choices ng Filter Button
        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Pag-click sa status option: Automatic trigger ng submisyon ng parent form
        function applyStatusFilter(statusValue) {
            document.getElementById('hiddenStatusFilter').value = statusValue;
            document.getElementById('searchForm').submit();
        }

        // Isara ang dropdown kapag nag-click sa labas ng button area
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('filterDropdown');
            if (dropdown && !e.target.closest('#filterDropdown') && !e.target.closest('button[onclick="toggleFilterDropdown()"]')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>