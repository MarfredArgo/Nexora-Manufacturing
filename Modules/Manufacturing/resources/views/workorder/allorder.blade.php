<div class="flex flex-col h-full">
    <div class="flex items-center gap-4">
        <h1 class="font-heading font-medium text-xl text-nexora-navy-mid whitespace-nowrap">
            {{ strtoupper($subName) }}
        </h1>
        <div class="relative flex-1">
            <span class="inline-block" aria-hidden="true">&#8226;</span>
        <input type="text" placeholder="Search"
                id="search-input"
                oninput="filterOrders(currentFilter)"
                class="pl-8 pr-3 py-1.5 min-w-[300px] rounded-md bg-nexora-steel-blue/50 text-nexora-deep-navy 
                text-xs placeholder-nexora-navy/50 border border-nexora-corporate focus:outline-none focus:border-nexora-deep-navy">
        </div>
                <div class="flex gap-2 my-2" id="filter-bar">
                    <button data-filter="all"
                            onclick="filterOrders('all')"
                            class="filter-btn py-1 px-2 rounded-full text-xs border border-nexora-corporate
                                bg-nexora-corporate text-white transition-colors duration-150">
                        All
                    </button>
                    <button data-filter="Building"
                            onclick="filterOrders('Building')"
                            class="filter-btn py-1 px-2 rounded-full text-xs border border-nexora-corporate
                                text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                        Building
                    </button>
                    <button data-filter="Pending"
                            onclick="filterOrders('Pending')"
                            class="filter-btn py-1 px-2 rounded-full text-xs border border-nexora-corporate
                                text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                        Pending
                    </button>
                    <button data-filter="Finished"
                            onclick="filterOrders('Finished')"
                            class="filter-btn py-1 px-2 rounded-full text-xs border border-nexora-corporate
                                text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                        Finished
                    </button>
                    <button data-filter="QC Check"
                            onclick="filterOrders('QC Check')"
                            class="filter-btn py-1 px-2 rounded-full text-xs border border-nexora-corporate
                                text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                        QC Check
                    </button>
                    <button data-filter="Cancelled"
                            onclick="filterOrders('Cancelled')"
                            class="filter-btn py-1 px-2 rounded-full text-xs border border-nexora-corporate
                                text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors duration-150">
                        Cancelled
                    </button>
                </div>
    </div>
    <div class="rounded-xl h-full bg-nexora-slate-200 overflow-auto [&::-webkit-scrollbar]:hidden mt-8 p-4 pt-0">
        <table class="w-full text-xs sortable-table" data-table-id="allorder">
            <thead class="z-10 sticky top-0 bg-nexora-slate-200">
                <tr>
                    <th class="text-left px-4 py-2.5 text-nexora-deep-navy font-medium text-base sortable" data-sort-type="text">Build Name</th>
                    <th class="text-left px-4 py-2.5 text-nexora-deep-navy font-medium text-base sortable" data-sort-type="text">Build ID</th>
                    <th class="text-left px-4 py-2.5 text-nexora-deep-navy font-medium text-base sortable" data-sort-type="text">Build Specs</th>
                    <th class="text-left px-4 py-2.5 text-nexora-deep-navy font-medium text-base sortable" data-sort-type="text">Scheduled date</th>
                    <th class="text-left px-4 py-2.5 text-nexora-deep-navy font-medium text-base sortable" data-sort-type="text">Status</th>
                </tr>
            </thead>
            <tbody>

                @foreach($workOrders as $order)
                    @php
                        $style    = $statusStyles[$order['status']] ?? ['pill' => 'bg-nexora-gray text-white', 'dot' => 'bg-off-white'];
                    @endphp
                    <tr class="border-b border-nexora-slate-200 hover:bg-nexora-slate-500/20 transition-colors duration-300 row-animate"
                        data-index="{{ $loop->index }}"
                        id="card-{{ $loop->index }}"
                        data-status="{{ $order['status'] }}"
                        data-name="{{ $order['name'] }}">
                        <td class="px-4 py-2.5 text-black" data-sort-value="{{ $order['name'] }}">{{ $order['name'] }}</td>
                        <td class="px-4 py-2.5 text-black font-['Courier_New']" data-sort-value="{{ $order['id'] }}">{{ $order['id'] }}</td>
                        <td class="px-4 py-2.5 text-black" data-sort-value="{{ $order['specs'] }}">{{ $order['specs'] }}</td>
                        <td class="px-4 py-2.5 text-black" data-sort-value="{{ $order['due'] }}">{{ $order['due'] }}</td>
                        <td class="px-4 py-2.5" data-sort-value="{{ $order['status'] }}">
                            <span class="px-3 py-1 rounded-full text-xs font-black {{ $style['pill'] }}">
                                {{ $order['status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>initRowAnimations();</script>

<script>initSortableTables();</script>
