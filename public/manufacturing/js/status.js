// status.js
// Handles the Status sub-page edit modal: parts list, mark ready, send to QC, save.

let editingOrderIndex = null;
let pendingChanges    = {};
let pendingQC         = false;

// ── Modal open ──────────────────────────────────────────────────────────
function openEditModal(i) {
    editingOrderIndex = i;
    pendingChanges    = {};
    pendingQC         = false;

    const order = workOrdersData[i];

    document.getElementById('modal-order-id').textContent   = order.id + ' • ' + order.source;
    document.getElementById('modal-order-name').textContent = order.name;

    const statusEl   = document.getElementById('modal-order-status');
    statusEl.textContent = order.status;
    statusEl.className   = 'px-2.5 py-1 rounded-full text-xs font-bold ' + getStatusPill(order.status);

    const allPartsAlreadyReady = order.parts.every(p => p.status === 'Ready');
    const showQCBtn = order.status === 'Finished' || (order.status === 'Building' && allPartsAlreadyReady);
    document.getElementById('section-order-status').classList.toggle('hidden', !showQCBtn);

    document.getElementById('section-cancel-order').classList.toggle('hidden', order.status === 'Cancelled');

    renderPartsList(order.parts);
    document.getElementById('modal-save-msg').classList.add('hidden');
    openModal('edit-backdrop');
}

// ── Parts list ──────────────────────────────────────────────────────────
function renderPartsList(parts) {
    const list = document.getElementById('modal-parts-list');
    list.innerHTML = '';

    parts.forEach((part, idx) => {
        const isReady    = part.status === 'Ready';
        const isSourcing = part.status === 'Sourcing';
        const isMissing  = part.status === 'Missing';

        const dotColor  = isReady ? 'bg-green-500' : isSourcing ? 'bg-yellow-400' : 'bg-red-500';
        const textColor = isReady ? 'text-green-600' : isSourcing ? 'text-yellow-600' : 'text-red-500';

        const toggleBtn = isSourcing
            ? `<button onclick="markReady(${idx})" id="toggle-${idx}"
                       class="flex-shrink-0 ml-3 px-3 py-1 rounded-full text-[10px] font-semibold
                              border border-green-500 text-green-600
                              hover:bg-green-500 hover:text-white transition-colors">
                   Mark Ready
               </button>`
            : isMissing
            ? `<span class="flex-shrink-0 ml-3 px-3 py-1 rounded-full text-[10px] font-semibold
                           bg-red-100 text-red-400 border border-red-200 cursor-not-allowed"
                    title="Out of stock — cannot change">
                   Out of Stock
               </span>`
            : `<span class="flex-shrink-0 ml-3 px-2 py-1 rounded-full text-[10px] font-semibold
                           bg-green-100 text-green-600">
                   ✓ Ready
               </span>`;

        list.innerHTML += `
            <div id="part-row-${idx}"
                 class="flex items-center justify-between px-3 py-2.5 rounded-lg
                        bg-nexora-slate-200 border border-nexora-corporate/20">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span id="dot-${idx}" class="w-2 h-2 rounded-full flex-shrink-0 ${dotColor}"></span>
                    <span class="text-sm text-nexora-deep-navy font-medium">${part.category} →</span>
                    <span class="text-xs text-nexora-navy-mid truncate">${part.name}</span>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span id="status-label-${idx}" class="text-xs font-medium ${textColor}">${part.status}</span>
                    ${toggleBtn}
                </div>
            </div>`;
    });
}

function markReady(partIdx) {
    pendingChanges[partIdx] = 'Ready';

    document.getElementById(`dot-${partIdx}`).className            = 'w-2 h-2 rounded-full flex-shrink-0 bg-green-500';
    document.getElementById(`status-label-${partIdx}`).className   = 'text-xs font-medium text-green-600';
    document.getElementById(`status-label-${partIdx}`).textContent = 'Ready';

    const btn = document.getElementById(`toggle-${partIdx}`);
    if (btn) {
        btn.outerHTML = `<span class="flex-shrink-0 ml-3 px-2 py-1 rounded-full text-[10px] font-semibold
                                     bg-green-100 text-green-600">✓ Ready</span>`;
    }

    // Reveal Send to QC if all parts are now ready
    if (editingOrderIndex !== null) {
        const order       = workOrdersData[editingOrderIndex];
        const allNowReady = order.parts.every((p, idx) => (pendingChanges[idx] ?? p.status) === 'Ready');
        if (allNowReady && order.status === 'Building') {
            document.getElementById('section-order-status').classList.remove('hidden');
        }
    }
}

