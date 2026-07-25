@php
    $period = request()->get('period', '30d');
    if (!in_array($period, ['7d', '30d', '1y'])) $period = '30d';

    $periodDays = match($period) {
        '7d'  => 7,
        '1y'  => 365,
        default => 30,
    };

    $now    = now();
    $cutoff = $now->copy()->subDays($periodDays - 1)->startOfDay();

    $filteredOrders = collect($workOrders)->filter(function ($wo) use ($cutoff) {
        if (empty($wo['createdAt'])) return false;
        return \Carbon\Carbon::parse($wo['createdAt'])->gte($cutoff);
    })->values();

    $total     = $filteredOrders->count();
    $building  = $filteredOrders->where('status', 'Building')->count();
    $qcCheck   = $filteredOrders->where('status', 'QC Check')->count();
    $pending   = $filteredOrders->where('status', 'Pending')->count();
    $finished  = $filteredOrders->where('status', 'Finished')->count();
    $cancelled = $filteredOrders->where('status', 'Cancelled')->count();

    $qcDenom  = $finished + $cancelled;
    $qcRate   = $qcDenom > 0 ? round(($finished / $qcDenom) * 100) : 0;

    $defectCount = $filteredOrders
        ->flatMap(fn($wo) => $wo['parts'])
        ->where('status', 'Missing')
        ->count();

    $avgParts = $total > 0 ? round($filteredOrders->sum(fn($wo) => count($wo['parts'])) / $total, 1) : 0;

    $statusLabels = ['Building', 'QC Check', 'Pending', 'Finished', 'Cancelled'];
    $statusCounts = array_map(fn($s) => $filteredOrders->where('status', $s)->count(), $statusLabels);
    $statusColors = ['#D97706', '#0EA5E9', '#DC2626', '#16A34A', '#9D9D9D'];

    $assignees = $filteredOrders
        ->groupBy('assigned')
        ->map(fn($group, $name) => ['name' => $name, 'count' => $group->count()])
        ->values()
        ->sortByDesc('count')
        ->values();

    $allParts       = $filteredOrders->flatMap(fn($wo) => $wo['parts']);
    $partsReady     = $allParts->where('status', 'Ready')->count();
    $partsSourcing  = $allParts->where('status', 'Sourcing')->count();
    $partsMissing   = $allParts->where('status', 'Missing')->count();
    $partsTotal     = $allParts->count();

    $recentOrders = $filteredOrders
        ->whereIn('status', ['Finished'])
        ->take(5)
        ->values();

    // ── Dynamic chart bucketing based on selected period ──────────────────────
    $bucketLabels  = [];
    $bucketBuilds  = [];
    $bucketDefects = [];

    if ($period === '7d') {
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $bucketLabels[] = $day->format('D');
            $dayOrders = $filteredOrders->filter(fn($wo) => \Carbon\Carbon::parse($wo['createdAt'])->isSameDay($day));
            $bucketBuilds[]  = $dayOrders->where('status', 'Finished')->count();
            $bucketDefects[] = $dayOrders->where('status', 'Cancelled')->count();
        }
    } elseif ($period === '1y') {
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $bucketLabels[] = $month->format('M');
            $monthOrders = $filteredOrders->filter(fn($wo) =>
                \Carbon\Carbon::parse($wo['createdAt'])->isSameMonth($month)
                && \Carbon\Carbon::parse($wo['createdAt'])->isSameYear($month)
            );
            $bucketBuilds[]  = $monthOrders->where('status', 'Finished')->count();
            $bucketDefects[] = $monthOrders->where('status', 'Cancelled')->count();
        }
    } else {
        $weeksCount = 5;
        for ($i = $weeksCount - 1; $i >= 0; $i--) {
            $weekEnd   = $now->copy()->subWeeks($i)->endOfDay();
            $weekStart = $weekEnd->copy()->subDays(6)->startOfDay();
            $bucketLabels[] = $weekStart->format('M j') . '–' . $weekEnd->format('j');
            $weekOrders = $filteredOrders->filter(function ($wo) use ($weekStart, $weekEnd) {
                $d = \Carbon\Carbon::parse($wo['createdAt']);
                return $d->gte($weekStart) && $d->lte($weekEnd);
            });
            $bucketBuilds[]  = $weekOrders->where('status', 'Finished')->count();
            $bucketDefects[] = $weekOrders->where('status', 'Cancelled')->count();
        }
    }

    $weekLabels  = $bucketLabels;
    $weekBuilds  = $bucketBuilds;
    $weekDefects = $bucketDefects;

    $periodTabs = [
        '7d'  => '7 Days',
        '30d' => '30 Days',
        '1y'  => '1 Year',
    ];
