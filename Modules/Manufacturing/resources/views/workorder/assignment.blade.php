@php
$selectedIndex = (int) request()->get('order', -1);
$workerName = request()->get('name','');
$workerRole = request()->get('role','');
@endphp

<div class="flex gap-2 h-[calc(100vh-10rem)]">
    {{-- Left Column: Work Orders --}}
    <div class="w-[32%] flex-shrink-0 flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <h2 class="font-heading font-medium text-xl text-nexora-navy-mid whitespace-nowrap">
                Work Orders
            </h2>
        </div>

        <div class="relative">
            <span class="inline-block" aria-hidden="true">&#8226;</span>
            <input type="text" id="searchWO" placeholder="Search orders..." oninput="filterAssignmentSearch()"
                   class="w-full pl-8 pr-3 py-1.5 rounded-md bg-nexora-steel-blue/50 text-nexora-deep-navy text-xs placeholder-nexora-navy/50 border border-nexora-corporate focus:outline-none focus:border-nexora-deep-navy">
        </div>
        <div class="flex-1 rounded-lg bg-nexora-slate-200 border border-nexora-corporate/50 px-1 py-3 overflow-y-auto [&::-webkit-scrollbar]:hidden">
            @foreach($workOrders as $i => $order)
                @if ($order['assigned']=='Unassigned')
                    @php
                        $style = $statusStyles[$order['status']] ?? ['pill' => 'bg-gray-400 text-white'];
                        $isActive = $i === $selectedIndex;
                    @endphp
                    <a id="card-{{ $i }}" 
                         data-index="{{ $i }}" 
                         data-status="{{ $order['status'] }}" 
                         data-name="{{ $order['name'] }}"
                         onclick="showOrder({{ $i }})"
                         class="block px-3 py-2.5 mb-1.5 cursor-pointer transition-all duration-150 row-animate
                             {{ $isActive
                                  ? 'bg-nexora-steel-blue/80'
                                  : 'hover:shadow-md hover:-translate-y-[2px] hover:bg-nexora-steel-blue/50' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[10px] text-nexora-navy mb-0.5">{{ $order['id'] }}</p>
                                <p class="text-sm font-semibold text-nexora-deep-navy truncate">{{ $order['name'] }}</p>
                                <p class="text-[10px] text-nexora-navy mt-0.5 truncate">{{ $order['specs'] }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $style['pill'] }}">{{ $order['status'] }}</span>
                                <span class="text-[10px] text-nexora-navy-mid">{{ $order['due'] }}</span>
                            </div>
                        </div>
                    </a>                
                @endif
            @endforeach
        </div>
    </div>

    {{-- Right Panel --}}
    <div id="right-panel" class="w-[65%] relative flex-shrink-0 bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl overflow-hidden text-nexora-deep-navy">
        <div id="worker-management" class="p-5 h-full overflow-y-auto">

            {{-- Shown when an order is selected --}}
            <div id="assignment-banner" class="hidden mb-4 p-3 rounded-lg bg-nexora-corporate/10 border border-nexora-corporate/40 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs text-nexora-slate-500">Assigning a worker to</p>
                    <p class="text-sm font-semibold truncate">
                        <span id="assignment-order-id" class="font-mono text-xs text-nexora-slate-500"></span>
                        <span id="assignment-order-name"></span>
                    </p>
                </div>
                <div class="flex-shrink-0 flex items-center gap-2">
                    <button id="confirm-assign-btn" onclick="confirmAssignment()" disabled
                            class="hidden px-3 py-1.5 rounded bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirm Assignment
                    </button>
                    <button onclick="cancelOrderSelection()" class="px-3 py-1.5 rounded bg-gray-200 text-gray-700 text-xs hover:bg-gray-300 transition-colors">Cancel</button>
                </div>
            </div>

            {{-- Default header, hidden while assigning --}}
            <div id="worker-mgmt-header">
                <h3 class="text-xl font-bold mb-1">Production Staff</h3>
                <p class="text-sm text-nexora-slate-500 mb-4">Active employees from HR</p>
            </div>

            <p id="assign-instructions" class="hidden text-sm text-nexora-slate-500 mb-4">Click a worker to select them, then hit Confirm Assignment</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 overflow-y-auto [&::-webkit-scrollbar]:hidden">
                @foreach($workers as $i => $worker)
                    <div id="worker-card-{{ $i }}"
                         class="worker-item p-3 rounded-lg bg-white border border-nexora-corporate/30 hover:shadow-md hover:border-nexora-corporate/60 cursor-pointer transition-all"
                         onclick="handleWorkerCardClick({{ $i }})">
                        <p class="text-sm font-semibold">{{ $worker['name'] }}</p>
                        <p class="text-xs text-nexora-slate-500">{{ $worker['role'] }}</p>
                        @if(!empty($worker['notes']))
                            <p class="text-xs text-nexora-slate-400 mt-1 line-clamp-2">{{ $worker['notes'] }}</p>
                        @endif
                    </div>
                @endforeach
                @if(empty($workers))
                    <p class="text-sm text-nexora-slate-500 italic col-span-2 text-center py-8">No active Production Management staff are available in HR.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modals & Scripts --}}
<script>
const workOrdersData = @json($workOrders);
const workersData = @json($workers);
const CURRENT_SELECTED = {{ $selectedIndex ?? -1 }};
</script>

<script>initRowAnimations();</script>
