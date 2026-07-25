// benchmark.js
// Handles the QC Benchmark modal: rendering checks, verdicts, value input, save.

let bmRows = {};

// ── Modal open ──────────────────────────────────────────────────────────
function openBenchmarkModal() {
    bmRows = {};
    document.getElementById('bm-modal-woid').textContent = benchmarkData.woId + ' • Enter / update benchmark results';
    document.getElementById('bm-modal-name').textContent = benchmarkData.orderName;
    renderBenchmarkChecks();
    updateBenchmarkCounts();
    document.getElementById('bm-save-msg').classList.add('hidden');
    openModal('benchmark-backdrop');
}

// ── Render checks ───────────────────────────────────────────────────────
function renderBenchmarkChecks() {
    const list    = document.getElementById('bm-check-list');
    const checks  = benchmarkData.checks;
    const results = benchmarkData.results;

    list.innerHTML = '';
    let lastCat    = '';

    checks.forEach((check, idx) => {
        const existing = results.find(r => r.checkId === check.id) ?? {};

        bmRows[check.id] = {
            value:   existing.value   ?? null,
            verdict: existing.verdict ?? '',
            note:    existing.note    ?? '',
        };

        if (check.category !== lastCat) {
            lastCat = check.category;
            list.innerHTML += `
                <div class="px-2 py-1 mt-2 first:mt-0">
                    <p class="text-[10px] font-semibold text-nexora-corporate uppercase tracking-wider">
                        ${check.category}
                    </p>
                </div>`;
        }

        const num         = String(idx + 1).padStart(2, '0');
        const curVal      = bmRows[check.id].value   ?? '';
        const curNote     = bmRows[check.id].note    ?? '';
        const curVerdict  = bmRows[check.id].verdict ?? '';
        const isPassFail  = check.unit === 'pass';
        const needsFormat = ['pts', 'MB/s', 'MT/s'].includes(check.unit);
        const targetDisp  = (needsFormat ? Number(check.target).toLocaleString() : check.target)
                          + (check.unit !== 'pass' ? ' ' + check.unit : '');

        list.innerHTML += `
            <div class="bg-nexora-slate-200 border border-nexora-corporate/20 rounded-xl px-4 py-3 flex flex-col gap-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-2 min-w-0">
                        <span class="text-[10px] font-mono text-nexora-navy-mid flex-shrink-0 mt-0.5">${num}</span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-nexora-deep-navy">${check.name}</p>
                            <p class="text-[10px] text-nexora-navy-mid mt-0.5">
                                ${check.tool} &bull; Target: ${check.operator} ${targetDisp}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        ${['Pass','Warn','Fail'].map(v => `
                            <button id="vbtn-${check.id}-${v}"
                                    onclick="setBenchmarkVerdict('${check.id}', '${v}')"
                                    class="verdict-btn px-2.5 py-1 rounded-full text-[10px] font-semibold border transition-colors
                                           ${curVerdict === v ? activePillClass(v) : inactivePillClass(v)}">
                                ${v}
                            </button>`).join('')}
                    </div>
                </div>
                <div class="flex gap-2">
                    ${!isPassFail ? `
                    <div class="flex items-center gap-1.5 bg-nexora-off-white border border-nexora-corporate/30
                                rounded-lg px-3 py-1.5 w-36 flex-shrink-0">
                        <input type="number"
                               id="val-${check.id}"
                               value="${curVal}"
                               placeholder="Result"
                               required
                               oninput="onBenchmarkValueInput('${check.id}', ${check.target}, '${check.operator}', '${check.unit}')"
                               class="w-full bg-transparent text-xs text-nexora-deep-navy placeholder-nexora-navy-mid/50
                                      focus:outline-none [appearance:textfield]">
                        <span class="text-[10px] text-nexora-navy-mid flex-shrink-0">${check.unit}</span>
                    </div>` : ''}
                    <input type="text"
                           id="note-${check.id}"
                           value="${curNote}"
                           placeholder="Note / observation (optional)"
                           oninput="bmRows['${check.id}'].note = this.value"
                           class="flex-1 bg-nexora-off-white border border-nexora-corporate/30 rounded-lg
                                  px-3 py-1.5 text-xs text-nexora-deep-navy placeholder-nexora-navy-mid/50
                                  focus:outline-none focus:border-nexora-corporate">
                </div>
            </div>`;
    });
}

function activePillClass(v) {
    return v === 'Pass' ? 'bg-nexora-success text-white border-nexora-success'
         : v === 'Warn' ? 'bg-nexora-warning text-white border-nexora-warning'
         :                'bg-nexora-danger  text-white border-nexora-danger';
}

