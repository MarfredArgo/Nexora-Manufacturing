@php
    $qcSessions  = collect($tempData['qcSessions'] ?? []);
    $benchmarkTargets = $tempData['benchmarkTargets'] ?? [];
    $reworkOrders = collect($tempData['reworkOrders'] ?? []);

    // Flatten all results
    $allResults  = $qcSessions->flatMap(fn($s) => collect($s['results'])->map(fn($r) => array_merge($r, ['woId' => $s['woId'], 'tech' => $s['tech']])));

    // Verdict counts
    $totalChecks = $allResults->filter(fn($r) => $r['verdict'] !== '')->count();
    $passCount   = $allResults->where('verdict', 'Pass')->count();
    $warnCount   = $allResults->where('verdict', 'Warn')->count();
    $failCount   = $allResults->where('verdict', 'Fail')->count();
    $passRate    = $totalChecks > 0 ? round(($passCount / $totalChecks) * 100) : 0;

    // Sessions summary
    $totalSessions   = $qcSessions->count();
    $cleanSessions   = $qcSessions->filter(fn($s) => collect($s['results'])->whereIn('verdict', ['Warn','Fail'])->count() === 0)->count();
    $flaggedSessions = $totalSessions - $cleanSessions;

    // Rework stats
    $totalRework   = $reworkOrders->count();
    $waitingRework = $reworkOrders->where('status', 'Waiting for Part')->count();
    $inRework      = $reworkOrders->where('status', 'In Rework')->count();

    // Most common flagged check across all sessions
    $flaggedChecks = $allResults->whereIn('verdict', ['Warn','Fail'])
        ->groupBy('checkId')
        ->map(fn($g, $id) => ['checkId' => $id, 'count' => $g->count(), 'verdicts' => $g->pluck('verdict')])
        ->sortByDesc('count')
        ->values()
        ->take(5);

    // Resolve check name from templates
    $allCheckDefs = collect($benchmarkTargets)->flatMap(function ($checks) {
        return collect($checks)->map(function ($def, $checkId) {
            return array_merge($def, ['id' => $checkId]);
        });
    })->keyBy('id');

    // Per-tech summary
    $techSummary = $qcSessions->groupBy('tech')->map(fn($sessions, $tech) => [
        'tech'    => $tech,
        'total'   => $sessions->count(),
        'flagged' => $sessions->filter(fn($s) => collect($s['results'])->whereIn('verdict', ['Warn','Fail'])->count() > 0)->count(),
        'pass'    => $sessions->filter(fn($s) => collect($s['results'])->whereIn('verdict', ['Warn','Fail'])->count() === 0)->count(),
    ])->values();

    // Chart data: verdict distribution
    $verdictLabels = ['Pass', 'Warn', 'Fail'];
    $verdictCounts = [$passCount, $warnCount, $failCount];
    $verdictColors = ['#16A34A', '#D97706', '#DC2626'];

    // Chart data: checks done per session (mock progress)
    $sessionLabels = $qcSessions->map(fn($s) => $s['woId'])->values()->toArray();
    $sessionDone   = $qcSessions->map(fn($s) => collect($s['results'])->filter(fn($r) => $r['verdict'] !== '')->count())->values()->toArray();
    $sessionTotal  = $qcSessions->map(fn($s) => count($s['results']))->values()->toArray();
@endphp

<div class="flex flex-col gap-3 h-full overflow-y-auto [&::-webkit-scrollbar]:hidden">

    <div class="flex items-center justify-between flex-shrink-0">
    <h1 class="font-heading font-medium text-xl text-nexora-navy-mid">QC ANALYTICS</h1>
    <button onclick="openAnalyticsNoteModal()"
            class="text-xs font-semibold px-3 py-1.5 rounded-full border border-nexora-corporate
                   text-nexora-corporate hover:bg-nexora-corporate hover:text-white transition-colors">
        + Add Note
    </button>