@endphp
<div class="flex flex-col h-full">
<div class="flex items-center justify-between flex-shrink-0 mb-4">
    <h1 class="font-heading font-medium text-2xl text-nexora-deep-navy">Reports & Analytics</h1>
    <div class="flex items-center gap-1 bg-nexora-slate-200 border border-nexora-corporate/50 rounded-full p-1">
        @foreach($periodTabs as $key => $label)
            <a href="?page=reports&period={{ $key }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold transition-colors duration-150
                      {{ $period === $key
                          ? 'bg-nexora-corporate text-white'
                          : 'text-nexora-navy-mid hover:bg-nexora-corporate/10' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
<div class="flex-1 min-h-0 flex flex-col">
<div class="grid grid-cols-4 gap-3 mb-4 flex-shrink-0">
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1">Total work orders</p>
        <p class="text-3xl font-heading font-medium text-nexora-deep-navy">{{ $total }}</p>
        <p class="text-xs text-nexora-navy-mid mt-1">across all statuses</p>
    </div>
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1">QC pass rate</p>
        <p class="text-3xl font-heading font-medium {{ $qcRate >= 80 ? 'text-nexora-success' : 'text-nexora-warning' }}">{{ $qcRate }}%</p>
        <p class="text-xs text-nexora-navy-mid mt-1">{{ $finished }} finished · {{ $cancelled }} cancelled</p>
    </div>
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1">Parts with issues</p>
        <p class="text-3xl font-heading font-medium {{ $defectCount > 0 ? 'text-nexora-danger' : 'text-nexora-success' }}">{{ $defectCount }}</p>
        <p class="text-xs text-nexora-navy-mid mt-1">{{ $partsSourcing }} sourcing · {{ $partsMissing }} missing</p>
    </div>
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1">Avg parts per build</p>
        <p class="text-3xl font-heading font-medium text-nexora-deep-navy">{{ $avgParts }}</p>
        <p class="text-xs text-nexora-navy-mid mt-1">{{ $partsTotal }} parts tracked total</p>
    </div>
</div>

<div class="flex gap-3 mb-4 flex-shrink-0">

    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4 flex-1">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-1">Work orders by status</p>
        <p class="text-xs text-nexora-navy-mid mb-3">Selected period · {{ $periodTabs[$period] }}</p>
        <div class="flex flex-wrap gap-3 mb-3">
            @foreach($statusLabels as $i => $label)
                <span class="flex items-center gap-1.5 text-xs text-nexora-navy-mid">
                    <span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:{{ $statusColors[$i] }}"></span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
        <div class="relative" style="height:160px">
            <canvas id="statusChart" aria-label="Bar chart showing work order counts by status"></canvas>
        </div>
    </div>

    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4 flex-1">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-1">Builds vs defects</p>
        <p class="text-xs text-nexora-navy-mid mb-3">Completed builds and cancelled/defect orders · {{ $periodTabs[$period] }}</p>
        <div class="flex flex-wrap gap-3 mb-3">
            <span class="flex items-center gap-1.5 text-xs text-nexora-navy-mid"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#1B6FC8"></span>Builds done</span>
            <span class="flex items-center gap-1.5 text-xs text-nexora-navy-mid"><span class="inline-block w-2.5 h-2.5 rounded-sm" style="background:#DC2626"></span>Defects / cancelled</span>
        </div>
        <div class="relative" style="height:160px">
            <canvas id="weeklyChart" aria-label="Line chart of builds and defects over the selected period"></canvas>
        </div>
    </div>

    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4 pt-1 w-[250px] flex flex-col items-center justify-center">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3 self-start">Parts status</p>
        <div class="relative" style="height:130px;width:130px">
            <canvas id="partsDonut" aria-label="Donut chart showing parts by status: Ready, Sourcing, Missing"></canvas>
        </div>
        <div class="flex flex-col gap-1 mt-3 self-start w-full">
            <span class="flex items-center justify-between text-xs text-nexora-navy-mid">
                <span class="flex items-center gap-1.5"><span class="inline-block w-2 h-2 rounded-full bg-nexora-success"></span>Ready</span>
                <span class="text-nexora-deep-navy">{{ $partsReady }}</span>
            </span>
            <span class="flex items-center justify-between text-xs text-nexora-navy-mid">
                <span class="flex items-center gap-1.5"><span class="inline-block w-2 h-2 rounded-full bg-nexora-warning"></span>Sourcing</span>
                <span class="text-nexora-deep-navy">{{ $partsSourcing }}</span>
            </span>
            <span class="flex items-center justify-between text-xs text-nexora-navy-mid">
                <span class="flex items-center gap-1.5"><span class="inline-block w-2 h-2 rounded-full bg-nexora-danger"></span>Missing</span>
                <span class="text-nexora-deep-navy">{{ $partsMissing }}</span>
            </span>
        </div>
    </div>

</div>

<div class="flex gap-3 mb-4 flex-1 min-h-0">

    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4 flex-1 flex flex-col min-h-0">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3 flex-shrink-0">Recent Finished Work Orders</p>
        <div class="overflow-auto flex-1 min-h-0 [&::-webkit-scrollbar]:hidden">
            <table class="w-full text-xs table-fixed sortable-table" data-table-id="reports-recent">
                <thead class="sticky top-0 bg-nexora-slate-200">
                    <tr class="border-b border-nexora-corporate/30">
                        <th class="text-left text-nexora-navy-mid font-medium pb-2 w-28 sortable" data-sort-type="text">Order ID</th>
                        <th class="text-left text-nexora-navy-mid font-medium pb-2 sortable" data-sort-type="text">Build</th>
                        <th class="text-left text-nexora-navy-mid font-medium pb-2 w-24 sortable" data-sort-type="text">Assigned</th>
                        <th class="text-left text-nexora-navy-mid font-medium pb-2 w-20 sortable" data-sort-type="number">Parts OK</th>
                        <th class="text-left text-nexora-navy-mid font-medium pb-2 w-24 sortable" data-sort-type="text">Status</th>
                        <th class="text-left text-nexora-navy-mid font-medium pb-2 w-28 sortable" data-sort-type="text">Due / Done</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-nexora-corporate/30">
                    @forelse($recentOrders as $wo)
                        @php
                            $woReady   = collect($wo['parts'])->where('status', 'Ready')->count();
                            $woTotal   = count($wo['parts']);
                            $allReady  = $woReady === $woTotal;
                            $pill      = $statusStyles[$wo['status']]['pill'] ?? 'bg-nexora-gray/80 text-nexora-off-white';
                        @endphp
                        <tr>
                            <td class="py-2 font-mono text-nexora-navy-mid" data-sort-value="{{ $wo['id'] }}">{{ $wo['id'] }}</td>
                            <td class="py-2 text-nexora-deep-navy truncate pr-2" data-sort-value="{{ $wo['name'] }}">{{ $wo['name'] }}</td>
                            <td class="py-2 text-nexora-navy-mid" data-sort-value="{{ $wo['assigned'] }}">{{ $wo['assigned'] }}</td>
                            <td class="py-2 {{ $allReady ? 'text-nexora-success' : 'text-nexora-warning' }}" data-sort-value="{{ $woReady }}">
                                {{ $woReady }}/{{ $woTotal }}
                            </td>
                            <td class="py-2" data-sort-value="{{ $wo['status'] }}">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $pill }}">{{ $wo['status'] }}</span>
                            </td>
                            <td class="py-2 text-nexora-navy-mid" data-sort-value="{{ $wo['due'] }}">{{ $wo['due'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-nexora-navy-mid">No finished orders in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4 w-[250px] flex flex-col min-h-0">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3 flex-shrink-0">Orders per technician</p>
        <div class="overflow-auto flex-1 min-h-0 [&::-webkit-scrollbar]:hidden flex flex-wrap gap-6 content-start">
            @forelse($assignees as $a)
                @php $pct = $total > 0 ? round(($a['count'] / $total) * 100) : 0; @endphp
                <div class="flex items-center gap-3 min-w-[180px]">
                    <div class="w-8 h-8 rounded-full bg-nexora-corporate flex items-center justify-center text-xs font-medium text-nexora-deep-navy flex-shrink-0">
                        {{ strtoupper(substr($a['name'], 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs text-nexora-deep-navy">{{ $a['name'] }}</span>
                            <span class="text-xs text-nexora-navy-mid">{{ $a['count'] }}</span>
                        </div>
                        <div class="h-1.5 bg-nexora-slate-500/20 rounded-full overflow-hidden w-[180px]">
                            <div class="h-full bg-nexora-corporate rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-nexora-navy-mid">No data for this period.</p>
            @endforelse
        </div>
    </div>
</div>
</div>
</div>
<script>
    window.reportsData = {
        statusLabels: @json($statusLabels),
        statusCounts: @json($statusCounts),
        statusColors: @json($statusColors),
        weekLabels:   @json($weekLabels),
        weekBuilds:   @json($weekBuilds),
        weekDefects:  @json($weekDefects),
        partsReady:   {{ $partsReady }},
        partsSourcing: {{ $partsSourcing }},
        partsMissing: {{ $partsMissing }}
    };
</script>
<script src="{{ asset('manufacturing/js/reports-charts.js') }}"></script>

<script>initSortableTables();</script>