// ── Send to QC ──────────────────────────────────────────────────────────
async function sendToQC() {
    if (editingOrderIndex === null) return;

    pendingQC = true;
    const statusEl = document.getElementById('modal-order-status');
    statusEl.textContent = 'QC Check';
    statusEl.className   = 'px-2.5 py-1 rounded-full text-xs font-bold bg-blue-400 text-blue-900';
    document.getElementById('section-order-status').classList.add('hidden');

    // This is a terminal workflow action, not merely a modal preference.
    // Submit it immediately so a worker cannot close the dialog and lose the
    // transition from Building/Finished to QC Check.
    await saveChanges();
}

// ── Save ────────────────────────────────────────────────────────────────
async function saveChanges() {
    if (editingOrderIndex === null) return;
    if (Object.keys(pendingChanges).length === 0 && !pendingQC) {
        closeModal('edit-backdrop');
        return;
    }

    const order      = workOrdersData[editingOrderIndex];
    const allReady   = order.parts.every((part, idx) => (pendingChanges[idx] ?? part.status) === 'Ready');
    const autoFinish = allReady && order.status === 'Building';

    const payload = {
        workOrderId: order.id,
        orderIndex:  editingOrderIndex,
        partChanges: pendingChanges,
        sendToQC:    pendingQC,
        autoFinish,
        _token:      document.querySelector('meta[name="csrf-token"]').content,
    };

    try {
        const res  = await fetch('/manufacturing/update-order', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body:    JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('modal-save-msg').classList.remove('hidden');
            setTimeout(() => window.location.reload(), 800);
        } else {
            alert('Save failed: ' + (data.message ?? 'Unknown error'));
        }
    } catch (err) {
        alert('Network error — could not save changes.');
        console.error(err);
    }
}

// ── Helpers ─────────────────────────────────────────────────────────────
function getStatusPill(status) {
    const map = {
        'Building': 'bg-yellow-400 text-yellow-900',
        'Pending':  'bg-red-500 text-white',
        'Finished': 'bg-green-500 text-white',
        'Completed':'bg-green-500 text-white',
        'Rework':   'bg-orange-500 text-white',
        'QC Check': 'bg-blue-400 text-blue-900',
        'Cancelled':'bg-gray-400 text-gray-900',
    };
    return map[status] ?? 'bg-gray-300 text-gray-800';
}

// ── Cancel order ────────────────────────────────────────────────────────
function confirmCancelOrder() {
    if (editingOrderIndex === null) return;
    const order = workOrdersData[editingOrderIndex];

    openConfirmModal(
        `This will mark "${order.name}" (${order.id}) as Cancelled. This cannot be undone from here.`,
        () => cancelOrder(editingOrderIndex),
        { title: 'Cancel this build?', confirmLabel: 'Yes, Cancel Order', dangerous: true }
    );
}

async function cancelOrder() {
    const order = workOrdersData[editingOrderIndex];
    const payload = {
        cancelOrder: true,
        workOrderId: order.id,
        orderIndex:  editingOrderIndex,
        _token: document.querySelector('meta[name="csrf-token"]').content,
    };

    try {
        const res  = await fetch('/manufacturing/cancel-order', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body:    JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            closeModal('edit-backdrop');
            showSuccess('Build cancelled.');
            setTimeout(() => window.location.reload(), 800);
        } else {
            alert('Failed to cancel: ' + (data.message ?? 'Unknown error'));
        }
    } catch (err) {
        alert('Network error — could not cancel order.');
        console.error(err);
    }
}
