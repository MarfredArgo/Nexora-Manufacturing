@php
    $benchmarkTargets = $tempData['benchmarkTargets'] ?? [];
    $qcSessions       = collect($tempData['qcSessions'] ?? []);
    $rangeStyles      = $tempData['rangeStyles'] ?? [];

    $rangeToKey = [
        'high-end'  => 'HE',
        'mid-range' => 'MR',
        'budget'    => 'BU',
        'office'    => 'OF',
    ];

    $qcOrders    = collect($workOrders)->where('status', 'QC Check')->values();
    $selectedIdx = (int) request()->get('qcorder', 0);
    $selectedOrder = $qcOrders[$selectedIdx] ?? $qcOrders[0] ?? null;

    $range     = $selectedOrder['range'] ?? 'mid-range';
    $rangeKey  = $rangeToKey[$range] ?? 'MR';
    $checksMap = $benchmarkTargets[$rangeKey] ?? [];

    // Turn the assoc map into an indexed list with checkId attached, grouped implicitly by category prefix
    $checks = collect($checksMap)->map(function ($def, $checkId) {
        [$category] = explode('_', $checkId, 2);
        return array_merge($def, ['id' => $checkId, 'category' => $category]);
    })->values();

    $session = $qcSessions->firstWhere('woId', $selectedOrder['id'] ?? '');
    $results = collect($session['results'] ?? []);

    $totalChecks = $checks->count();
    $passCount   = $results->where('verdict', 'Pass')->count();
    $warnCount   = $results->where('verdict', 'Warn')->count();
    $failCount   = $results->where('verdict', 'Fail')->count();
    $doneCount   = $results->filter(fn($r) => $r['verdict'] !== '')->count();
    $pct         = $totalChecks > 0 ? round(($doneCount / $totalChecks) * 100) : 0;

    $checkMap = $checks->keyBy('id');
    $flagged  = $results->filter(fn($r) => in_array($r['verdict'], ['Warn','Fail']) && $r['note'] !== '');

    $rangePill = $rangeStyles[$range] ?? 'bg-nexora-slate-500/80 text-white';
@endphp

