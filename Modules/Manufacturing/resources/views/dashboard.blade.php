@php
    $total      = count($workOrders);
    $building   = collect($workOrders)->where('status', 'Building')->count();
    $qcCheck    = collect($workOrders)->where('status', 'QC Check')->count();
    $pending    = collect($workOrders)->where('status', 'Pending')->count();
    $finished   = collect($workOrders)->where('status', 'Finished')->count();
    $cancelled  = collect($workOrders)->where('status', 'Cancelled')->count();
    $active     = $building + $qcCheck + $pending;

    $overdue = collect($workOrders)
        ->filter(fn($o) => str_starts_with($o['due'], 'Due') && $o['status'] === 'Cancelled')
        ->count();

    $qcDenom   = $finished + $cancelled;
    $qcRate    = $qcDenom > 0 ? round(($finished / $qcDenom) * 100) : 0;

    $partIssues = [];
    foreach ($workOrders as $wo) {
        foreach ($wo['parts'] as $part) {
            if (in_array($part['status'], ['Sourcing', 'Missing'])) {
                $key = $part['name'];
                if (!isset($partIssues[$key])) {
                    $partIssues[$key] = ['name' => $part['name'], 'status' => $part['status'], 'count' => 0];
                }
                $partIssues[$key]['count']++;
                if ($part['status'] === 'Missing') {
                    $partIssues[$key]['status'] = 'Missing';
                }
            }
        }
    }
    usort($partIssues, fn($a, $b) =>
        ($b['status'] === 'Missing' ? 1 : 0) - ($a['status'] === 'Missing' ? 1 : 0)
        ?: $b['count'] - $a['count']
    );
    $topPartIssues = array_slice(array_values($partIssues), 0, 6);

    $activeOrders = collect($workOrders)
        ->whereIn('status', ['Building', 'QC Check', 'Pending', 'Cancelled'])
        ->take(5)
        ->values();

    $alerts = [];
    $missingParts = collect($workOrders)
        ->flatMap(fn($wo) => collect($wo['parts'])->where('status', 'Missing')->map(fn($p) => $p['name']))
        ->unique()->take(2)->values();
    if ($missingParts->count()) {
        $alerts[] = ['type' => 'danger', 'icon' => 'alert-circle',
            'title' => $missingParts->count() . ' part(s) missing across orders',
            'sub'   => $missingParts->implode(' · ')];
    }
    if ($overdue > 0) {
        $overdueIds = collect($workOrders)
            ->filter(fn($o) => str_starts_with($o['due'], 'Due') && $o['status'] === 'Cancelled')
            ->pluck('id')->implode(' · ');
        $alerts[] = ['type' => 'warning', 'icon' => 'clock',
            'title' => $overdue . ' order(s) cancelled / overdue',
            'sub'   => $overdueIds];
    }
    $qcWarns = collect($workOrders)->where('status', 'QC Check')->count();
    if ($qcWarns > 0) {
        $alerts[] = ['type' => 'warning', 'icon' => 'shield-x',
            'title' => $qcWarns . ' build(s) currently in QC',
            'sub'   => 'Review benchmark results before marking done'];
    }
    $newOrders = collect($workOrders)->where('status', 'Pending')->count();
    if ($newOrders > 0) {
        $alerts[] = ['type' => 'info', 'icon' => 'truck-delivery',
            'title' => $newOrders . ' pending order(s) from e-commerce',
            'sub'   => 'Added to queue · ready to assign'];
    }

    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $weekCounts = [4, 6, 5, 8, 7, 3, $finished];
@endphp

<div class="grid grid-cols-4 gap-3 mb-4">

    {{-- Active Orders --}}
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1 flex items-center gap-1">
            <span class="inline-block" aria-hidden="true">&#8226;</span>
            Active Work Orders
        </p>
        <p class="text-3xl font-heading font-medium text-nexora-deep-navy">{{ $active }}</p>
        <p class="text-xs text-nexora-navy-mid mt-1">{{ $building }} building · {{ $qcCheck }} in QC · {{ $pending }} queued</p>
    </div>

    {{-- Completed --}}
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1 flex items-center gap-1">
           <span class="inline-block" aria-hidden="true">&#8226;</span>
            Completed Builds
        </p>
        <p class="text-3xl font-heading font-medium text-nexora-success">{{ $finished }}</p>
        <p class="text-xs text-nexora-navy-mid mt-1">out of {{ $total }} total orders</p>
    </div>

    {{-- Overdue --}}
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1 flex items-center gap-1">
            <span class="inline-block" aria-hidden="true">&#8226;</span>
            Cancelled / Overdue
        </p>
        <p class="text-3xl font-heading font-medium {{ $overdue > 0 ? 'text-nexora-danger' : 'text-nexora-deep-navy' }}">{{ $overdue }}</p>
        <p class="text-xs {{ $overdue > 0 ? 'text-nexora-danger' : 'text-nexora-navy-mid' }} mt-1">
            {{ $overdue > 0 ? 'Needs attention' : 'All on track' }}
        </p>
    </div>

    {{-- QC Pass Rate --}}
    <div class="bg-nexora-slate-200 rounded-xl px-4 py-3 border border-nexora-corporate/50">
        <p class="text-xs text-nexora-navy-mid mb-1 flex items-center gap-1">
            <span class="inline-block" aria-hidden="true">&#8226;</span>
            QC Pass rate
        </p>
        <p class="text-3xl font-heading font-medium {{ $qcRate >= 80 ? 'text-nexora-success' : 'text-nexora-warning' }}">{{ $qcRate }}%</p>
        <p class="text-xs text-nexora-navy-mid mt-1">{{ $finished }} passed · {{ $cancelled }} failed / cancelled</p>
    </div>
