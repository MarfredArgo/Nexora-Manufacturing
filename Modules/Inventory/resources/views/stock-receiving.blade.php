@extends('inventory::layouts.dashboard')

@section('title', 'Receiving')

@push('styles')
<style>
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; border:1px solid transparent; }
    .status-pending { background: #F0FFF5; color: #D97706; border-color: rgba(217,119,6,0.5); }
    .status-intransit { background: #F0FFF5; color: #3B82F6; border-color: rgba(59,130,246,0.5); }
    .status-approved { background: #F0FFF5; color: #0CAE57; border-color: rgba(12,174,87,0.5); }
    .status-rejected { background: #F0FFF5; color: #DC2626; border-color: rgba(220,38,38,0.5); }

    .shipment-row { cursor: pointer; transition: background 0.15s; }
    .shipment-row:hover { background: #f1f5f9; }

    .modal-items-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:6px; max-height:200px; overflow-y:auto; }
    .modal-items-list li { display:flex; align-items:center; gap:8px; padding:8px 10px; background:#f8fafc; border-radius:8px; font-size:13px; }
    .modal-item-name { font-weight:600; color:#0f172a; flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .modal-item-meta { color:#94a3b8; font-size:11px; flex-shrink:0; }
    .modal-item-qty { background:#e2e8f0; color:#475569; font-weight:700; font-size:12px; padding:2px 10px; border-radius:9999px; flex-shrink:0; }

    .expand-arrow { transition: transform 0.2s ease; display: inline-flex; align-items: center; color: #94a3b8; }
    .expand-arrow.open { transform: rotate(90deg); color: #0b1e3d; }

    .items-detail { display: none; }
    .items-detail.open { display: table-row; }
    .items-detail td { padding: 0 !important; background: #f8fafc; }
    .items-detail-inner { border-top: 1px solid #e2e8f0; padding: 14px 16px 14px 52px; }
    .items-mini-table { width:100%; border-collapse:collapse; font-size:13px; }
    .items-mini-table th { text-align:left; padding:6px 10px; color:#64748b; font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #e2e8f0; }
    .items-mini-table td { padding:6px 10px; color:#334155; border-bottom:1px solid #eef2f7; }
    .items-mini-table tr:last-child td { border-bottom:none; }

    .modal-items-list { margin: 0; padding: 0; list-style: none; font-size: 13px; }
    .modal-items-list li { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .modal-items-list li:last-child { border-bottom: none; }
    .modal-item-name { color: #fff; font-weight: 500; }
    .modal-item-meta { color: rgba(255,255,255,0.45); font-size: 12px; }
    .modal-item-qty { background: rgba(27,111,200,0.20); padding: 2px 8px; border-radius: 6px; font-weight: 600; font-size: 12px; color: #90c8ff; }
</style>
@endpush

@section('content')
<div class="inv-page">

    <!-- KPI tiles -->
    <div class="kpi-row cols-3">
        <div class="kpi-tile" style="--accent:#f59e0b;">
            <div class="kpi-head">
                <span class="kpi-label">Pending Deliveries</span>
                <span class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($pendingCount) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#22c55e;">
            <div class="kpi-head">
                <span class="kpi-label">Received Today</span>
                <span class="kpi-icon" style="background:rgba(34,197,94,0.15);color:#22c55e;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($receivedTodayCount) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#ef4444;">
            <div class="kpi-head">
                <span class="kpi-label">Rejected</span>
                <span class="kpi-icon" style="background:rgba(239,68,68,0.15);color:#ef4444;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($rejectedCount) }}</p>
        </div>
    </div>

    <!-- Data panel -->
    <div class="data-panel">
        <div class="data-toolbar" style="border-bottom:1px solid #eef2f7;">
            <div class="inv-tabs" style="flex-shrink:0;" role="tablist" aria-label="Receiving view">
                <button type="button" id="tabPending" class="inv-tab active" role="tab" aria-selected="true" onclick="switchTab('pending')">Pending</button>
                <button type="button" id="tabHistory" class="inv-tab" role="tab" aria-selected="false" onclick="switchTab('history')">History</button>
            </div>
            <div id="pendingFilters" style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <form method="GET" action="{{ route('inventory.stock-receiving') }}" style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                    <div class="tb-search">
                        <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search shipment or supplier...">
                    </div>
                    <select name="status" class="tb-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="intransit" {{ ($filters['status'] ?? '') === 'intransit' ? 'selected' : '' }}>In Transit</option>
                    </select>
                    @if(array_filter($filters ?? []))
                        <a href="{{ route('inventory.stock-receiving') }}" class="tb-clear">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="responsive-table" style="min-width:0;">
            <table class="data-grid">
                <thead>
                    <tr>
                        <th style="width:32px;"></th>
                        <th>SHIPMENT</th>
                        <th>SUPPLIER</th>
                        <th>ITEMS</th>
                        <th class="col-r">QTY</th>
                        <th>WAREHOUSE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody id="tbodyPending">
                    @forelse ($deliveries as $delivery)
                        @php $isProcessed = $deliveryProcessed[$delivery->id] ?? false; @endphp
                        <tr class="shipment-row" data-po-id="{{ $delivery->purchase_order_id }}" onclick="toggleItems(this)" style="{{ $isProcessed ? 'opacity:0.4;' : '' }}">
                            <td>
                                <span class="expand-arrow" data-arrow="1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                                </span>
                            </td>
                            <td class="cell-strong">{{ $delivery->shipment_number }}</td>
                            <td>{{ $delivery->supplier_name }}</td>
                            <td class="cell-muted">{{ $delivery->po_category ?? '—' }}</td>
                            <td class="col-r cell-strong">{{ $delivery->qty }}</td>
                            <td>{{ $delivery->destination_warehouse_name }}</td>
                            <td>
                                <span class="status-badge status-{{ str_replace(' ', '-', strtolower($delivery->status)) }}">{{ ucfirst($delivery->status) }}</span>
                            </td>
                            <td onclick="event.stopPropagation()">
                                @error("del_action_{$delivery->id}")
                                    <p style="color:#ef4444;font-size:11px;margin:0 0 6px 0;">{{ $message }}</p>
                                @enderror
                                @if(!$isProcessed)
                                    <button type="button" class="inv-btn inv-btn-success inv-btn-xs" onclick="openConfirmModal({{ $delivery->id }}, '{{ $delivery->shipment_number }}', '{{ $delivery->supplier_name }}', '{{ $delivery->destination_warehouse_name }}', {{ $delivery->qty }})">Approve</button>
                                    <button type="button" class="inv-btn inv-btn-danger inv-btn-xs" onclick="openRejectModal({{ $delivery->id }})">Reject</button>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">Processed</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="items-detail" data-po-id="{{ $delivery->purchase_order_id }}">
                            <td colspan="8">
                                <div class="items-detail-inner">
                                    <div style="max-width:600px;">
                                        <table class="items-mini-table">
                                            <thead><tr><th style="width:40%;">Item</th><th style="width:35%;">SKU</th><th style="width:25%;text-align:right;">Qty</th></tr></thead>
                                            <tbody class="items-tbody" data-po-id="{{ $delivery->purchase_order_id }}"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:48px 16px;color:#94a3b8;font-size:14px;">
                                <svg width="48" height="48" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 12px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                No pending deliveries from Procurement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tbody id="tbodyHistory" style="display:none;">
                    @forelse ($history as $entry)
                        <tr>
                            <td></td>
                            <td class="cell-strong">{{ $entry->shipment_number }}</td>
                            <td class="cell-muted">{{ $historySuppliers[$entry->shipment_number] ?? '—' }}</td>
                            <td class="cell-muted">{{ $entry->item?->category?->name ?? '—' }}</td>
                            <td class="col-r cell-strong">{{ $entry->quantity }}</td>
                            <td>{{ $entry->warehouse?->name ?? '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ $entry->status }}">{{ ucfirst($entry->status) }}</span>
                            </td>
                            <td class="cell-muted" style="font-size:12px;">
                                <div>by {{ $entry->processor?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $entry->processed_at?->format('M d, Y h:i A') ?? '—' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:48px 16px;color:#94a3b8;font-size:14px;">No processed records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="pendingPagination">
            @if($deliveries->hasPages())
                <div class="panel-foot">
                    {{ $deliveries->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

    <script>
        const deliveryItems = {!! $deliveryItemsJson !!};

        function toggleItems(el) {
            const row = el.closest('tr.shipment-row');
            const poId = row.dataset.poId;
            const detailRow = row.nextElementSibling;
            const isOpen = detailRow.classList.contains('open');
            const arrow = row.querySelector('.expand-arrow');

            arrow.classList.toggle('open');

            if (isOpen) {
                detailRow.classList.remove('open');
            } else {
                const tbody = detailRow.querySelector('.items-tbody');
                if (!tbody.hasChildNodes()) {
                    const items = deliveryItems[poId] || [];
                    tbody.innerHTML = items.length
                        ? items.map(i => `<tr><td style="font-weight:500;">${i.name}</td><td style="color:#64748b;font-family:monospace;font-size:12px;">${i.sku}</td><td style="text-align:right;font-weight:600;">${i.qty}</td></tr>`).join('')
                        : '<tr><td colspan="3" style="color:#94a3b8;text-align:center;padding:12px;">No items found for this purchase order.</td></tr>';
                }
                detailRow.classList.add('open');
            }
        }

        function switchTab(tab) {
            const tabPending = document.getElementById('tabPending');
            const tabHistory = document.getElementById('tabHistory');
            const tbodyPending = document.getElementById('tbodyPending');
            const tbodyHistory = document.getElementById('tbodyHistory');
            const pendingFilters = document.getElementById('pendingFilters');
            const pendingPagination = document.getElementById('pendingPagination');

            const showPending = tab === 'pending';

            tabPending.classList.toggle('active', showPending);
            tabHistory.classList.toggle('active', !showPending);
            tabPending.setAttribute('aria-selected', showPending ? 'true' : 'false');
            tabHistory.setAttribute('aria-selected', showPending ? 'false' : 'true');

            tbodyPending.style.display = showPending ? '' : 'none';
            tbodyHistory.style.display = showPending ? 'none' : '';
            pendingFilters.style.display = showPending ? '' : 'none';
            pendingPagination.style.display = showPending ? '' : 'none';
        }
    </script>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="confirmReceivingTitle">
        <div class="nexora-modal nexora-modal-md">
            <div class="nexora-modal-logo"></div>
            <div class="nexora-modal-header">
                <div class="nexora-modal-heading">
                    <span class="nexora-modal-icon nexora-modal-icon-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </span>
                    <h2 id="confirmReceivingTitle" class="nexora-modal-title">Approve Receiving</h2>
                </div>
                <button type="button" onclick="closeConfirmModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
            </div>
            <form id="confirmForm" method="POST" action="">
                @csrf
                <div style="padding:16px 24px;">
                    <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #e2e8f0;">
                        <div><strong style="color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Shipment</strong><br><span id="confirmShipment" style="font-size:14px;font-weight:600;color:#0f172a;"></span></div>
                        <div><strong style="color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Supplier</strong><br><span id="confirmSupplier" style="font-size:14px;color:#0f172a;"></span></div>
                        <div><strong style="color:#475569;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Warehouse</strong><br><span id="confirmWarehouse" style="font-size:14px;color:#0f172a;"></span></div>
                    </div>
                    <div style="margin-bottom:8px;"><strong style="color:#475569;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">Items to receive</strong></div>
                    <ul class="modal-items-list" id="confirmItemsList"></ul>
                </div>
                <div class="nexora-modal-actions">
                    <button type="button" onclick="closeConfirmModal()" class="nexora-modal-btn-secondary">Cancel</button>
                    <button type="submit" class="nexora-modal-btn-primary" style="background:#0CAE57;">Confirm &amp; Receive</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="rejectDeliveryTitle">
        <div class="nexora-modal nexora-modal-sm">
            <div class="nexora-modal-logo"></div>
            <div class="nexora-modal-header">
                <div class="nexora-modal-heading">
                    <span class="nexora-modal-icon nexora-modal-icon-red">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </span>
                    <h2 id="rejectDeliveryTitle" class="nexora-modal-title">Reject Delivery</h2>
                </div>
                <button type="button" onclick="closeRejectModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="nexora-modal-form">
                    <div class="nexora-modal-form-full">
                        <label class="nexora-modal-label">Reason <span style="color:#ef4444;">*</span></label>
                        <textarea name="reject_reason" required rows="4" class="nexora-modal-textarea"></textarea>
                        @error('reject_reason')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="nexora-modal-actions">
                    <button type="button" onclick="closeRejectModal()" class="nexora-modal-btn-secondary">Cancel</button>
                    <button type="submit" class="nexora-modal-btn-primary nexora-modal-btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const confirmModal = document.getElementById('confirmModal');
        const rejectModal = document.getElementById('rejectModal');

        function openConfirmModal(id, shipment, supplier, warehouse, qty) {
            document.getElementById('confirmShipment').textContent = shipment;
            document.getElementById('confirmSupplier').textContent = supplier;
            document.getElementById('confirmWarehouse').textContent = warehouse;

            const itemsList = document.getElementById('confirmItemsList');
            const row = document.querySelector(`tr.shipment-row[data-po-id]`);
            if (row) {
                const poId = row.dataset.poId;
                const items = deliveryItems[poId] || [];
                itemsList.innerHTML = items.length
                    ? items.map(i => `<li><span class="modal-item-name">${i.name}</span> <span class="modal-item-meta">${i.sku}</span> <span class="modal-item-qty">${i.qty}</span></li>`).join('')
                    : '<li style="justify-content:center;padding:12px 0;color:#94a3b8;">No items</li>';
            }

            document.getElementById('confirmForm').action = '{{ url("inventory/stock-receiving") }}' + '/' + id + '/approve';
            confirmModal.classList.add('open');
        }
        function closeConfirmModal() {
            confirmModal.classList.remove('open');
        }
        confirmModal.addEventListener('click', function(e) {
            if (e.target === this) closeConfirmModal();
        });

        function openRejectModal(deliveryId) {
            document.getElementById('rejectForm').action = '{{ url("inventory/stock-receiving") }}' + '/' + deliveryId + '/reject';
            rejectModal.classList.add('open');
        }
        function closeRejectModal() {
            rejectModal.classList.remove('open');
            document.getElementById('rejectForm').reset();
        }
        rejectModal.addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
    </script>
@endsection
