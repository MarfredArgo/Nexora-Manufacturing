<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Compliance Tracking</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/nexora-icon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons for clean tab and action styling -->
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

        <!-- Main section scales up to 1760px to occupy maximum widescreen space natively -->
        <main class="relative flex-1 overflow-hidden px-8 py-6 xl:px-12">
            <img src="{{ asset('images/nexora-icon.png') }}" alt="" class="pointer-events-none absolute left-1/2 top-1/2 w-[72rem] -translate-x-1/2 -translate-y-1/2 opacity-5 blur-sm">

            <section class="relative z-10 mx-auto w-full max-w-[1760px] space-y-5">
                
                <!-- Compliance Tracking Header (Sleek, Wide Banner) -->
                <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-6 text-slate-950 shadow-sm flex justify-between items-center">
                    <h1 class="text-4xl font-bold tracking-tight">Compliance Tracking</h1>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-1.5 rounded-full text-xs font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                <!-- Main Content Workspace Console (Added min-h-[75vh] to stretch downward) -->
                <div class="flex flex-col min-h-[75vh] overflow-hidden rounded-[2rem] bg-[#C9D6E4] shadow-2xl text-slate-900 pb-10">
                    
                    <!-- Subtabs Bar (Stretches Full Width using flex-1 for buttons) -->
                    <div class="flex w-full border-b border-slate-300/80 bg-white pt-4 text-sm font-semibold text-slate-500">
                        <button class="flex flex-1 items-center justify-center gap-2 border-b-4 border-[#132B52] pb-3.5 text-[#132B52]">
                            <i data-lucide="clipboard-check" class="h-4.5 w-4.5"></i> Compliance Requirements
                        </button>
                        <a href="{{ route('client.itsm.audit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
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

                    <!-- Filter & Search Action Bar Form -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-10 py-6">
                        <!-- Add New Button triggers dynamic JS modal -->
                        <button type="button" onclick="openModal()" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-blue-700 transition">
                            <span class="text-lg leading-none">+</span> Add new
                        </button>
                        
                        <div class="flex items-center gap-5">
                            <!-- Search Field Container -->
                            <form action="{{ route('client.itsm.compliance') }}" method="GET" id="searchForm" class="relative">
                                <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                                    <i data-lucide="search" class="h-4 w-4"></i>
                                </span>
                                <!-- Pinanatili ang dating filtered query states kung mayroon man -->
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search" class="w-72 rounded-full border border-slate-300/60 bg-white/80 py-2 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()" />
                            </form>

                            <!-- Filter Controls (Dynamic Text Context Dropdown) -->
                            <div class="relative inline-block text-left">
                                <button type="button" onclick="toggleFilterDropdown()" class="flex items-center gap-2 text-sm font-bold text-slate-900 hover:text-slate-700 transition focus:outline-none">
                                    <i data-lucide="filter" class="h-4 w-4"></i>
                                    <span class="capitalize">{{ request('status') ?: 'All' }}</span>
                                </button>

                                <!-- Dropdown Card Options Overlay -->
                                <div id="filterDropdown" class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5 opacity-0 pointer-events-none transition-all duration-200 z-30">
                                    <div class="py-1">
                                        <a href="{{ route('client.itsm.compliance', ['search' => request('search')]) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">All</a>
                                        <a href="{{ route('client.itsm.compliance', ['status' => 'Active', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">Active</a>
                                        <a href="{{ route('client.itsm.compliance', ['status' => 'Urgent', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">Urgent</a>
                                        <a href="{{ route('client.itsm.compliance', ['status' => 'Completed', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">Completed</a>
                                        <a href="{{ route('client.itsm.compliance', ['status' => 'Pending Review', 'search' => request('search')]) }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">Pending Review</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Layout for cards -->
                    <div class="grid gap-6 px-10 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                        @forelse ($requirements as $item)
                            <article class="flex flex-col justify-between rounded-2xl border border-slate-200/50 bg-[#E8EEF4] p-6 shadow-md transition hover:shadow-lg">
                                <div>
                                    <h2 class="text-xl font-bold leading-tight text-slate-950">{{ $item['title'] }}</h2>
                                    <p class="text-xs font-semibold text-slate-500 mt-1.5">{{ $item['audience'] }}</p>

                                    <!-- Progress Bar Component -->
                                    <div class="mt-8 flex items-center gap-3">
                                        <div class="h-2.5 flex-1 rounded-full bg-[#BCCAD6]">
                                            <div class="h-2.5 rounded-full bg-[#132B52]" style="width: {{ $item['progress'] }}"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-600 w-8 text-right">{{ $item['progress'] }}</span>
                                    </div>
                                </div>

                                <div class="mt-8 space-y-4">
                                    <!-- Status Badge -->
                                    <div>
                                        <span class="inline-block rounded-full {{ $item['color'] }} px-3.5 py-1 text-[11px] font-bold text-white shadow-sm">
                                            {{ $item['status'] }}
                                        </span>
                                    </div>
                                    <!-- View Action Button -->
                                    <button class="w-full rounded-md border border-slate-950 py-1.5 text-xs font-bold tracking-wide transition hover:bg-slate-950 hover:text-white">
                                        View
                                    </button>
                                </div>
                            </article>
                        @empty
                            <!-- Border Dashed Empty State framework fallback -->
                            <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-400 p-12 text-center bg-white/20">
                                <p class="text-base font-bold text-slate-800">No records found.</p>
                                <p class="text-xs text-slate-600 mt-1">Try adding a new requirement.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Add New Requirement Modal Overlay Container -->
    <div id="addRequirementModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="w-full max-w-md scale-95 transform rounded-[2rem] bg-white p-8 text-slate-900 shadow-2xl transition-transform duration-300 ease-out">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                    <i data-lucide="file-plus" class="text-blue-600 h-5 w-5"></i> Add Requirement
                </h3>
                <button type="button" onclick="closeModal()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form action="{{ route('client.itsm.compliance.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Requirement Title</label>
                    <input type="text" name="title" required placeholder="e.g., Data Privacy Enforcement" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Target Audience</label>
                    <input type="text" name="audience" required placeholder="e.g., All Staff, IT Department" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Status Type</label>
                        <select name="status" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white font-semibold">
                            <option value="Active">Active</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Completed">Completed</option>
                            <option value="Pending Review">Pending Review</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Progress Percentage</label>
                        <input type="number" name="progress" min="0" max="100" required placeholder="e.g., 75" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5 mt-6">
                    <button type="button" onclick="closeModal()" class="rounded-full bg-slate-100 px-5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white hover:bg-blue-700 shadow-sm transition">
                        Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
   
    <script>
        // Init Lucide Icons
        lucide.createIcons();

        // Dropdown Toggle Engine
        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            if(dropdown.classList.contains('opacity-0')) {
                dropdown.classList.remove('opacity-0', 'pointer-events-none');
                dropdown.classList.add('opacity-100');
            } else {
                dropdown.classList.add('opacity-0', 'pointer-events-none');
                dropdown.classList.remove('opacity-100');
            }
        }

        // Close dropdown when clicking anywhere outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('filterDropdown');
            if (!e.target.closest('#filterDropdown') && !e.target.closest('button[onclick="toggleFilterDropdown()"]')) {
                dropdown.classList.add('opacity-0', 'pointer-events-none');
            }
        });

        // Add New Modal Script Helpers
        const modal = document.getElementById('addRequirementModal');
        const modalContainer = modal.querySelector('div');

        function openModal() {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modalContainer.classList.remove('scale-95');
            modalContainer.classList.add('scale-100');
        }

        function closeModal() {
            modal.classList.add('opacity-0', 'pointer-events-none');
            modalContainer.classList.remove('scale-100');
            modalContainer.classList.add('scale-95');
        }
    </script>
</body>
</html>