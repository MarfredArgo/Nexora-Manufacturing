<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Compliance Tracking - Risk Assessment</title>
    <link class="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js for Modals, Search, and Filtering without changing the layout -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-[#1B365D] font-sans text-white" 
      x-data="{ 
          searchQuery: '', 
          activeFilter: 'All', 
          isAddModalOpen: false, 
          isFilterModalOpen: false,
          risks: @js($risks ?? [])
      }">
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
                
                <!-- Compliance Tracking Header -->
                <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-6 text-slate-950 shadow-sm">
                    <h1 class="text-4xl font-bold tracking-tight">Compliance Tracking</h1>
                </div>

                <!-- Main Content Workspace Console -->
                <div class="flex flex-col min-h-[78vh] overflow-hidden rounded-[2rem] bg-[#C9D6E4] pb-10 shadow-2xl text-slate-900">
                    
                    <!-- Subtabs Bar (Active: Risk Assessment) -->
                    <div class="flex w-full border-b border-slate-300/80 bg-white pt-4 text-sm font-semibold text-slate-500">
                        <a href="{{ route('client.itsm.compliance') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="clipboard-check" class="h-4.5 w-4.5"></i> Compliance Requirements
                        </a>
                        <a href="{{ route('client.itsm.audit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="shield-alert" class="h-4.5 w-4.5"></i> Audits & Inspections
                        </a>
                        <a href="{{ route('client.itsm.permit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="file-badge" class="h-4.5 w-4.5"></i> Permits & Licenses
                        </a>
                        <a href="{{ route('client.itsm.risk.assessment') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-[#132B52] pb-3.5 text-[#132B52]">
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
                                <span class="block text-sm font-bold text-slate-700">Highest Exposure</span>
                                <span class="block text-4xl font-extrabold text-red-600 my-1">
                                    <span x-text="risks.length ? Math.max(...risks.map(r => r.inherent_score), 0) : 0"></span>
                                    <span class="text-lg font-bold" x-show="risks.length">(Critical)</span>
                                </span>
                                <span class="text-[10px] font-semibold text-slate-500" x-text="risks.length ? risks.reduce((max, r) => r.inherent_score > max.inherent_score ? r : max, risks[0]).risk_id + ' ' + (risks.reduce((max, r) => r.inherent_score > max.inherent_score ? r : max, risks[0]).title || '') : 'No active exposures'"></span>
                            </div>
                            <!-- Metric 2 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Average Residual Risk Rating</span>
                                <span class="block text-4xl font-extrabold text-green-600 my-1">
                                    <span x-text="risks.length ? (risks.reduce((sum, r) => sum + parseFloat(r.residual_score), 0) / risks.length).toFixed(1) : 0"></span>
                                    <span class="text-lg font-bold" x-show="risks.length">(Low)</span>
                                </span>
                                <span class="text-[10px] font-semibold text-slate-500" x-text="risks.length ? 'Within safe boundaries' : 'No data metrics available'"></span>
                            </div>
                            <!-- Metric 3 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Pending Re-assessment</span>
                                <span class="block text-4xl font-extrabold text-amber-600 my-1" x-text="risks.filter(r => r.status === 'Pending Review').length"></span>
                                <span class="text-[10px] font-semibold text-amber-600 font-medium" x-text="risks.filter(r => r.status === 'Pending Review').length ? 'Review action overdue' : 'All updates resolved'"></span>
                            </div>
                        </div>

                        <!-- Main Table Container -->
                        <div class="overflow-hidden rounded-2xl border border-slate-300/40 bg-white shadow-md">
                            
                            <!-- Control Panel Strip Inside Content Area -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
                                <!-- Action Trigger -->
                                <button @click="isAddModalOpen = true" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                                    <span class="text-sm leading-none">+</span> New Assessment
                                </button>
                                
                                <div class="flex items-center gap-5">
                                    <!-- Search Input -->
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                            <i data-lucide="search" class="h-4 w-4"></i>
                                        </span>
                                        <input type="text" x-model="searchQuery" placeholder="Search" class="w-64 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-9 pr-4 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                    </div>

                                    <!-- Filter Tool -->
                                    <div class="relative" x-data="{ openFilter: false }">
                                        <button @click="openFilter = !openFilter" class="flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-600 transition">
                                            <i data-lucide="filter" class="h-4 w-4"></i>
                                            <span x-text="activeFilter">All</span>
                                        </button>

                                        <div x-show="openFilter" @click.outside="openFilter = false" class="absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 z-20" x-cloak>
                                            <div class="py-1" role="none">
                                                <button type="button" @click="activeFilter = 'All'; openFilter = false" class="block w-full px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium text-left">
                                                    All
                                                </button>
                                                <button type="button" @click="activeFilter = 'Active'; openFilter = false" class="block w-full px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium text-left">
                                                    Active
                                                </button>
                                                <button type="button" @click="activeFilter = 'Mitigated'; openFilter = false" class="block w-full px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium text-left">
                                                    Mitigated
                                                </button>
                                                <button type="button" @click="activeFilter = 'Pending Review'; openFilter = false" class="block w-full px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 font-medium text-left">
                                                    Pending Review
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- List / Table (Fully Centered) -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-center border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                            <th class="px-6 py-4 font-semibold">
                                                <button class="inline-flex items-center justify-center hover:text-slate-800 w-full">
                                                    <span>Risk ID & Title</span>
                                                </button>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <button class="inline-flex items-center justify-center hover:text-slate-800 w-full">
                                                    <span>Inherent Score</span>
                                                </button>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <button class="inline-flex items-center justify-center hover:text-slate-800 w-full">
                                                    <span>Likelihood (1-5)</span>
                                                </button>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <button class="inline-flex items-center justify-center hover:text-slate-800 w-full">
                                                    <span>Impact (1-5)</span>
                                                </button>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <button class="inline-flex items-center justify-center hover:text-slate-800 w-full">
                                                    <span>Residual Score</span>
                                                </button>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <button class="inline-flex items-center justify-center hover:text-slate-800 w-full">
                                                    <span>Status</span>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        <!-- Empty State Placeholder UI Element -->
                                        <tr x-show="!risks.length">
                                            <td colspan="6" class="px-6 py-10 text-slate-400 font-medium">
                                                No risk matrix information tracked currently. Click "New Assessment" to initialize files.
                                            </td>
                                        </tr>

                                        <template x-for="risk in risks" :key="risk.id">
                                            <tr x-show="(activeFilter === 'All' || risk.status === activeFilter) && 
                                                       (risk.title.toLowerCase().includes(searchQuery.toLowerCase()) || risk.risk_id.toLowerCase().includes(searchQuery.toLowerCase()))"
                                                class="bg-white hover:bg-slate-50/50 transition">
                                                <td class="px-6 py-4 font-bold text-slate-900" x-text="'#' + risk.risk_id + ' : ' + risk.title"></td>
                                                <td class="px-6 py-4 text-slate-600 font-semibold" x-text="risk.inherent_score + ' (' + risk.inherent_text + ')'"></td>
                                                <td class="px-6 py-4 text-slate-600" x-text="risk.likelihood"></td>
                                                <td class="px-6 py-4 text-slate-600" x-text="risk.impact"></td>
                                                <td class="px-6 py-4 text-slate-600 font-semibold" x-text="risk.residual_score + ' (' + risk.residual_text + ')'"></td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold"
                                                          :class="{
                                                              'bg-red-100 text-red-800': risk.status === 'Active',
                                                              'bg-green-100 text-green-800': risk.status === 'Mitigated',
                                                              'bg-amber-100 text-amber-800': risk.status === 'Pending Review'
                                                          }"
                                                          x-text="risk.status">
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </main>
    </div>

    <!-- NEW ASSESSMENT MODAL -->
    <div x-show="isAddModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="isAddModalOpen = false"></div>
        
        <div class="relative w-full max-w-lg overflow-hidden rounded-[2rem] bg-[#C9D6E4] p-8 shadow-2xl text-slate-900 transition-all transform">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4 mb-5">
                <h3 class="text-xl font-bold text-[#132B52]">Create New Risk Assessment</h3>
                <button @click="isAddModalOpen = false" class="text-slate-500 hover:text-slate-800 transition">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <form action="{{ route('client.itsm.risk.assessment.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Risk ID (e.g., RSK-326)</label>
                    <input type="text" name="risk_id" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Risk Title</label>
                    <input type="text" name="title" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Inherent Score</label>
                        <input type="number" name="inherent_score" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Inherent Context text</label>
                        <input type="text" name="inherent_text" placeholder="Critical, High, Medium, Low" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Likelihood (1-5)</label>
                        <input type="number" min="1" max="5" name="likelihood" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Impact (1-5)</label>
                        <input type="number" min="1" max="5" name="impact" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Residual Score</label>
                        <input type="number" step="0.1" name="residual_score" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Residual Context text</label>
                        <input type="text" name="residual_text" placeholder="Low, Medium, High" required class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white/80 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1A73E8]">
                        <option value="Active">Active</option>
                        <option value="Mitigated">Mitigated</option>
                        <option value="Pending Review">Pending Review</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-300/60 mt-5">
                    <button type="button" @click="isAddModalOpen = false" class="rounded-full bg-slate-400 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-500 transition">Cancel</button>
                    <button type="submit" class="rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">Save Assessment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
        // Hook into Alpine mutation loops to ensure newly loaded dynamically rendered templates inherit Lucide visual graphics elements
        document.addEventListener('alpine:initialized', () => {
            Alpine.effect(() => {
                let query = searchQuery || activeFilter;
                setTimeout(() => lucide.createIcons(), 25);
            });
        });
    </script>
</body>
</html>