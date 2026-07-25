@php
    $reworkOrders   = collect($tempData['reworkOrders'] ?? []);
    $selectedIdx    = (int) request()->get('rework', 0);
    $selectedRework = $reworkOrders[$selectedIdx] ?? $reworkOrders[0] ?? null;

    // Cross-reference the source work order for actual physical defective parts
    $sourceWo = $selectedRework
        ? collect($workOrders)->firstWhere('id', $selectedRework['woId'])
        : null;
    $defectiveParts = ($selectedRework && $sourceWo)
        ? collect($sourceWo['parts'])->whereIn('status', ['Sourcing', 'Missing'])->values()
        : collect([]);

    // Retest is only allowed once every requested replacement part has been
    // received and marked Ready (no parts needed = software/config-only rework).
    $reqParts       = collect($selectedRework['requiredParts'] ?? []);
    $partsReplaced  = $reqParts->isEmpty() || $reqParts->every(fn($p) => ($p['status'] ?? '') === 'Ready');

    // Live Inventory availability per replacement part — drives the Grab button.
    $bridge   = app(\Modules\Manufacturing\Services\InventoryBridgeService::class);
    $clientId = ((int) session('employee_client_id')) ?: null;
    $stockFor = [];
    foreach ($reqParts as $pi => $p) {
        $stockFor[$pi] = ($p['status'] ?? '') === 'Ready'
            ? 0
            : $bridge->availableStockFor($p['name'] ?? '', $clientId);
    }

    $reworkPill = fn($s) => match($s) {
        'Waiting for Part' => 'bg-nexora-warning/80 text-nexora-off-white',
        'In Rework'        => 'bg-nexora-info/80 text-nexora-off-white',
        'Ready for QC'     => 'bg-nexora-success/80 text-nexora-off-white',
        'Escalated'        => 'bg-nexora-danger/80 text-nexora-off-white',
        default            => 'bg-nexora-slate-500/30 text-nexora-navy-mid',
    };
    $priorityColor = fn($p) => match($p) {
        'High'   => 'text-nexora-danger',
        'Medium' => 'text-nexora-warning',
        'Low'    => 'text-nexora-success',
        default  => 'text-nexora-navy-mid',
    };
    $partPill = fn($s) => match($s) {
        'Ready'    => 'bg-nexora-success/80 text-nexora-off-white',
        'Sourcing' => 'bg-nexora-warning/80 text-nexora-off-white',
        'Missing'  => 'bg-nexora-danger/80 text-nexora-off-white',
        default    => 'bg-nexora-slate-500/30 text-nexora-navy-mid',
    };
@endphp

