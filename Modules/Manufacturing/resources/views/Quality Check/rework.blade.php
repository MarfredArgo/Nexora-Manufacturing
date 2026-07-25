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

            {{-- Replacement parts needed --}}
            <div class="flex-1 bg-nexora-slate-200 border border-nexora-corporate/50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-semibold text-nexora-deep-navy uppercase tracking-wider">Replacement Parts Required</p>
                    <button onclick="openAddPartModal({{ $selectedIdx }})"
                            class="text-[10px] font-semibold px-2.5 py-1 rounded-full border border-nexora-corporate
                                   text-nexora-corporate hover:bg-nexora-corporate hover:text-white transition-colors">
                        + Add Part
                    </button>
                </div>
                @if(count($selectedRework['requiredParts']) > 0)
                    <div class="flex flex-col gap-2">
                        @foreach($selectedRework['requiredParts'] as $pi => $part)
                            <div class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg
                                        bg-nexora-slate-500/10 border border-nexora-corporate/20
                                        hover:bg-nexora-steel-blue/20 transition-colors">
                                <p class="text-xs font-medium text-nexora-deep-navy">{{ $part['name'] }}</p>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    @if(!empty($part['eta']))
                                        <p class="text-[10px] text-nexora-navy-mid">ETA: {{ $part['eta'] }}</p>
                                    @endif
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $partPill($part['status']) }}">{{ $part['status'] }}</span>
                                    <button onclick="openEditPartModal({{ $selectedIdx }}, {{ $pi }})"
                                            class="text-[10px] px-2 py-0.5 rounded-full border border-nexora-corporate/40
                                                   text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors">
                                        Edit
                                    </button>
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

            <button onclick="openMarkReadyForQCModal({{ $selectedIdx }})"
                    class="w-full py-2 rounded-xl text-xs font-semibold border border-nexora-corporate
                           bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">
                Mark Ready for QC
            </button>
        </div>
    </div>
    @else
        <div class="flex-1 flex items-center justify-center text-nexora-navy-mid text-sm">No rework orders at the moment.</div>
    @endif
</div>

{{-- ── EDIT REWORK MODAL ──────────────────────────────────────────────────── --}}
<div id="rework-edit-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'rework-edit-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20 flex-shrink-0">
            <div>
                <p class="text-[10px] text-nexora-navy-mid mb-0.5">Edit Rework Order</p>
                <h2 id="rw-modal-title" class="text-base font-bold text-nexora-deep-navy"></h2>
            </div>
            <button onclick="closeModal('rework-edit-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto [&::-webkit-scrollbar]:hidden px-5 py-4 flex flex-col gap-4">
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Status</label>
                <select id="rw-modal-status" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    <option>Waiting for Part</option>
                    <option>In Rework</option>
                    <option>Ready for QC</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Priority</label>
                <select id="rw-modal-priority" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    <option>High</option><option>Medium</option><option>Low</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Technician Notes</label>
                <textarea id="rw-modal-notes" rows="4" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate resize-none"></textarea>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-nexora-corporate/20 flex-shrink-0">
            <p id="rw-modal-save-msg" class="text-xs text-nexora-success hidden">✓ Saved</p>
            <div class="flex gap-2 ml-auto">
                <button onclick="closeModal('rework-edit-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
                <button onclick="saveReworkEdit()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- ── ADD / EDIT PART MODAL ──────────────────────────────────────────────── --}}
<div id="part-modal-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'part-modal-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4 flex flex-col">
        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 id="part-modal-title" class="text-base font-bold text-nexora-deep-navy">Add Replacement Part</h2>
            <button onclick="closeModal('part-modal-backdrop')" class="w-7 h-7 rounded-full flex items-center justify-center text-nexora-navy-mid hover:bg-nexora-slate-500/20 transition-colors text-lg leading-none">✕</button>
        </div>
        <div class="px-5 py-4 flex flex-col gap-3">
            <input type="hidden" id="part-modal-rework-idx">
            <input type="hidden" id="part-modal-part-idx">
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Part Name</label>
                <input id="part-modal-name" type="text" placeholder="e.g. Replacement GPU Cooler" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">Status</label>
                <select id="part-modal-status" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
                    <option>Sourcing</option><option>Ready</option><option>Missing</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-semibold text-nexora-slate-500 uppercase tracking-wider">ETA (optional)</label>
                <input id="part-modal-eta" type="text" placeholder="e.g. Jul 10, 2024" class="mt-1.5 w-full border border-nexora-corporate/40 rounded-lg px-3 py-2 text-xs text-nexora-deep-navy bg-nexora-slate-200 focus:outline-none focus:border-nexora-corporate">
            </div>
        </div>
        <div class="flex gap-2 justify-end px-5 pb-5">
            <button onclick="closeModal('part-modal-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
            <button id="part-modal-save-btn" onclick="savePartModal()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors">Add Part</button>
        </div>
    </div>
