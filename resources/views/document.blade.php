<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora | Compliance Tracking - Documents</title>
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
                
                <!-- Compliance Tracking Header -->
                <div class="rounded-[2rem] bg-[#DDE4EC] px-10 py-6 text-slate-950 shadow-sm">
                    <h1 class="text-4xl font-bold tracking-tight">Compliance Tracking</h1>
                </div>

                <!-- Main Content Workspace Console -->
                <div class="flex flex-col min-h-[78vh] overflow-hidden rounded-[2rem] bg-[#C9D6E4] pb-10 shadow-2xl text-slate-900">
                    
                    <!-- Subtabs Bar (Active: Documents) -->
                    <div class="flex w-full border-b border-slate-300/80 bg-white pt-4 text-sm font-semibold text-slate-500">
                        <a href="{{ route('client.itsm.compliance') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="clipboard-check" class="h-4.5 w-4.5"></i> Compliance Requirements
                        </a>
                        <a href="{{ route('client.itsm.audit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="shield-alert" class="h-4.5 w-4.5"></i> Audits & Inspections
                        </a>
                        <a href="{{ route('client.itsm.permit') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:text-slate-800 transition">
                            <i data-lucide="file-badge" class="h-4.5 w-4.5"></i> Permits & Licenses
                        </a>
                        <a href="{{ route('client.itsm.risk.assessment') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-transparent pb-3.5 hover:border-slate-300 hover:text-slate-800 transition">
                            <i data-lucide="alert-triangle" class="h-4.5 w-4.5"></i> Risk Assessment
                        </a>
                        <a href="{{ route('client.itsm.document') }}" class="flex flex-1 items-center justify-center gap-2 border-b-4 border-[#132B52] pb-3.5 text-[#132B52]">
                            <i data-lucide="folder" class="h-4.5 w-4.5"></i> Documents
                        </a>
                    </div>

                    <!-- Inner Console Section -->
                    <div class="px-10 py-6 space-y-6">

                        <!-- Metrics Summary Strip -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Metric 1 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Total Stored Documents</span>
                                <span class="block text-4xl font-extrabold text-[#132B52] my-1">{{ $totalStored }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">Active audits/auditing</span>
                            </div>
                            <!-- Metric 2 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Needs Sign-Off</span>
                                <span class="block text-4xl font-extrabold text-[#132B52] my-1">{{ $needsSignOff }}</span>
                                <span class="text-[10px] font-semibold text-slate-500">Awaiting manager approval</span>
                            </div>
                            <!-- Metric 3 -->
                            <div class="rounded-2xl border border-slate-300/40 bg-white/70 p-5 text-center shadow-sm">
                                <span class="block text-sm font-bold text-slate-700">Lapsed Documents</span>
                                <span class="block text-4xl font-extrabold text-amber-600 my-1">{{ $lapsedCount }}</span>
                                <span class="text-[10px] font-semibold text-amber-600 font-medium">Require immediate re-upload</span>
                            </div>
                        </div>

                        <!-- Main Table Container (Fully Centered) -->
                        <div class="overflow-hidden rounded-2xl border border-slate-300/40 bg-white shadow-md">
                            
                            <!-- Filter & Search Action Bar -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
                                <!-- Upload Document Button -->
                                <button onclick="toggleModal(true)" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                                    <span class="text-sm leading-none">+</span> Upload Document
                                </button>
                                
                                <div class="flex items-center gap-5">
                                    <!-- Search Field Container -->
                                    <div class="relative">
                                        <form method="GET" action="{{ route('client.itsm.document') }}">
                                            @if($currentFilter !== 'All')
                                                <input type="hidden" name="filter" value="{{ $currentFilter }}">
                                            @endif
                                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                                <i data-lucide="search" class="h-4 w-4"></i>
                                            </span>
                                            <input type="text" name="search" value="{{ request('search') }}" onchange="this.form.submit()" placeholder="Search" class="w-64 rounded-full border border-slate-200 bg-slate-50 py-1.5 pl-9 pr-4 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                        </form>
                                    </div>

                                    <!-- Filter Controls Dropdown Container -->
                                    <div class="relative inline-block text-left">
                                        <button onclick="toggleFilterMenu()" class="flex items-center gap-2 text-xs font-bold text-slate-800 hover:text-slate-600 transition focus:outline-none">
                                            <i data-lucide="filter" class="h-4 w-4"></i>
                                            <span>{{ $currentFilter }}</span>
                                        </button>

                                        <!-- Dropdown Menu Elements -->
                                        <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-40 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 z-50">
                                            <div class="py-1">
                                                <a href="{{ route('client.itsm.document', ['filter' => 'All', 'search' => request('search')]) }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-medium">All</a>
                                                <a href="{{ route('client.itsm.document', ['filter' => 'Active', 'search' => request('search')]) }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-medium">Active</a>
                                                <a href="{{ route('client.itsm.document', ['filter' => 'Needs Sign-Off', 'search' => request('search')]) }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-medium">Needs Sign-Off</a>
                                                <a href="{{ route('client.itsm.document', ['filter' => 'Lapsed', 'search' => request('search')]) }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 font-medium">Lapsed</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Table View -->
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse text-center">
                                    <thead>
                                        <tr class="bg-slate-50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="inline-flex items-center justify-center gap-1.5 w-full">
                                                    <span>Document Details</span> 
                                                    
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="inline-flex items-center justify-center gap-1.5 w-full">
                                                    <span>Linked ID</span> 
                                                    
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="inline-flex items-center justify-center gap-1.5 w-full">
                                                    <span>Classification</span> 
                                                    
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="inline-flex items-center justify-center gap-1.5 w-full">
                                                    <span>Status</span> 
                                                   
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 font-semibold">
                                                <div class="inline-flex items-center justify-center gap-1.5 w-full">
                                                    <span>Actions</span> 
                                                   
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-xs">
                                        @if(count($documents) > 0)
                                            @foreach($documents as $doc)
                                                <tr class="bg-white hover:bg-slate-50/50 transition h-12 text-slate-700 font-medium">
                                                    <td class="px-6 py-4 border-b border-slate-100 text-slate-900 font-semibold text-left max-w-xs truncate">{{ $doc['details'] }}</td>
                                                    <td class="px-6 py-4 border-b border-slate-100 font-mono text-xs">{{ $doc['linked_id'] }}</td>
                                                    <td class="px-6 py-4 border-b border-slate-100">{{ $doc['classification'] }}</td>
                                                    <td class="px-6 py-4 border-b border-slate-100">
                                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                            @if($doc['status'] === 'Active') bg-emerald-50 text-emerald-700 border border-emerald-200
                                                            @elseif($doc['status'] === 'Needs Sign-Off') bg-blue-50 text-blue-700 border border-blue-200
                                                            @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                                            {{ $doc['status'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 border-b border-slate-100">
                                                        <button class="text-slate-400 hover:text-slate-600 px-1"><i data-lucide="eye" class="h-4 w-4"></i></button>
                                                        <button class="text-slate-400 hover:text-slate-600 px-1"><i data-lucide="download" class="h-4 w-4"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <!-- Empty Matrix Mid-row Placeholder Layout -->
                                            <tr class="bg-white">
                                                <td colspan="5" class="px-6 py-16 text-center border-b border-slate-100">
                                                    <div class="flex flex-col items-center justify-center space-y-2 opacity-40">
                                                        <i data-lucide="folder-open" class="h-10 w-10 text-slate-400"></i>
                                                        <p class="text-sm font-bold text-slate-500">Empty Matrix</p>
                                                        <p class="text-[11px] text-slate-400">No registered documents found matching query settings.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Creation Workspace Modal Sheet overlay -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 text-slate-900 transform transition-all">
            <div class="bg-[#DDE4EC] px-8 py-5 flex items-center justify-between border-b border-slate-200">
                <h3 class="text-lg font-bold tracking-tight text-slate-900 flex items-center gap-2">
                    <i data-lucide="folder-plus" class="h-5 w-5 text-[#132B52]"></i> Upload Document
                </h3>
                <button onclick="toggleModal(false)" class="text-slate-500 hover:text-slate-800 focus:outline-none">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            
            <form action="{{ route('client.itsm.document.store') }}" method="POST" class="p-8 space-y-5">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Document Details / Title</label>
                    <input type="text" name="details" required placeholder="e.g. ISO 27001 Compliance Audit Certification" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Linked Reference ID</label>
                        <input type="text" name="linked_id" required placeholder="e.g. DOC-842" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Classification</label>
                        <input type="text" name="classification" required placeholder="e.g. Confidential / Internal" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Initial Status Status</label>
                    <select name="status" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="Active">Active</option>
                        <option value="Needs Sign-Off">Needs Sign-Off</option>
                        <option value="Lapsed">Lapsed</option>
                    </select>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="toggleModal(false)" class="rounded-full border border-slate-200 px-5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-full bg-[#1A73E8] px-6 py-2 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition">
                        Commit Document
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Init lucide components
        lucide.createIcons();

        // Dropdown Menu Toggle Logic Handler
        function toggleFilterMenu() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Action Sheet Overlay Visibility Toggle Handler
        function toggleModal(show) {
            const modal = document.getElementById('uploadModal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        // Click outside handler to dismiss drop downs smoothly
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('filterDropdown');
            if (!e.target.closest('#filterDropdown') && !e.target.closest('button[onclick="toggleFilterMenu()"]')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>