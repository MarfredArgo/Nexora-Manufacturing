function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}

function handleBackdropClick(event, id) {
    if (event.target === event.currentTarget) closeModal(id);
}

document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.modal-backdrop:not(.hidden)').forEach(el => {
        closeModal(el.id);
    });
});

function showSuccess(msg) {
    document.getElementById('success-text').textContent = msg;
    document.getElementById('success-notif').classList.remove('hidden');
}

function closeSuccessNotif() {
    document.getElementById('success-notif').classList.add('hidden');
}

function showOrder(index) {
    document.querySelectorAll('[id^="detail-"]').forEach(el => el.classList.add('hidden'));
    const detailPanel = document.getElementById('detail-' + index);
    if (detailPanel) detailPanel.classList.remove('hidden');

    document.querySelectorAll('[id^="card-"]').forEach(el => {
        el.classList.remove('bg-nexora-steel-blue/80');
        el.classList.add('hover:bg-nexora-steel-blue/50', 'hover:-translate-y-[2px]', 'hover:shadow-md');
    });

    const activeCard = document.getElementById('card-' + index);
    if (activeCard) {
        activeCard.classList.add('bg-nexora-steel-blue/80');
        activeCard.classList.remove('hover:bg-nexora-steel-blue/50', 'hover:-translate-y-[2px]', 'hover:shadow-md');
    }

    if (document.getElementById('assignment-banner')) {
        selectedOrderIndex  = index;
        selectedWorkerIndex = null;
        history.replaceState({}, '', `?page=orders&sub=assignment&order=${index}`);
        updateAssignmentBanner();
        updateWorkerSelectionHighlight();
    }
}

let currentFilter = 'all';

function filterOrders(status) {
    currentFilter = status;
    const searchEl = document.getElementById('search-input');
    const search   = searchEl ? searchEl.value.toLowerCase() : '';

    document.querySelectorAll('[id^="card-"]').forEach(card => {
        const matchesStatus = status === 'all' || card.dataset.status === status;
        const matchesSearch = card.dataset.name.toLowerCase().includes(search);
        card.classList.toggle('hidden', !(matchesStatus && matchesSearch));
    });

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-nexora-corporate', 'text-white');
        btn.classList.add('text-nexora-deep-navy');
    });

    const activeBtn = document.querySelector(`[data-filter="${status}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-nexora-corporate', 'text-white');
        activeBtn.classList.remove('text-nexora-deep-navy');
    }

    reanimateRows();
}

function reanimateRows() {
    const visibleRows = document.querySelectorAll('.row-animate:not(.hidden)');
    visibleRows.forEach(row => row.classList.remove('animate', 'done'));
    setTimeout(() => {
        visibleRows.forEach((row, i) => {
            setTimeout(() => row.classList.add('animate'), i * 20);
        });
    }, 20);
}

function initRowAnimations() {
    document.querySelectorAll('.row-animate').forEach(row => {
        row.addEventListener('animationend', () => row.classList.add('done'));
    });
    reanimateRows();
}

// ── Universal Confirm Modal ────────────────────────────────────────────────
let _confirmCallback = null;

function openConfirmModal(message, callback, options = {}) {
    document.getElementById('universal-confirm-title').textContent   = options.title || 'Are you sure?';
    document.getElementById('universal-confirm-message').textContent = message;

    const confirmBtn = document.getElementById('universal-confirm-btn');
    confirmBtn.textContent = options.confirmLabel || 'Confirm';
    confirmBtn.className = options.dangerous
        ? 'px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-danger text-white hover:opacity-90 transition-colors'
        : 'px-4 py-1.5 rounded-full text-xs font-semibold bg-nexora-corporate text-white hover:bg-nexora-navy-mid transition-colors';

    _confirmCallback = callback;
    openModal('universal-confirm-backdrop');
}

function runConfirmedAction() {
    closeModal('universal-confirm-backdrop');
    const callback = _confirmCallback;
    _confirmCallback = null;
    if (typeof callback === 'function') callback();
}