<div class="flex gap-3 h-full">

    {{-- LEFT: picker --}}
    <div class="w-44 flex-shrink-0 flex flex-col gap-2">
        <h1 class="font-heading font-medium text-xl text-nexora-navy-mid whitespace-nowrap">REWORK</h1>
        <div class="flex-1 rounded-lg bg-nexora-slate-200 border border-nexora-corporate/50
                    px-1 py-3 overflow-y-auto [&::-webkit-scrollbar]:hidden">
            @forelse($reworkOrders as $i => $rw)
                @php $isActive = $i === $selectedIdx; @endphp
                <a href="?page=qc&sub=rework&rework={{ $i }}"
                   class="block px-3 py-2.5 mb-1 rounded-md cursor-pointer transition-all duration-150
                          {{ $isActive ? 'bg-nexora-steel-blue/80' : 'hover:bg-nexora-steel-blue/50 hover:shadow-md hover:-translate-y-[2px]' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] text-nexora-navy font-['Courier_New'] mb-0.5">{{ $rw['id'] }}</p>
                            <p class="text-xs font-semibold text-nexora-deep-navy truncate">{{ $rw['buildName'] }}</p>
                            <p class="text-[10px] text-nexora-navy-mid mt-0.5">{{ $rw['assignedTech'] }}</p>
                        </div>
                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full flex-shrink-0 mt-0.5 {{ $reworkPill($rw['status']) }}">
                            {{ explode(' ', $rw['status'])[0] }}
                        </span>
                    </div>
                </a>
            @empty
                <p class="text-xs text-nexora-navy-mid px-3 py-2">No rework orders.</p>
            @endforelse
        </div>
    </div>

    {{-- RIGHT: detail + side panel --}}
    @if($selectedRework)
    <div class="flex flex-1 gap-3 min-w-0">

        {{-- Main --}}
        <div class="flex-1 flex flex-col gap-3 min-w-0">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 flex-wrap flex-shrink-0">
                <div>
                    <p class="text-[10px] text-nexora-navy font-['Courier_New']">
                        {{ $selectedRework['id'] }} &bull; from {{ $selectedRework['woId'] }}
                    </p>
                    <h2 class="text-xl font-bold text-nexora-deep-navy leading-tight">{{ $selectedRework['buildName'] }}</h2>
                    <p class="text-xs text-nexora-navy-mid mt-0.5">
                        Tech: {{ $selectedRework['assignedTech'] }} &bull; Raised: {{ $selectedRework['raisedDate'] }}
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-semibold {{ $priorityColor($selectedRework['priority']) }}">
                        {{ $selectedRework['priority'] }} priority
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $reworkPill($selectedRework['status']) }}">
                        {{ $selectedRework['status'] }}
                    </span>
                    <button onclick="openReworkEditModal({{ $selectedIdx }})"
                            class="px-3 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate
                                   bg-nexora-steel-blue text-nexora-deep-navy hover:bg-nexora-corporate hover:text-white transition-colors">
                        Edit
                    </button>
                </div>
            </div>

            {{-- Defective physical parts from WO --}}
            @if($defectiveParts->count())
            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4 flex-shrink-0">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">
                    Defective / Unavailable Parts
                    <span class="ml-1 normal-case font-normal text-nexora-navy-mid">(from work order {{ $selectedRework['woId'] }})</span>
                </p>
                <div class="flex flex-col gap-1.5">
                    @foreach($defectiveParts as $part)
                        @php $ps = $partStyles[$part['status']] ?? ['dot' => 'bg-gray-400', 'text' => 'text-gray-400']; @endphp
                        <div class="flex items-center justify-between px-3 py-2 rounded-lg
                                    bg-nexora-slate-500/10 border border-nexora-corporate/20
                                    hover:bg-nexora-steel-blue/20 transition-colors">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $ps['dot'] }}"></span>
                                <span class="text-xs font-medium text-nexora-deep-navy">{{ $part['category'] }}</span>
                                <span class="text-[10px] text-nexora-navy-mid">→</span>
                                <span class="text-xs text-nexora-deep-navy">{{ $part['name'] }}</span>
                            </div>
                            <span class="text-xs font-semibold {{ $ps['text'] }}">{{ $part['status'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-[10px] text-nexora-navy-mid mt-2 italic">These are the physical parts unavailable for this build.</p>
            </div>
            @endif

            {{-- Failed benchmark checks --}}
            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4 flex-shrink-0">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">Failed / Warned Benchmark Checks</p>
                <table class="w-full text-xs table-fixed sortable-table" data-table-id="rework-checks">
                    <thead>
                        <tr class="border-b border-nexora-corporate/30">
                            <th class="text-left text-nexora-deep-navy font-medium px-3 py-2 sortable" data-sort-type="text">Check</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-3 py-2 w-28 sortable" data-sort-type="text">Result</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-3 py-2 w-28 sortable" data-sort-type="text">Target</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-3 py-2 w-20 sortable" data-sort-type="text">Verdict</th>
                            <th class="text-left text-nexora-deep-navy font-medium px-3 py-2 sortable" data-sort-type="text">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedRework['failedChecks'] as $fc)
                            @php
                                $vPill    = $fc['verdict'] === 'Fail' ? 'bg-nexora-danger/80 text-nexora-off-white' : 'bg-nexora-warning/80 text-nexora-off-white';
                                $valColor = $fc['verdict'] === 'Fail' ? 'text-nexora-danger' : 'text-nexora-warning';
                            @endphp
                            <tr class="border-b border-nexora-corporate/10 hover:bg-nexora-steel-blue/20 transition-colors">
                                <td class="px-3 py-2.5 font-medium text-nexora-deep-navy" data-sort-value="{{ $fc['checkName'] }}">{{ $fc['checkName'] }}</td>
                                <td class="px-3 py-2.5 font-['Courier_New'] {{ $valColor }}" data-sort-value="{{ $fc['result'] }}">{{ $fc['result'] }}</td>
                                <td class="px-3 py-2.5 text-nexora-navy-mid" data-sort-value="{{ $fc['target'] }}">{{ $fc['target'] }}</td>
                                <td class="px-3 py-2.5" data-sort-value="{{ $fc['verdict'] }}"><span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $vPill }}">{{ $fc['verdict'] }}</span></td>
                                <td class="px-3 py-2.5 text-nexora-navy-mid italic" data-sort-value="{{ $fc['reason'] }}">{{ $fc['reason'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Replacement parts — auto-listed from the build's failed parts.
                 Status is driven by stock: Missing = out of stock, Sourcing = has
                 stock (grab enabled), Ready = grabbed. No manual add/edit. --}}
            <div class="flex-1 bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">Replacement Parts Required</p>
                @if(count($selectedRework['requiredParts']) > 0)
                    <div class="flex flex-col gap-2">
                        @foreach($selectedRework['requiredParts'] as $pi => $part)
                            @php
                                $isReady = ($part['status'] ?? '') === 'Ready';
                                $avail   = $isReady ? 0 : ($stockFor[$pi] ?? 0);
                                $disp    = $isReady ? 'Ready' : ($avail > 0 ? 'Sourcing' : 'Missing');
                            @endphp
                            <div class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg
                                        bg-nexora-slate-500/10 border border-nexora-corporate/20
                                        hover:bg-nexora-steel-blue/20 transition-colors">
                                <p class="text-xs font-medium text-nexora-deep-navy">{{ $part['name'] }}</p>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $partPill($disp) }}">
                                        {{ $disp === 'Missing' ? 'Out of Stock' : $disp }}
                                    </span>
                                    @if($isReady)
                                        <span class="text-[10px] font-semibold text-nexora-success">✓ Replaced</span>
                                    @elseif($avail > 0)
                                        <button onclick="grabReplacementPart({{ $selectedIdx }}, {{ $pi }})"
                                                class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full border border-nexora-success
                                                       text-nexora-success hover:bg-nexora-success hover:text-white transition-colors">
                                            Grab from Stock
                                        </button>
                                    @else
                                        <button disabled title="Out of stock — waiting for restock"
                                                class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full border border-nexora-corporate/20
                                                       bg-nexora-slate-500/20 text-nexora-navy-mid/60 cursor-not-allowed">
                                            Awaiting Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-nexora-navy-mid">No replacement parts needed — rework is software or configuration only.</p>
                @endif

                <div class="mt-4 pt-3 border-t border-nexora-corporate/20">
                    <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-1.5">Technician Notes</p>
                    <p class="text-xs text-nexora-navy-mid leading-relaxed">{{ $selectedRework['notes'] ?: '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Side panel --}}
        <div class="w-52 flex-shrink-0 flex flex-col gap-3">

            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">Rework Info</p>
                @foreach([
                    ['Rework ID',  $selectedRework['id']],
                    ['Work Order', $selectedRework['woId']],
                    ['Raised by',  $selectedRework['raisedBy']],
                    ['Raised',     $selectedRework['raisedDate']],
                    ['Priority',   $selectedRework['priority']],
                    ['Status',     $selectedRework['status']],
                ] as [$k,$v])
                    <div class="flex justify-between items-center py-1.5 border-b border-nexora-corporate/20 last:border-0">
                        <span class="text-[10px] text-nexora-navy-mid">{{ $k }}</span>
                        <span class="text-[10px] font-medium text-nexora-deep-navy">{{ $v }}</span>
                    </div>
                @endforeach
            </div>

            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">Inventory Request</p>
                @if($selectedRework['escalatedToInventory'])
                    <div class="rounded-lg border border-nexora-info/40 bg-nexora-info/10 px-2.5 py-2 mb-2">
                        <p class="text-[10px] font-semibold text-nexora-info">Sent to Inventory</p>
                        <p class="text-[10px] text-nexora-navy-mid mt-0.5">Defect report sent to inventory for replacement part.</p>
                    </div>
                @else
                    <div class="rounded-lg border border-nexora-corporate/30 bg-nexora-slate-500/10 px-2.5 py-2 mb-2">
                        <p class="text-[10px] text-nexora-navy-mid">Not yet sent to inventory.</p>
                    </div>
                    <button onclick="escalateToInventory({{ $selectedIdx }})"
                            class="w-full py-1.5 rounded-lg text-[10px] font-semibold border border-nexora-corporate/50
                                   text-nexora-corporate bg-nexora-corporate/10 hover:bg-nexora-corporate hover:text-white transition-colors">
                        Send to Inventory
                    </button>
                @endif
            </div>

            <div class="bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider mb-3">Rework Flow</p>
                @php
                    $fs = $selectedRework['status'];
                    $flowSteps = [
                        ['QC flagged',       'Benchmark flags an issue',       true],
                        ['Rework raised',    'Sent from QC benchmark',         true],
                        ['Waiting for part', 'Inventory sourcing replacement', in_array($fs, ['Waiting for Part','In Rework','Ready for QC'])],
                        ['In rework',        'Tech fixes or replaces part',    in_array($fs, ['In Rework','Ready for QC'])],
                        ['Ready for QC',     'Full benchmark re-run',          $fs === 'Ready for QC'],
                    ];
                @endphp
                @foreach($flowSteps as $si => [$sname, $ssub, $sdone])
                    <div class="flex gap-2 items-start">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-semibold
                                {{ $sdone ? 'bg-nexora-success/20 text-nexora-success border border-nexora-success/50'
                                          : 'bg-nexora-slate-500/20 text-nexora-navy-mid border border-nexora-corporate/30' }}">
                                {{ $sdone ? '✓' : $si+1 }}
                            </div>
                            @if($si < count($flowSteps)-1)
                                <div class="w-px h-4 bg-nexora-corporate/20 my-0.5"></div>
                            @endif
                        </div>
                        <div class="pt-0.5 pb-3">
                            <p class="text-[10px] font-semibold text-nexora-deep-navy">{{ $sname }}</p>
                            <p class="text-[10px] text-nexora-navy-mid">{{ $ssub }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($partsReplaced)
                <button onclick="openMarkReadyForQCModal({{ $selectedIdx }})"
                        class="w-full py-2 rounded-xl text-xs font-semibold border border-nexora-corporate
                               bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">
                    Mark Ready for QC
                </button>
            @else
                <button disabled title="All replacement parts must be received and marked Ready first."
                        class="w-full py-2 rounded-xl text-xs font-semibold border border-nexora-corporate/20
                               bg-nexora-slate-500/20 text-nexora-navy-mid/60 cursor-not-allowed">
                    Awaiting Replacement Parts
                </button>
            @endif
        </div>
    </div>
    @else
        <div class="flex-1 flex items-center justify-center text-nexora-navy-mid text-sm">No rework orders at the moment.</div>
    @endif
</div>

<script>
const reworkData   = @json($reworkOrders->values()->toArray());
let rwEditIdx      = null;
let qcReadyIdx     = null;

// ── Edit rework ──────────────────────────────────────────────────────────────
function openReworkEditModal(i) {
    rwEditIdx = i;
    const rw = reworkData[i];
    document.getElementById('rw-modal-title').textContent    = rw.buildName;
    document.getElementById('rw-modal-status').value         = rw.status;
    document.getElementById('rw-modal-priority').value       = rw.priority;
    document.getElementById('rw-modal-notes').value          = rw.notes ?? '';
    document.getElementById('rw-modal-save-msg').classList.add('hidden');
    openModal('rework-edit-backdrop');
}

async function saveReworkEdit() {
    const payload = {
        reworkIndex: rwEditIdx,
        status:   document.getElementById('rw-modal-status').value,
        priority: document.getElementById('rw-modal-priority').value,
        notes:    document.getElementById('rw-modal-notes').value,
        _token:   document.querySelector('meta[name="csrf-token"]').content,
    };
    try {
        const res  = await fetch('/manufacturing/update-rework', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':payload._token}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) { document.getElementById('rw-modal-save-msg').classList.remove('hidden'); setTimeout(() => location.reload(), 800); }
        else alert('Save failed: ' + (data.message ?? 'Unknown'));
    } catch(e) { alert('Network error'); console.error(e); }
}

// ── Mark ready for QC ────────────────────────────────────────────────────────
async function grabReplacementPart(reworkIdx, partIdx) {
    const payload = { reworkIndex: reworkIdx, partIndex: partIdx, _token: document.querySelector('meta[name="csrf-token"]').content };
    try {
        const res  = await fetch('/manufacturing/grab-replacement-part', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':payload._token}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) location.reload();
        else alert(data.message ?? 'Could not grab stock.');
    } catch(e) { alert('Network error'); console.error(e); }
}

function openMarkReadyForQCModal(i) { qcReadyIdx = i; openModal('qc-ready-backdrop'); }

async function confirmMarkReadyForQC() {
    const payload = { reworkIndex: qcReadyIdx, status: 'Ready for QC', _token: document.querySelector('meta[name="csrf-token"]').content };
    try {
        const res  = await fetch('/manufacturing/update-rework', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':payload._token}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) { closeModal('qc-ready-backdrop'); location.reload(); }
        else alert('Failed: ' + (data.message ?? 'Unknown'));
    } catch(e) { alert('Network error'); console.error(e); }
}

// ── Escalate to inventory ─────────────────────────────────────────────────────
async function escalateToInventory(i) {
    const payload = { reworkIndex: i, escalate: true, _token: document.querySelector('meta[name="csrf-token"]').content };
    try {
        const res  = await fetch('/manufacturing/update-rework', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':payload._token}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) location.reload();
        else alert('Failed: ' + (data.message ?? 'Unknown'));
    } catch(e) { alert('Network error'); console.error(e); }
}
</script>

<script>initSortableTables();</script>