<div class="flex gap-3 h-full">

    {{-- ── LEFT: QC order picker ─────────────────────────────────────────────--}}
    <div class="w-44 flex-shrink-0 flex flex-col gap-2">
        <h1 class="font-heading font-medium text-xl text-nexora-navy-mid whitespace-nowrap">BENCHMARK</h1>

        <div class="flex-1 rounded-lg bg-nexora-slate-200 border border-nexora-corporate/50
                    px-1 py-3 overflow-y-auto [&::-webkit-scrollbar]:hidden">
            @forelse($qcOrders as $i => $order)
                @php
                    $sess    = $qcSessions->firstWhere('woId', $order['id']);
                    $res     = collect($sess['results'] ?? []);
                    $hasFail = $res->where('verdict', 'Fail')->count() > 0;
                    $hasWarn = $res->where('verdict', 'Warn')->count() > 0;
                    $dot     = $hasFail ? 'bg-nexora-danger' : ($hasWarn ? 'bg-nexora-warning' : 'bg-nexora-success');
                    $isActive = $i === $selectedIdx;
                @endphp
                <a href="?page=qc&sub=benchmark&qcorder={{ $i }}"
                   class="block px-3 py-2.5 mb-1 rounded-md cursor-pointer transition-all duration-150
                          {{ $isActive
                              ? 'bg-nexora-steel-blue/80'
                              : 'hover:shadow-md hover:-translate-y-[2px] hover:bg-nexora-steel-blue/50' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] text-nexora-navy mb-0.5 font-['Courier_New']">{{ $order['id'] }}</p>
                            <p class="text-xs font-semibold text-nexora-deep-navy truncate">{{ $order['name'] }}</p>
                            <p class="text-[10px] text-nexora-navy-mid mt-0.5">{{ $order['assigned'] }}</p>
                        </div>
                        <span class="w-2 h-2 rounded-full flex-shrink-0 mt-1 {{ $dot }}"></span>
                    </div>
                </a>
            @empty
                <p class="text-xs text-nexora-navy-mid px-3 py-2">No orders in QC check.</p>
            @endforelse
        </div>
    </div>

    {{-- ── RIGHT: Checklist + Side panel ────────────────────────────────────--}}
    @if($selectedOrder)
    <div class="flex flex-1 gap-3 min-w-0">

        {{-- Checklist table --}}
        <div class="flex flex-col gap-2 flex-wrap">

            {{-- Order header --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] text-nexora-navy font-['Courier_New']">
                        {{ $selectedOrder['id'] }} &bull; {{ $selectedOrder['source'] }}
                    </p>
                    <h2 class="text-xl font-bold text-nexora-deep-navy leading-tight">{{ $selectedOrder['name'] }}</h2>
                    <p class="text-xs text-nexora-navy-mid mt-0.5">
                        {{ $selectedOrder['specs'] }} &bull; Tech: {{ $selectedOrder['assigned'] }}
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $rangePill }} whitespace-nowrap capitalize">
                        {{ str_replace('-', ' ', $range) }}
                    </span>
                    <button onclick="openBenchmarkModal()"
                            class="px-4 py-1.5 rounded-full text-xs font-semibold
                                border border-nexora-corporate bg-nexora-corporate text-white
                                hover:bg-nexora-navy-mid transition-colors duration-150 whitespace-nowrap">
                        Enter Results
                    </button>
                </div>
            </div>

            {{-- Pills + progress bar --}}
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5 flex-shrink-0">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-success/80 text-nexora-off-white whitespace-nowrap">
                        {{ $passCount }} Pass
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-warning/80 text-nexora-off-white whitespace-nowrap">
                        {{ $warnCount }} Warn
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-nexora-danger/80 text-nexora-off-white whitespace-nowrap">
                        {{ $failCount }} Fail
                    </span>
                </div>
                <div class="flex-1 h-1.5 bg-nexora-slate-500/20 rounded-full overflow-hidden">
                    <div class="h-full bg-nexora-corporate rounded-full transition-all duration-300"
                        style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-[10px] text-nexora-navy-mid flex-shrink-0 whitespace-nowrap">
                    {{ $doneCount }}/{{ $totalChecks }} checked
                </p>
            </div>

            {{-- Table --}}
            <div class="flex-1 rounded-xl bg-nexora-slate-200 border border-nexora-corporate/50
                        overflow-y-auto [&::-webkit-scrollbar]:hidden">
                <table class="w-full text-xs table-fixed sortable-table" data-table-id="benchmark">
                    <thead class="sticky top-0 bg-nexora-slate-200 z-10">
                        <tr class="border-b border-nexora-corporate/30">
                            <th class="text-left text-nexora-deep-navy font-medium px-4 py-2.5 w-7">#</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-4 py-2.5 sortable" data-sort-type="text">Benchmark / Check</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-4 py-2.5 w-28 sortable" data-sort-type="text">Tool</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-4 py-2.5 w-32 sortable" data-sort-type="number">Target</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-4 py-2.5 w-32 sortable" data-sort-type="number">Result</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-4 py-2.5 w-20 sortable" data-sort-type="text">Verdict</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 1; $lastCat = ''; @endphp
                        @foreach($checks as $check)
                            @php
                                $res     = $results->firstWhere('checkId', $check['id']);
                                $val     = $res['value'] ?? null;
                                $verdict = $res['verdict'] ?? '';
                                $note    = $res['note'] ?? '';

                                if ($val !== null && $verdict === '') {
                                    if ($check['operator'] === '>=')
                                        $verdict = $val >= $check['target'] ? 'Pass' : ($val >= $check['target'] * 0.9 ? 'Warn' : 'Fail');
                                    elseif ($check['operator'] === '<=')
                                        $verdict = $val <= $check['target'] ? 'Pass' : ($val <= $check['target'] * 1.1 ? 'Warn' : 'Fail');
                                    else
                                        $verdict = $val == $check['target'] ? 'Pass' : 'Fail';
                                }

                                $vPill = match($verdict) {
                                    'Pass'  => 'bg-nexora-success/80 text-nexora-off-white',
                                    'Warn'  => 'bg-nexora-warning/80 text-nexora-off-white',
                                    'Fail'  => 'bg-nexora-danger/80 text-nexora-off-white',
                                    default => 'bg-nexora-slate-500/30 text-nexora-navy-mid',
                                };
                                $valColor = match($verdict) {
                                    'Pass'  => 'text-nexora-success',
                                    'Warn'  => 'text-nexora-warning',
                                    'Fail'  => 'text-nexora-danger',
                                    default => 'text-nexora-navy-mid',
                                };
                                $showCat = $check['category'] !== $lastCat;
                                $lastCat = $check['category'];
                            @endphp

                            @if($showCat)
                                <tr class="bg-nexora-slate-500/10 no-sort">
                                    <td colspan="6" class="px-4 py-1.5 text-[10px] font-semibold
                                                           text-nexora-corporate uppercase tracking-wider">
                                        {{ $check['category'] }}
                                    </td>
                                </tr>
                            @endif

                            <tr class="border-b border-nexora-corporate/10
                                       hover:bg-nexora-steel-blue/30 transition-colors duration-150">
                                <td class="px-4 py-2.5 text-nexora-navy-mid font-['Courier_New']">
                                    {{ str_pad($rowNum++, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-2.5" data-sort-value="{{ $check['name'] }}">
                                    <p class="font-medium text-nexora-deep-navy">{{ $check['name'] }}</p>
                                    @if($note)
                                        <p class="text-[10px] text-nexora-warning mt-0.5 italic">{{ $note }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-nexora-navy-mid" data-sort-value="{{ $check['tool'] }}">{{ $check['tool'] }}</td>
                                <td class="px-4 py-2.5 text-nexora-navy-mid" data-sort-value="{{ $check['target'] }}">
                                    {{ $check['operator'] }}
                                    {{ in_array($check['unit'], ['pts','MB/s','MT/s']) ? number_format($check['target']) : $check['target'] }}
                                    {{ $check['unit'] !== 'pass' ? $check['unit'] : '' }}
                                </td>
                                <td class="px-4 py-2.5 font-medium font-['Courier_New'] {{ $valColor }}" data-sort-value="{{ $val ?? -1 }}">
                                    @if($val !== null)
                                        {{ in_array($check['unit'], ['pts','MB/s','MT/s']) ? number_format($val) : $val }}
                                        {{ !in_array($check['unit'], ['pass']) ? $check['unit'] : '' }}
                                    @else
                                        <span class="text-nexora-navy-mid opacity-40">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5" data-sort-value="{{ $verdict ?: 'zzz' }}">
                                    @if($verdict)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $vPill }}">
                                            {{ $verdict }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-nexora-navy-mid opacity-50">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Side panel ──────────────────────────────────────────────────--}}
        <div class="w-52 flex-shrink-0 flex flex-col gap-3">

            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">Build Info</p>
                @foreach([
                    ['Due',    $selectedOrder['due']],
                    ['Range',  ucfirst(str_replace('-', ' ', $range))],
                    ['Checks', $totalChecks . ' total'],
                    ['Done',   $doneCount . ' / ' . $totalChecks . ' (' . $pct . '%)'],
                ] as [$k, $v])
                    <div class="flex justify-between items-center py-1.5 border-b border-nexora-corporate/20 last:border-0">
                        <span class="text-[10px] text-nexora-navy-mid">{{ $k }}</span>
                        <span class="text-[10px] font-medium text-nexora-deep-navy">{{ $v }}</span>
                    </div>
                @endforeach
            </div>

            @if($flagged->count())
            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">
                    Flagged Issues
                </p>
                <div class="flex flex-col gap-2">
                    @foreach($flagged as $flag)
                        @php
                            $chk = $checkMap[$flag['checkId']] ?? null;
                            $fc  = $flag['verdict'] === 'Fail'
                                    ? 'border-nexora-danger/40 bg-nexora-danger/10'
                                    : 'border-nexora-warning/40 bg-nexora-warning/10';
                            $ft  = $flag['verdict'] === 'Fail'
                                    ? 'text-nexora-danger'
                                    : 'text-nexora-warning';
                        @endphp
                        <div class="rounded-lg border px-2.5 py-2 {{ $fc }}">
                            <p class="text-[10px] font-semibold {{ $ft }}">{{ $chk['name'] ?? $flag['checkId'] }}</p>
                            <p class="text-[10px] text-nexora-navy-mid mt-0.5 leading-relaxed">{{ $flag['note'] }}</p>
                        </div>
                    @endforeach
                </div>
                <button onclick="openSendToInventoryModal()"
                        class="mt-3 w-full py-1.5 rounded-lg text-[10px] font-semibold
                               border border-nexora-danger/50 bg-nexora-danger/10 text-nexora-danger
                               hover:bg-nexora-danger/20 transition-colors duration-150">
                    Send to Inventory
                </button>
            </div>
            @endif

            <button class="w-full py-2 rounded-xl text-xs font-semibold
                           border border-nexora-corporate bg-nexora-corporate text-white
                           hover:bg-nexora-navy-mid transition-colors duration-150">
                Submit QC Report
            </button>

        </div>
    </div>
    @else
        <div class="flex-1 flex items-center justify-center text-nexora-navy-mid text-sm">
            No orders currently in QC Check.
        </div>
    @endif

    @if($selectedOrder)
    <script>
        const benchmarkData = {
            woId:      "{{ $selectedOrder['id'] }}",
            range:     "{{ $range }}",
            checks:    @json($checks->values()),
            results:   @json($results->values()),
            orderName: "{{ $selectedOrder['name'] }}",
            assigned:  "{{ $selectedOrder['assigned'] }}",
        };
    </script>
    @endif
</div>

<script>initSortableTables();</script>