</div>

{{-- ── MARK READY FOR QC MODAL ────────────────────────────────────────────── --}}
<div id="qc-ready-backdrop" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center hidden" onclick="handleBackdropClick(event,'qc-ready-backdrop')">
    <div class="absolute inset-0 bg-nexora-deep-navy/40 backdrop-blur-sm pointer-events-none"></div>
    <div onclick="event.stopPropagation()" class="relative z-10 bg-nexora-off-white border border-nexora-corporate/50 rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="px-5 pt-5 pb-3 border-b border-nexora-corporate/20">
            <h2 class="text-base font-bold text-nexora-deep-navy">Mark Ready for QC?</h2>
            <p class="text-xs text-nexora-navy-mid mt-1">This will update the rework status to "Ready for QC" and queue it for a full benchmark re-check.</p>
        </div>
        <div class="flex gap-2 justify-end px-5 py-4">
            <button onclick="closeModal('qc-ready-backdrop')" class="px-4 py-1.5 rounded-full text-xs font-medium border border-nexora-corporate/50 text-nexora-navy-mid hover:bg-nexora-slate-200 transition-colors">Cancel</button>
            <button onclick="confirmMarkReadyForQC()" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-success text-white hover:opacity-90 transition-colors">Confirm</button>
        </div>
    </div>
</div>

<script>
const reworkData   = @json($reworkOrders->values()->toArray());
let rwEditIdx      = null;
let partReworkIdx  = null;
let partEditIdx    = null; // null = add mode, number = edit mode
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

// ── Add / Edit part ──────────────────────────────────────────────────────────
function openAddPartModal(reworkIdx) {
    partReworkIdx = reworkIdx; partEditIdx = null;
    document.getElementById('part-modal-title').textContent    = 'Add Replacement Part';
    document.getElementById('part-modal-save-btn').textContent = 'Add Part';
    document.getElementById('part-modal-name').value   = '';
    document.getElementById('part-modal-status').value = 'Sourcing';
    document.getElementById('part-modal-eta').value    = '';
    openModal('part-modal-backdrop');
}

function openEditPartModal(reworkIdx, partIdx) {
    partReworkIdx = reworkIdx; partEditIdx = partIdx;
    const part = reworkData[reworkIdx].requiredParts[partIdx];
    document.getElementById('part-modal-title').textContent    = 'Edit Part';
    document.getElementById('part-modal-save-btn').textContent = 'Save';
    document.getElementById('part-modal-name').value   = part.name;
    document.getElementById('part-modal-status').value = part.status;
    document.getElementById('part-modal-eta').value    = part.eta ?? '';
    openModal('part-modal-backdrop');
}

async function savePartModal() {
    const partData = {
        name:   document.getElementById('part-modal-name').value.trim(),
        status: document.getElementById('part-modal-status').value,
        eta:    document.getElementById('part-modal-eta').value.trim() || null,
    };
    const url     = partEditIdx !== null ? '/manufacturing/update-rework-part' : '/manufacturing/add-rework-part';
    const payload = { reworkIndex: partReworkIdx, partIndex: partEditIdx, part: partData, _token: document.querySelector('meta[name="csrf-token"]').content };
    try {
        const res  = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':payload._token}, body:JSON.stringify(payload) });
        const data = await res.json();
        if (data.success) { closeModal('part-modal-backdrop'); location.reload(); }
        else alert('Failed: ' + (data.message ?? 'Unknown'));
    } catch(e) { alert('Network error'); console.error(e); }
}

// ── Mark ready for QC ────────────────────────────────────────────────────────
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
