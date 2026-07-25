// assignment.js
// Handles the Assignment sub-page: order selection, worker selection, assign, CRUD modals.

let selectedOrderIndex  = (typeof CURRENT_SELECTED !== 'undefined') ? CURRENT_SELECTED : -1;
let selectedWorkerIndex = null;

// ── Order selection ────────────────────────────────────────────────────────
function cancelOrderSelection() {
    selectedOrderIndex  = -1;
    selectedWorkerIndex = null;
    history.replaceState({}, '', `?page=orders&sub=assignment`);

    document.querySelectorAll('[id^="card-"]').forEach(el => {
        el.classList.remove('bg-nexora-steel-blue/80');
        el.classList.add('hover:bg-nexora-steel-blue/50', 'hover:-translate-y-[2px]', 'hover:shadow-md');
    });

    updateAssignmentBanner();
    updateWorkerSelectionHighlight();
}

function updateAssignmentBanner() {
    const banner     = document.getElementById('assignment-banner');
    const header     = document.getElementById('worker-mgmt-header');
    const instr      = document.getElementById('assign-instructions');
    const confirmBtn = document.getElementById('confirm-assign-btn');

    if (selectedOrderIndex >= 0 && workOrdersData[selectedOrderIndex]) {
        const order = workOrdersData[selectedOrderIndex];
        document.getElementById('assignment-order-id').textContent   = order.id;
        document.getElementById('assignment-order-name').textContent = order.name;
        banner.classList.remove('hidden');
        header.classList.add('hidden');
        instr.classList.remove('hidden');
        confirmBtn.classList.remove('hidden');
    } else {
        banner.classList.add('hidden');
        header.classList.remove('hidden');
        instr.classList.add('hidden');
    }

    updateConfirmButtonState();
}

// ── Search ─────────────────────────────────────────────────────────────────
function filterAssignmentSearch() {
    const search = document.getElementById('searchWO').value.toLowerCase();
    document.querySelectorAll('[id^="card-"]').forEach(card => {
        card.classList.toggle('hidden', !card.dataset.name.toLowerCase().includes(search));
    });
}

// ── Worker card click ──────────────────────────────────────────────────────
function handleWorkerCardClick(workerIndex) {
    if (selectedOrderIndex >= 0) {
        selectedWorkerIndex = (selectedWorkerIndex === workerIndex) ? null : workerIndex;
        updateWorkerSelectionHighlight();
        updateConfirmButtonState();
    } else {
        showSuccess('Select an unassigned work order before choosing a Production staff member.');
    }
}

function updateWorkerSelectionHighlight() {
    document.querySelectorAll('.worker-item').forEach((el, i) => {
        el.classList.toggle('ring-2',                  i === selectedWorkerIndex);
        el.classList.toggle('ring-nexora-corporate',   i === selectedWorkerIndex);
        el.classList.toggle('border-nexora-corporate', i === selectedWorkerIndex);
    });
}

function updateConfirmButtonState() {
    const btn = document.getElementById('confirm-assign-btn');
    if (!btn) return;
    btn.disabled = !(selectedOrderIndex >= 0 && selectedWorkerIndex !== null);
}

function confirmAssignment() {
    if (selectedOrderIndex < 0 || selectedWorkerIndex === null) return;
    assignWorkerToOrder(selectedOrderIndex, selectedWorkerIndex);
}

// ── Assign (save) ──────────────────────────────────────────────────────────
function assignWorkerToOrder(orderIndex, workerIndex) {
    const order  = workOrdersData[orderIndex];
    const worker = workersData[workerIndex];
    if (!order || !worker) return;

    fetch('/manufacturing/assign-worker', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body:    JSON.stringify({ orderId: order.id, workerId: worker.id }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showSuccess(`${worker.name} assigned to ${order.name}`);
            setTimeout(() => location.reload(), 800);
        } else {
            alert('Assignment failed: ' + (data.message ?? 'Unknown error'));
        }
    })
    .catch(err => { alert('Network error — could not assign worker.'); console.error(err); });
}