function inactivePillClass(v) {
    return v === 'Pass' ? 'border-nexora-success/40 text-nexora-success hover:bg-nexora-success/10'
         : v === 'Warn' ? 'border-nexora-warning/40 text-nexora-warning hover:bg-nexora-warning/10'
         :                'border-nexora-danger/40  text-nexora-danger  hover:bg-nexora-danger/10';
}

function setBenchmarkVerdict(checkId, verdict) {
    bmRows[checkId].verdict = verdict;

    ['Pass','Warn','Fail'].forEach(v => {
        const btn = document.getElementById(`vbtn-${checkId}-${v}`);
        if (!btn) return;
        btn.className = `verdict-btn px-2.5 py-1 rounded-full text-[10px] font-semibold border transition-colors
                         ${verdict === v ? activePillClass(v) : inactivePillClass(v)}`;
    });

    updateBenchmarkCounts();
}

function onBenchmarkValueInput(checkId, target, operator, unit) {
    const input = document.getElementById(`val-${checkId}`);
    const val   = parseFloat(input.value);

    bmRows[checkId].value = isNaN(val) ? null : val;

    if (!isNaN(val)) {
        let verdict = '';
        if (operator === '>=')
            verdict = val >= target ? 'Pass' : (val >= target * 0.9 ? 'Warn' : 'Fail');
        else if (operator === '<=')
            verdict = val <= target ? 'Pass' : (val <= target * 1.1 ? 'Warn' : 'Fail');
        else
            verdict = val == target ? 'Pass' : 'Fail';

        setBenchmarkVerdict(checkId, verdict);
    }
}

function updateBenchmarkCounts() {
    let pass = 0, warn = 0, fail = 0;
    Object.values(bmRows).forEach(r => {
        if (r.verdict === 'Pass') pass++;
        else if (r.verdict === 'Warn') warn++;
        else if (r.verdict === 'Fail') fail++;
    });
    document.getElementById('bm-count-pass').textContent = pass + ' Pass';
    document.getElementById('bm-count-warn').textContent = warn + ' Warn';
    document.getElementById('bm-count-fail').textContent = fail + ' Fail';
}

// ── Save ────────────────────────────────────────────────────────────────
async function saveBenchmarkResults() {
    // Every measurable check must have a result value before saving. Pass/fail
    // checks (unit === 'pass') are verdict-only and are exempt.
    const missing = [];
    benchmarkData.checks.forEach(check => {
        if (check.unit === 'pass') return;
        const row = bmRows[check.id];
        const input = document.getElementById(`val-${check.id}`);
        if (!row || row.value === null || row.value === undefined || row.value === '') {
            missing.push(check.id);
            if (input) input.parentElement.classList.add('border-nexora-danger');
        } else if (input) {
            input.parentElement.classList.remove('border-nexora-danger');
        }
    });

    if (missing.length) {
        alert(`Please enter a result for all ${missing.length} remaining check(s) before saving.`);
        const first = document.getElementById(`val-${missing[0]}`);
        if (first) first.focus();
        return;
    }

    const results = Object.entries(bmRows).map(([checkId, data]) => ({
        checkId,
        value:   data.value,
        verdict: data.verdict,
        note:    data.note,
    }));

    const payload = {
        woId:   benchmarkData.woId,
        results,
        _token: document.querySelector('meta[name="csrf-token"]').content,
    };

    try {
        const res  = await fetch('/manufacturing/update-qc', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body:    JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));
        if (data.success) {
            document.getElementById('bm-save-msg').classList.remove('hidden');
            setTimeout(() => window.location.reload(), 800);
        } else {
            alert('Save failed: ' + (data.message ?? `Server error (${res.status})`));
        }
    } catch (err) {
        alert('Network error — could not save results.');
        console.error(err);
    }
}

// ── Send to inventory ───────────────────────────────────────────────────
function openSendToInventoryModal() {
    document.getElementById('req-part-name').value = '';
    document.getElementById('req-quantity').value = 1;
    document.getElementById('req-notes').value = '';
    openModal('inventory-backdrop');
}

async function submitInventoryRequest() {
    const payload = {
        woId:        benchmarkData.woId,
        partName:    document.getElementById('req-part-name').value.trim(),
        quantity:    document.getElementById('req-quantity').value,
        requestedBy: benchmarkData.assigned,
        notes:       document.getElementById('req-notes').value.trim(),
        _token:      document.querySelector('meta[name="csrf-token"]').content,
    };

    if (!payload.partName) {
        alert('Please enter a part name.');
        return;
    }

    try {
        const res  = await fetch('/manufacturing/send-to-inventory', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body:    JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            closeModal('inventory-backdrop');
            alert(data.message ?? 'Defective part sent to Inventory.');
        } else {
            alert('Failed: ' + (data.message ?? 'Unknown error'));
        }
    } catch (err) {
        alert('Network error — could not send requisition.');
        console.error(err);
    }
}
