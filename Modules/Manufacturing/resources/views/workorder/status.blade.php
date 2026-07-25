@php

$selectedIndex = (int) request()->get('order', 0);
$selectedOrder = $workOrders[$selectedIndex] ?? $workOrders[0];

@endphp

<div class="flex gap-2 h-full">

    {{-- LEFT COLUMN --}}
    <div class="w-[60%] flex-shrink-0 flex flex-col gap-2">
        <div class="flex items-center gap-4">
            <h1 class="font-heading font-medium text-xl text-nexora-navy-mid whitespace-nowrap">
                {{ strtoupper($subName) }}
            </h1>
            <div class="relative flex-1 max-w-[50%]">
                <span class="inline-block" aria-hidden="true">&#8226;</span>
                <input type="text" placeholder="Search"
                        id="search-input"
                        oninput="filterOrders(currentFilter)"
                        class="w-full pl-8 pr-3 py-1.5 rounded-md bg-nexora-steel-blue/50 text-nexora-deep-navy
                              text-xs placeholder-nexora-navy/50 border border-nexora-corporate
                              focus:outline-none focus:border-nexora-deep-navy">
            </div>
            <div class="flex gap-2 mb-3" id="filter-bar">
                <button data-filter="all"
                        onclick="filterOrders('all')"
                        class="filter-btn px-3 py-1 rounded-full text-xs border border-nexora-corporate
                            bg-nexora-corporate text-white transition-colors duration-150">
                    All
                </button>
                <button data-filter="Building"
                        onclick="filterOrders('Building')"
                        class="filter-btn px-3 py-1 rounded-full text-xs border border-nexora-corporate
                            text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                    Building
                </button>
                <button data-filter="Finished"
                        onclick="filterOrders('Finished')"
                        class="filter-btn px-3 py-1 rounded-full text-xs border border-nexora-corporate
                            text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                    Finished
                </button>
            </div>
        </div>

        {{-- Card list --}}
        <div class="flex-1 rounded-lg bg-nexora-slate-200 border border-nexora-corporate/50
                    px-1 py-3 overflow-y-auto [&::-webkit-scrollbar]:hidden">
            @foreach($workOrders as $i => $order)
            @if (strtolower($order['status']) != 'qc check' && strtolower($order['status']) != 'cancelled' && strtolower($order['assigned']) != 'unassigned')
                @php
                    $style    = $statusStyles[$order['status']] ?? ['pill' => 'bg-gray-400 text-white'];
                    $isActive = $i === $selectedIndex;
                @endphp
                <a id="card-{{ $i }}"
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
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $style['pill'] }}">
                                {{ $order['status'] }}
                            </span>
                            <span class="text-[10px] text-nexora-navy-mid">{{ $order['due'] }}</span>
                        </div>
                    </div>
                </a>
            @endif
            @endforeach
        </div>

    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex-1 h-full bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl overflow-y-auto [&::-webkit-scrollbar]:hidden text-nexora-deep-navy">

    @foreach($workOrders as $i => $order)
        @php
            $selStyle      = $statusStyles[$order['status']] ?? ['pill' => 'bg-gray-300 text-gray-800'];
            $Total        = count($order['parts']);
            $Ready        = collect($order['parts'])->where('status', 'Ready')->count();
            $Sourcing     = collect($order['parts'])->where('status', 'Sourcing')->count();
            $Missing      = collect($order['parts'])->where('status', 'Missing')->count();
            $Pct          = $Total > 0 ? round(($Ready / $Total) * 100) : 0;
        @endphp

        <div id="detail-{{ $i }}" class="p-5 {{ $i === 0 ? '' : 'hidden' }}">
            <div class="flex justify-between">
                <span class="text-xs text-nexora-navy mb-1">{{ $order['id'] }} &bull; {{ $order['source'] }}</span>
                <button onclick="openEditModal({{ $i }})" class="px-3 rounded-full font-medium text-base text-nexora-deep-navy bg-nexora-steel-blue border border-nexora-deep-navy 
                        hover:border-nexora-corporate hover:text-nexora-off-white transition-colors whitespace-nowrap">Edit</button>
            </div>
            <h2 class="text-2xl font-bold mb-2">{{ $order['name'] }}</h2>
            <div class="flex items-center gap-2 mb-5">
                <span class="px-2.5 py-1.5 rounded-full text-xs font-bold {{ $selStyle['pill'] }}">
                    {{ $order['status'] }}
                </span>
                <span class="text-xs text-nexora-navy">Assigned: {{ $order['assigned'] }}</span>
            </div>

            <div class="grid grid-cols-4 gap-3 mb-4">
                @foreach([
                    ['label' => 'Total parts', 'value' => $Total,    'color' => 'text-nexora-deep-navy'],
                    ['label' => 'Ready',        'value' => $Ready,    'color' => 'text-nexora-success'],
                    ['label' => 'Sourcing',     'value' => $Sourcing, 'color' => 'text-nexora-warning'],
                    ['label' => 'Missing',      'value' => $Missing,  'color' => 'text-nexora-danger'],
                ] as $stat)
                    <div class="bg-nexora-slate-500/20 border border-nexora-corporate rounded-lg p-3 text-center">
                        <p class="text-2xl font-medium {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                        <p class="text-[10px] text-nexora-navy-mid mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mb-1 flex justify-between text-xs text-nexora-navy-mid">
                <span>Parts ready</span>
                <span>{{ $Ready }} / {{ $Total }} ({{ $Pct }}%)</span>
            </div>
            <div class="w-full bg-nexora-navy-mid/60 rounded-full h-2 mb-2">
                <div class="h-2 rounded-full bg-nexora-success transition-all duration-500"
                     style="width: {{ $Pct }}%"></div>
            </div>

            <div class="flex items-center gap-4 text-xs text-nexora-navy-mid mb-5">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-nexora-success/80 inline-block"></span> Ready</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-nexora-warning/80 inline-block"></span> Sourcing</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-nexora-danger/80 inline-block"></span> Missing</span>
            </div>

            <p class="text-xs font-semibold tracking-widest text-nexora-deep-navy uppercase mb-3">Parts for this build</p>
            <div class="flex flex-col gap-1">
                @foreach($order['parts'] as $part)
                    @php $ps = $partStyles[$part['status']] ?? ['dot' => 'bg-gray-400', 'text' => 'text-gray-400']; @endphp
                    <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-nexora-slate-500/20 hover:bg-nexora-steel-blue/60 transition-colors">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $ps['dot'] }}"></span>
                            <span class="text-sm text-nexora-deep-navy font-medium">{{ $part['category']}} -></span>
                            <span class="text-xs text-nexora-deep-navy">{{ $part['name'] }}</span>
                        </div>
                        <span class="text-xs font-medium {{ $ps['text'] }}">{{ $part['status'] }}</span>
                    </div>
                @endforeach
            </div>

        </div>
    @endforeach
    <script>
    const workOrdersData = @json($workOrders);
    </script>
</div>

<script>initRowAnimations();</script>