</div>
<div class="grid grid-cols-[1fr_280px] gap-3 mb-4">

    {{-- Active Orders List --}}
    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3">Active work orders</p>
        <div class="flex flex-col divide-y divide-nexora-corporate/30">
            @foreach($activeOrders as $wo)
                @php
                    $pill = $statusStyles[$wo['status']]['pill'] ?? 'bg-nexora-gray/80 text-nexora-off-white';
                    $isOverdue = str_starts_with($wo['due'], 'Due') && $wo['status'] === 'Cancelled';
                @endphp
                <div class="flex items-center gap-3 p-2.5 hover:bg-nexora-steel-blue transition duration-300">
                    {{-- Icon --}}
                    <div class="w-8 h-8 rounded-lg bg-nexora-slate-500/20 flex items-center justify-center flex-shrink-0">
                        <span class="inline-block" aria-hidden="true">&#8226;</span>
                    </div>
                    {{-- Meta --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-nexora-deep-navy truncate">{{ $wo['name'] }}</p>
                        <p class="text-xs text-nexora-navy-mid font-mono">{{ $wo['id'] }}</p>
                    </div>
                    {{-- Right --}}
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $pill }}">{{ $wo['status'] }}</span>
                        <span class="text-xs {{ $isOverdue ? 'text-nexora-danger' : 'text-nexora-navy-mid' }}">
                            {{ $wo['due'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        <a href="?page=orders&sub=all"
           class="mt-3 block text-center text-xs text-nexora-corporate hover:text-nexora-corporate transition-colors duration-150">
            View all work orders →
        </a>
    </div>

    {{-- Alerts --}}
    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3">
            Alerts
            <span class="ml-1 px-1.5 py-0.5 rounded-full bg-nexora-danger/20 text-nexora-danger text-xs">{{ count($alerts) }}</span>
        </p>
        <div class="flex flex-col gap-2">
            @foreach($alerts as $alert)
                @php
                    $alertBg = match($alert['type']) {
                        'danger'  => 'bg-nexora-danger/10 border-nexora-danger/40',
                        'warning' => 'bg-nexora-warning/10 border-nexora-warning/40',
                        'info'    => 'bg-nexora-info/10 border-nexora-info/40',
                        default   => 'bg-nexora-slate-500/20 border-nexora-corporate/30',
                    };
                    $alertIcon = match($alert['type']) {
                        'danger'  => 'text-nexora-danger',
                        'warning' => 'text-nexora-warning',
                        'info'    => 'text-nexora-info',
                        default   => 'text-nexora-navy-mid',
                    };
                @endphp
                <div class="rounded-lg border px-3 py-2 {{ $alertBg }}">
                    <div class="flex items-start gap-2">
                        @if($alert['icon'] === 'alert-circle')
                            <span class="inline-block" aria-hidden="true">&#8226;</span>
                        @elseif($alert['icon'] === 'clock')
                            <span class="inline-block" aria-hidden="true">&#8226;</span>
                        @elseif($alert['icon'] === 'shield-x')
                            <span class="inline-block" aria-hidden="true">&#8226;</span>
                        @else
                            <span class="inline-block" aria-hidden="true">&#8226;</span>
                        @endif
                        <div>
                            <p class="text-xs font-medium text-nexora-deep-navy">{{ $alert['title'] }}</p>
                            <p class="text-xs text-nexora-navy-mid mt-0.5">{{ $alert['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="grid grid-cols-2 gap-3">

    {{-- Parts with Issues --}}
    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3">Parts needing attention</p>
        <div class="flex flex-col gap-2">
            @forelse($topPartIssues as $part)
                @php
                    $isMissing  = $part['status'] === 'Missing';
                    $barColor   = $isMissing ? 'bg-nexora-danger' : 'bg-nexora-warning';
                    $textColor  = $isMissing ? 'text-nexora-danger' : 'text-nexora-warning';
                    $barWidth   = $isMissing ? 'w-1/12' : 'w-3/12';
                @endphp
                <div class="flex items-center gap-2">
                    <span class="text-xs text-nexora-navy-mid w-40 truncate flex-shrink-0" title="{{ $part['name'] }}">{{ $part['name'] }}</span>
                    <div class="flex-1 h-1.5 bg-nexora-slate-500/20 rounded-full overflow-hidden">
                        <div class="{{ $barColor }} {{ $barWidth }} h-full rounded-full"></div>
                    </div>
                    <span class="text-xs {{ $textColor }} w-16 text-right flex-shrink-0">{{ $part['status'] }} ×{{ $part['count'] }}</span>
                </div>
            @empty
                <p class="text-xs text-nexora-navy-mid">All parts are accounted for.</p>
            @endforelse
        </div>
        <p class="text-xs text-nexora-navy-mid mt-3">
            Showing parts marked <span class="text-nexora-danger">Missing</span> or <span class="text-nexora-warning">Sourcing</span> across active orders.
        </p>
    </div>

    {{-- Weekly Builds Chart --}}
    <div class="bg-nexora-slate-200 rounded-xl border border-nexora-corporate/50 p-4">
        <p class="text-xs font-medium text-nexora-slate-500 uppercase tracking-wider mb-3">Builds completed this week</p>
        <div class="relative" style="height:140px">
            <canvas id="dashWeekChart" aria-label="Bar chart of builds completed per day this week"></canvas>
        </div>
    </div>
</div>

<script>
    window.dashboardData = {
        days: @json($days),
        weekCounts: @json($weekCounts)
    };
</script>
<script src="{{ asset('manufacturing/js/dashboard-charts.js') }}"></script>