</div>

    {{-- KPI row --}}
    <div class="grid grid-cols-4 gap-3 flex-shrink-0">
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl px-4 py-3">
            <p class="text-[10px] text-nexora-navy-mid mb-1">Overall Pass Rate</p>
            <p class="text-3xl font-heading font-medium {{ $passRate >= 80 ? 'text-nexora-success' : 'text-nexora-warning' }}">
                {{ $passRate }}%
            </p>
            <p class="text-[10px] text-nexora-navy-mid mt-1">{{ $passCount }} / {{ $totalChecks }} checks passed</p>
        </div>
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl px-4 py-3">
            <p class="text-[10px] text-nexora-navy-mid mb-1">QC Sessions</p>
            <p class="text-3xl font-heading font-medium text-nexora-deep-navy">{{ $totalSessions }}</p>
            <p class="text-[10px] text-nexora-navy-mid mt-1">{{ $cleanSessions }} clean · {{ $flaggedSessions }} flagged</p>
        </div>
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl px-4 py-3">
            <p class="text-[10px] text-nexora-navy-mid mb-1">Warns & Fails</p>
            <p class="text-3xl font-heading font-medium {{ ($warnCount + $failCount) > 0 ? 'text-nexora-warning' : 'text-nexora-success' }}">
                {{ $warnCount + $failCount }}
            </p>
            <p class="text-[10px] text-nexora-navy-mid mt-1">{{ $warnCount }} warn · {{ $failCount }} fail</p>
        </div>
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl px-4 py-3">
            <p class="text-[10px] text-nexora-navy-mid mb-1">Rework Orders</p>
            <p class="text-3xl font-heading font-medium {{ $totalRework > 0 ? 'text-nexora-danger' : 'text-nexora-success' }}">
                {{ $totalRework }}
            </p>
            <p class="text-[10px] text-nexora-navy-mid mt-1">{{ $waitingRework }} waiting · {{ $inRework }} in progress</p>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-[200px_1fr] gap-3 flex-shrink-0">

        {{-- Verdict donut --}}
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4 flex flex-col items-center">
            <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3 self-start">
                Verdict Split
            </p>
            <div style="height:120px;width:120px;position:relative">
                <canvas id="qcVerdictDonut" aria-label="Donut chart of QC verdict split"></canvas>
            </div>
            <div class="flex flex-col gap-1 mt-3 self-start w-full">
                @foreach([['Pass','bg-nexora-success',$passCount],['Warn','bg-nexora-warning',$warnCount],['Fail','bg-nexora-danger',$failCount]] as [$label,$dot,$count])
                    <span class="flex items-center justify-between text-[10px] text-nexora-navy-mid">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block w-2 h-2 rounded-full {{ $dot }}"></span>{{ $label }}
                        </span>
                        <span class="text-nexora-deep-navy font-medium">{{ $count }}</span>
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Session progress bars --}}
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-4">
                Checks completed per session
            </p>
            <div class="flex flex-col gap-3">
                @foreach($qcSessions as $sess)
                    @php
                        $done    = collect($sess['results'])->filter(fn($r) => $r['verdict'] !== '')->count();
                        $tot     = count($sess['results']);
                        $pct     = $tot > 0 ? round(($done / $tot) * 100) : 0;
                        $hasFlag = collect($sess['results'])->whereIn('verdict', ['Warn','Fail'])->count() > 0;
                        $barCol  = $hasFlag ? 'bg-nexora-warning' : 'bg-nexora-corporate';
                        $wo      = collect($workOrders)->firstWhere('id', $sess['woId']);
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-['Courier_New'] text-nexora-navy-mid w-28 flex-shrink-0">
                            {{ $sess['woId'] }}
                        </span>
                        <div class="flex-1 h-2 bg-nexora-slate-500/20 rounded-full overflow-hidden">
                            <div class="{{ $barCol }} h-full rounded-full transition-all duration-300"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] text-nexora-navy-mid w-16 text-right flex-shrink-0">
                            {{ $done }}/{{ $tot }} ({{ $pct }}%)
                        </span>
                        @if($hasFlag)
                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full flex-shrink-0
                                         bg-nexora-warning/80 text-nexora-off-white">
                                Flagged
                            </span>
                        @else
                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full flex-shrink-0
                                         bg-nexora-success/80 text-nexora-off-white">
                                Clean
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bottom row: most flagged checks + per-tech --}}
    <div class="grid grid-cols-2 gap-3">

        {{-- Most flagged checks --}}
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">
                Most Flagged Checks
            </p>
            @forelse($flaggedChecks as $fc)
                @php
                    $def     = $allCheckDefs[$fc['checkId']] ?? null;
                    $label   = $def['name'] ?? $fc['checkId'];
                    $failsHere = $fc['verdicts']->where(null, 'Fail')->count();
                    $warnsHere = $fc['verdicts']->where(null, 'Warn')->count();
                    $barWidth  = min(100, $fc['count'] * 25);
                @endphp
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-[10px] text-nexora-navy-mid w-44 truncate flex-shrink-0" title="{{ $label }}">
                        {{ $label }}
                    </span>
                    <div class="flex-1 h-1.5 bg-nexora-slate-500/20 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $failsHere > 0 ? 'bg-nexora-danger' : 'bg-nexora-warning' }}"
                             style="width:{{ $barWidth }}%"></div>
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        @if($warnsHere > 0)
                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full
                                         bg-nexora-warning/80 text-nexora-off-white">
                                {{ $warnsHere }}W
                            </span>
                        @endif
                        @if($failsHere > 0)
                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full
                                         bg-nexora-danger/80 text-nexora-off-white">
                                {{ $failsHere }}F
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-nexora-navy-mid">No flagged checks yet.</p>
            @endforelse
        </div>

        {{-- Per-tech summary --}}
        <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
            <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">
                QC Results by Technician
            </p>
            @foreach($techSummary as $t)
                @php $pct = $t['total'] > 0 ? round(($t['pass'] / $t['total']) * 100) : 0; @endphp
                <div class="flex items-center gap-3 mb-3 last:mb-0">
                    <div class="w-7 h-7 rounded-full bg-nexora-corporate flex items-center justify-center
                                text-[10px] font-semibold text-white flex-shrink-0">
                        {{ strtoupper(substr($t['tech'], 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between mb-1">
                            <span class="text-[10px] font-medium text-nexora-deep-navy">{{ $t['tech'] }}</span>
                            <span class="text-[10px] text-nexora-navy-mid">
                                {{ $t['pass'] }}/{{ $t['total'] }} clean ({{ $pct }}%)
                            </span>
                        </div>
                        <div class="h-1.5 bg-nexora-slate-500/20 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $pct >= 80 ? 'bg-nexora-corporate' : 'bg-nexora-warning' }}"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                        @if($t['flagged'] > 0)
                            <p class="text-[10px] text-nexora-warning mt-0.5">{{ $t['flagged'] }} session(s) with flags</p>
                        @endif
                    </div>
                </div> {{-- adssa --}}
            @endforeach
        </div>
    </div>
</div>

<script>
    window.qcAnalyticsData = {
        verdictLabels: @json($verdictLabels),
        verdictCounts: @json($verdictCounts),
        verdictColors: @json($verdictColors),
    };
</script>
<script src="{{ asset('manufacturing/js/analytics-charts.js') }}"></script>

{{-- ── ADD QC NOTE MODAL ───────────────────────────────────────────────────── --}}
<div id="qc-note-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'qc-note-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4 flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 class="text-base font-bold text-nexora-deep-navy">Add QC Note</h2>
            <button onclick="closeModal('qc-note-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="px-5 py-4 flex flex-col gap-3">
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Work Order</label>
                <select id="qc-note-wo" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    @foreach($qcSessions as $sess)
                        <option value="{{ $sess['woId'] }}">{{ $sess['woId'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Note</label>
                <textarea id="qc-note-text" rows="4" placeholder="Enter your QC observation or note..." class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate resize-none"></textarea>
            </div>
        </div>
        <div class="flex gap-2 justify-end px-5 pb-5">
            <button onclick="closeModal('qc-note-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
            <button onclick="saveQcNote()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">Save Note</button>
        </div>
    </div>
</div>

<script>
function openAnalyticsNoteModal() {
    document.getElementById('qc-note-text').value = '';
    openModal('qc-note-backdrop');
}

async function saveQcNote() {
    const payload = {
        woId:   document.getElementById('qc-note-wo').value,
        note:   document.getElementById('qc-note-text').value,
        _token: document.querySelector('meta[name="csrf-token"]').content,
    };
    try {
        const res  = await fetch('/manufacturing/add-qc-note', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':payload._token}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) { closeModal('qc-note-backdrop'); location.reload(); }
        else alert('Failed: ' + (data.message ?? 'Unknown'));
    } catch(e) { alert('Network error'); console.error(e); }
}
</script>
