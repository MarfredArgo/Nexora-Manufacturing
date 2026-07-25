<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Risk Management - Risk Analytics</title>
    <link class="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Include AlpineJS for seamless dynamic form & interaction submittals -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        <a href="{{ route('client.itsm.risk') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-50 hover:text-slate-900 {{ Route::is('client.itsm.risk') ? 'bg-slate-100 text-[#132B52] font-bold' : '' }}">
                            <i data-lucide="file-text" class="h-4.5 w-4.5"></i> Risk Register
                        </a>
                        <a href="{{ route('client.itsm.risk.mitigation') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-50 hover:text-slate-900 {{ Route::is('client.itsm.risk.mitigation') ? 'bg-slate-100 text-[#132B52] font-bold' : '' }}">
                            <i data-lucide="list-checks" class="h-4.5 w-4.5"></i> Mitigation Plans
                        </a>
                        <a href="{{ route('client.itsm.risk.incident') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-50 hover:text-slate-900 {{ Route::is('client.itsm.risk.incident') ? 'bg-slate-100 text-[#132B52] font-bold' : '' }}">
                            <i data-lucide="alert-circle" class="h-4.5 w-4.5"></i> Incident Report
                        </a>
                        <a href="{{ route('client.itsm.risk.analytics') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition hover:bg-slate-50 hover:text-slate-900 {{ Route::is('client.itsm.risk.analytics') ? 'bg-slate-100 text-[#132B52] font-bold' : '' }}">
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

                            <!-- Maximized Canvas Area -->
                            <div class="flex-1 flex flex-col overflow-hidden rounded-2xl border border-slate-300/40 bg-white p-6 shadow-md justify-between">
                                
                                <div class="flex flex-col flex-1 justify-between gap-5">
                                    
                                    <!-- Dynamic Action Controls Form Workspace Container -->
                                    <form id="analyticsFilterForm" action="{{ route('client.itsm.risk.analytics') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2 shrink-0" x-data>
                                        <span class="text-sm font-bold text-slate-700 uppercase tracking-wider">Executive Performance & Trends</span>
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <select name="timeframe" @change="$el.form.submit()" class="appearance-none w-40 rounded-full border border-slate-300/60 bg-white/80 py-1.5 pl-4 pr-9 text-xs font-bold text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                                    <option value="30_days" {{ $timeframe === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                                    <option value="6_months" {{ $timeframe === '6_months' ? 'selected' : '' }}>Last 6 Months</option>
                                                    <option value="this_year" {{ $timeframe === 'this_year' ? 'selected' : '' }}>This Year</option>
                                                </select>
                                                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-500">
                                                    <i data-lucide="chevron-down" class="h-3.5 w-3.5"></i>
                                                </span>
                                            </div>
                                            
                                            <!-- Export button links down to dynamic backend generation stream -->
                                            <a href="{{ route('client.itsm.risk.analytics.export', ['timeframe' => $timeframe]) }}" class="flex items-center gap-1.5 rounded-full border border-slate-300/60 bg-white/80 px-4 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition shadow-sm">
                                                <i data-lucide="download" class="h-3.5 w-3.5"></i> Export Report
                                            </a>
                                        </div>
                                    </form>

                                    <!-- Dynamic Data Aggregate Metric Strips -->
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 shrink-0">
                                        <!-- Card 1: Risk Mitigation Index -->
                                        <div class="rounded-2xl border border-slate-300/40 bg-slate-50/50 p-5 text-left shadow-sm">
                                            <span class="block text-xs font-bold text-slate-500 tracking-wide uppercase">Mitigation Index</span>
                                            <span class="block text-3xl font-extrabold text-[#132B52] my-1">{{ $mitigationIndex }}%</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold {{ $mitigationIndex >= 75 ? 'text-green-600' : 'text-amber-600' }}">
                                                <i data-lucide="trending-up" class="h-3 w-3"></i> System efficiency baseline
                                            </span>
                                        </div>
                                        
                                        <!-- Card 2: Avg Resolution Efficiency -->
                                        <div class="rounded-2xl border border-slate-300/40 bg-slate-50/50 p-5 text-left shadow-sm">
                                            <span class="block text-xs font-bold text-slate-500 tracking-wide uppercase">Avg Resolution</span>
                                            <span class="block text-3xl font-extrabold text-[#132B52] my-1">{{ $avgResolutionFormatted }}</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600">
                                                <i data-lucide="trending-down" class="h-3 w-3"></i> Active timeline index
                                            </span>
                                        </div>
                                        
                                        <!-- Card 3: Controlled Hazards -->
                                        <div class="rounded-2xl border border-slate-300/40 bg-slate-50/50 p-5 text-left shadow-sm">
                                            <span class="block text-xs font-bold text-slate-500 tracking-wide uppercase">Controlled Hazards</span>
                                            <span class="block text-3xl font-extrabold text-[#132B52] my-1">{{ $controlledHazards }} / {{ $totalHazards }}</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500">
                                                {{ $totalHazards - $controlledHazards }} remaining exposures
                                            </span>
                                        </div>
                                        
                                        <!-- Card 4: Unassigned Risks -->
                                        <div class="rounded-2xl border border-slate-300/40 bg-slate-50/50 p-5 text-left shadow-sm">
                                            <span class="block text-xs font-bold text-slate-500 tracking-wide uppercase">Unassigned Risks</span>
                                            <span class="block text-3xl font-extrabold my-1 {{ $unassignedRisks > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $unassignedRisks }}</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold {{ $unassignedRisks > 0 ? 'text-amber-600' : 'text-green-600' }}">
                                                <i data-lucide="{{ $unassignedRisks > 0 ? 'alert-triangle' : 'check-circle' }}" class="h-3 w-3"></i> 
                                                {{ $unassignedRisks > 0 ? 'Action required immediately' : 'All tracks owned' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Dual Grid Panel Chart Canvas Layouts -->
                                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 flex-1 min-h-0">
                                        
                                        <!-- Left Panel Box: Risk Status Breakdown Tracker -->
                                        <div class="xl:col-span-1 rounded-2xl border border-slate-200/60 bg-slate-50/30 p-5 shadow-sm flex flex-col justify-between">
                                            <div class="pb-3 border-b border-slate-100 flex items-center justify-between shrink-0">
                                                <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wide">Risk Status Distribution</span>
                                                <span class="text-[10px] font-bold text-slate-400">Total Tracked: {{ $totalRisks }}</span>
                                            </div>
                                            
                                            <!-- Visual Dynamic Status Metrics Bars Area -->
                                            <div class="my-auto space-y-3">
                                                <!-- Unmitigated Count -->
                                                <div class="flex items-center justify-between bg-red-50/60 border border-red-100/50 rounded-xl p-3">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="h-3 w-3 rounded-full bg-red-500"></span>
                                                        <span class="text-xs font-bold text-slate-700">Unmitigated Danger</span>
                                                    </div>
                                                    <span class="text-sm font-extrabold text-red-600">{{ $statusDistribution['unmitigated'] }} {{ Str::plural('Risk', $statusDistribution['unmitigated']) }}</span>
                                                </div>

                                                <!-- In Progress Count -->
                                                <div class="flex items-center justify-between bg-orange-50/60 border border-orange-100/50 rounded-xl p-3">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="h-3 w-3 rounded-full bg-orange-500"></span>
                                                        <span class="text-xs font-bold text-slate-700">In Progress Plans</span>
                                                    </div>
                                                    <span class="text-sm font-extrabold text-orange-600">{{ $statusDistribution['in_progress'] }} {{ Str::plural('Risk', $statusDistribution['in_progress']) }}</span>
                                                </div>

                                                <!-- Safe Count -->
                                                <div class="flex items-center justify-between bg-green-50/60 border border-green-100/50 rounded-xl p-3">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="h-3 w-3 rounded-full bg-green-500"></span>
                                                        <span class="text-xs font-bold text-slate-700">Fully Secured & Safe</span>
                                                    </div>
                                                    <span class="text-sm font-extrabold text-green-600">{{ $statusDistribution['secured'] }} {{ Str::plural('Risk', $statusDistribution['secured']) }}</span>
                                                </div>
                                            </div>

                                            <div class="pt-3 border-t border-slate-100 text-[10px] font-medium text-slate-400 text-center shrink-0">
                                                Updated real-time from active Register modules
                                            </div>
                                        </div>

                                        <!-- Right Panel Box: Breakdown Distribution / Vulnerability Vectors -->
                                        <div class="xl:col-span-2 rounded-2xl border border-slate-200/60 bg-slate-50/30 p-5 shadow-sm flex flex-col justify-between">
                                            <div class="pb-3 border-b border-slate-100 flex items-center justify-between shrink-0">
                                                <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wide">Top Vulnerability Vectors</span>
                                                <span class="text-[10px] font-bold text-slate-400">Exposures per Department</span>
                                            </div>

                                            <!-- Dynamic Iteration over Database Analytics Vectors -->
                                            <div class="my-auto space-y-3.5 overflow-y-auto max-h-[220px] pr-1">
                                                @foreach($vulnerabilityVectors as $vector)
                                                <div class="space-y-1">
                                                    <div class="flex justify-between text-xs font-bold text-slate-700">
                                                        <span>{{ $vector['name'] }}</span>
                                                        <span class="text-slate-500">{{ $vector['percentage'] }}%</span>
                                                    </div>
                                                    <div class="w-full bg-slate-200/70 h-2 rounded-full overflow-hidden">
                                                        <div class="bg-[#1A73E8] h-full rounded-full transition-all duration-500" style="width: {{ $vector['percentage'] }}%"></div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div class="text-right pt-2 border-t border-slate-100 shrink-0">
                                                <a href="{{ route('client.itsm.risk') }}" class="text-[11px] font-bold text-[#1A73E8] hover:underline transition">View Detailed Logs →</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </section>
            </div>
        </main>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>