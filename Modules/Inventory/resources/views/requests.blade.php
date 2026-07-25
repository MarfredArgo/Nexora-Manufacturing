@extends('inventory::layouts.dashboard')

@section('title', 'Requests')

@push('styles')
<style>
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; border:1px solid transparent; }
    .status-pending { background: #F0FFF5; color: #D97706; border-color: rgba(217,119,6,0.5); }
    .status-processing { background: #F0FFF5; color: #3B82F6; border-color: rgba(59,130,246,0.5); }
    .status-completed { background: #F0FFF5; color: #0CAE57; border-color: rgba(12,174,87,0.5); }
    .status-rejected { background: #F0FFF5; color: #DC2626; border-color: rgba(220,38,38,0.5); }
    .status-cancelled { background: #F0FFF5; color: #64748B; border-color: rgba(100,116,139,0.5); }

    .type-restock { background: #dbeafe; color: #1e40af; }
    .type-replacement { background: #fef9c3; color: #854d0e; }
    .type-pill { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }

    .stock-status { font-size: 11px; font-weight: 600; margin-left: 6px; padding: 2px 6px; border-radius: 4px; }
    .stock-out { color: #991b1b; background: #fee2e2; }
    .stock-low { color: #854d0e; background: #fef9c3; }

    .type-toggle { display: flex; gap: 0; background: rgba(255,255,255,0.06); border-radius: 10px; padding: 4px; border: 1px solid rgba(255,255,255,0.06); }

    .item-list { max-height: 220px; overflow-y: auto; border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); padding: 4px; }
    .item-list .il-group-header { padding: 8px 12px 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: rgba(255,255,255,0.35); }
    .item-list .il-item { display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; border-radius: 8px; cursor: pointer; transition: all 0.12s ease; }
    .item-list .il-item:hover { background: rgba(255,255,255,0.06); }
    .item-list .il-item.selected { background: rgba(27,111,200,0.2); }
    .item-list .il-item .il-name { font-size: 13px; font-weight: 600; color: #fff; }
    .item-list .il-item .il-sku { font-size: 11px; color: rgba(255,255,255,0.4); margin-left: 8px; }
    .item-list .il-item .il-stock { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; flex-shrink: 0; }
    .item-list .il-item .il-stock.il-out { color: #fca5a5; background: rgba(239,68,68,0.15); }
    .item-list .il-item .il-stock.il-low { color: #fcd34d; background: rgba(245,158,11,0.15); }
    .item-list .il-empty { padding: 20px 12px; text-align: center; color: rgba(255,255,255,0.3); font-size: 13px; }
    .item-list::-webkit-scrollbar { width: 4px; }
    .item-list::-webkit-scrollbar-track { background: transparent; }
    .item-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
    .type-toggle button { padding: 8px 20px; border: none; border-radius: 7px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; cursor: pointer; background: transparent; color: rgba(255,255,255,0.45); transition: all 0.2s ease; flex: 1; letter-spacing: 0.2px; }
    .type-toggle button:hover { color: rgba(255,255,255,0.75); background: rgba(255,255,255,0.04); }
    .type-toggle button.active { background: rgba(27,111,200,0.3); color: #90c8ff; box-shadow: 0 0 20px -6px rgba(27,111,200,0.25); }
    .type-toggle button.active::after { content: ''; display: block; height: 2px; width: 20px; background: #4a9ee8; margin: 4px auto 0; border-radius: 2px; }

    .restock-fields, .replacement-fields { display: none; }
    .restock-fields.active, .replacement-fields.active { display: block; }

    .req-type-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; margin-right: 8px; flex-shrink: 0; }
    .req-type-icon.restock { background: rgba(27,111,200,0.2); }
    .req-type-icon.replacement { background: rgba(245,158,11,0.2); }

    .priority-high { background: rgba(239,68,68,0.2); color: #fca5a5; }
    .priority-low { background: rgba(245,158,11,0.2); color: #fcd34d; }
    .priority-pill { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }

    .cart-wrap { border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; }
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table thead th { padding: 10px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: rgba(255,255,255,0.35); text-align: left; background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.06); }
    .cart-table thead th.th-qty { text-align: center; width: 80px; }
    .cart-table thead th.th-act { width: 40px; }
    .cart-table tbody tr { transition: background 0.12s ease; }
    .cart-table tbody tr:hover { background: rgba(255,255,255,0.03); }
    .cart-table tbody tr:last-child td { border-bottom: none; }
    .cart-table td { padding: 10px 12px; font-size: 13px; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.06); vertical-align: middle; }
    .cart-type { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; margin-right: 8px; vertical-align: middle; }
    .cart-type.restock { background: rgba(27,111,200,0.2); color: #90c8ff; }
    .cart-type.replacement { background: rgba(245,158,11,0.2); color: #fcd34d; }
    .cart-input { padding: 6px 8px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.06); color: #fff; font-size: 13px; font-family: 'Inter', sans-serif; transition: border-color 0.15s ease, background 0.15s ease; outline: none; }
    .cart-input:focus { border-color: rgba(74,158,232,0.5); background: rgba(255,255,255,0.08); }
    .cart-qty { width: 36px; text-align: center; border: none; background: transparent; color: #fff; font-size: 13px; font-family: 'Inter', sans-serif; outline: none; -moz-appearance: textfield; }
    .cart-qty::-webkit-outer-spin-button, .cart-qty::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .cart-stepper { display: inline-flex; align-items: stretch; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.06); overflow: hidden; }
    .cart-stepper button { background: none; border: none; color: rgba(255,255,255,0.5); width: 26px; cursor: pointer; font-size: 14px; line-height: 1; transition: all 0.12s ease; display: flex; align-items: center; justify-content: center; padding: 0; }
    .cart-stepper button:hover { background: rgba(74,158,232,0.2); color: #90c8ff; }
    .cart-stepper button:active { background: rgba(74,158,232,0.35); }
    .cart-stepper button:first-child { border-right: 1px solid rgba(255,255,255,0.08); }
    .cart-stepper button:last-child { border-left: 1px solid rgba(255,255,255,0.08); }
    .cart-qty-w { text-align: center; }
    .cart-notes { width: 100%; }
    .cart-remove { background: none; border: none; color: rgba(255,255,255,0.2); cursor: pointer; font-size: 18px; line-height: 1; padding: 4px; border-radius: 6px; transition: all 0.12s ease; }
    .cart-remove:hover { color: #ef4444; background: rgba(239,68,68,0.12); }
    .cart-empty { padding: 32px 16px; text-align: center; color: rgba(255,255,255,0.25); font-size: 13px; border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); }
    .cart-empty svg { display: block; margin: 0 auto 10px; opacity: 0.25; }
    .cart-count { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.35); font-weight: 500; margin-left: 8px; }
    .cart-count span { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 9999px; background: rgba(27,111,200,0.2); color: #90c8ff; font-size: 10px; font-weight: 700; }

    .inv-page .kpi-row.cols-2 { grid-template-columns:1fr 1fr; }
    .multi-count { color: #94a3b8; font-weight: 500; font-size: 11px; white-space: nowrap; }
    @media (max-width:520px){ .inv-page .kpi-row.cols-2 { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="inv-page">

    <div class="kpi-row cols-2">
        <div class="kpi-tile" style="--accent:#f59e0b;">
            <div class="kpi-head">
                <span class="kpi-label">Total Requests</span>
                <span class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($totalCount) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#22c55e;">
            <div class="kpi-head">
                <span class="kpi-label">Pending</span>
                <span class="kpi-icon" style="background:rgba(34,197,94,0.15);color:#22c55e;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($pendingCount) }}</p>
        </div>
    </div>

    <div class="data-panel">
        <div class="data-toolbar" style="border-bottom:1px solid #eef2f7;">
            <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <div class="tb-search">
                    <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" id="searchInput" placeholder="Search by item or req #..." oninput="filterRequests()">
                </div>
                <select id="statusFilter" class="tb-select" onchange="filterRequests()">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <button type="button" onclick="openRequestModal()" class="inv-btn inv-btn-primary" style="margin-left:auto;flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                New Request
            </button>
        </div>

        <div class="responsive-table" style="min-width:0;">
            <table class="data-grid">
                <thead>
                    <tr>
                        <th>REQ #</th>
                        <th>TYPE</th>
                        <th>ITEM</th>
                        <th class="col-r">QTY</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody id="requestsBody">
                    @forelse ($requests as $req)
                        <tr class="req-row" data-status="{{ strtolower($req->status ?? 'pending') }}" data-search="{{ strtolower($req->req_id . ' ' . $req->part_name) }}">
                            <td class="cell-strong" style="font-size:11px;">{{ $req->req_id }}</td>
                            <td>@if($req->type)<span class="type-pill type-{{ $req->type }}">{{ ucfirst($req->type) }}</span>@else<span style="color:#94a3b8;">—</span>@endif</td>
                            <td>{!! $req->part_name !!}</td>
                            <td class="col-r cell-strong">{{ $req->quantity }}</td>
                            <td>
                                @php $p = strtolower($req->priority ?? 'low'); @endphp
                                <span class="priority-pill priority-{{ $p }}">{{ $p === 'high' ? 'High' : 'Low' }}</span>
                            </td>
                            <td>
                                @php
                                    $s = strtolower($req->status ?? 'pending');
                                    $bc = match($s) {
                                        'pending' => 'status-pending',
                                        'processing' => 'status-processing',
                                        'completed' => 'status-completed',
                                        'rejected' => 'status-rejected',
                                        'cancelled' => 'status-cancelled',
                                        default => 'status-pending',
                                    };
                                @endphp
                                <span class="status-badge {{ $bc }}">{{ $req->status ?? 'Pending' }}</span>
                            </td>
                            <td class="cell-muted" style="font-size:12px;">{{ \Carbon\Carbon::parse($req->date_requested)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="7" style="text-align:center;padding:48px 16px;color:#94a3b8;font-size:14px;">
                                <svg width="40" height="40" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                No requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel-foot">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<div id="requestModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="newRequestTitle">
    <div class="nexora-modal nexora-modal-md">
        <div class="nexora-modal-logo"></div>
        <div class="nexora-modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="req-type-icon restock" id="headerIconRestock">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4a9ee8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </span>
                <span class="req-type-icon replacement" id="headerIconReplacement" style="display:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </span>
                <h2 id="newRequestTitle" class="nexora-modal-title">New Request</h2>
            </div>
            <button type="button" onclick="closeRequestModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
        </div>
        <form method="POST" action="{{ route('inventory.requests.store') }}" id="requestForm">
            @csrf
            <input type="hidden" name="type" id="requestType" value="restock">

            <div style="padding:0 0 18px 0;">
                <div class="type-toggle">
                    <button type="button" class="active" onclick="setRequestType('restock')" id="typeRestockBtn">Restock</button>
                    <button type="button" onclick="setRequestType('replacement')" id="typeReplacementBtn">Replacement</button>
                </div>
            </div>

            <div class="nexora-modal-form">

                <div style="grid-column:1/-1;">
                    <label class="nexora-modal-label">Select Items</label>
                    <div class="restock-fields active" id="restockFields">
                        <div class="item-list" id="itemList">
                            <div class="il-empty" id="restockLoading">Loading items…</div>
                        </div>
                    </div>
                    <div class="replacement-fields" id="replacementFields">
                        <div class="item-list" id="defectList">
                            <div class="il-empty">No open defects</div>
                        </div>
                    </div>
                </div>

                <div style="grid-column:1/-1;margin-top:8px;">
                    <label class="nexora-modal-label">
                        Items to Request
                        <span class="cart-count" id="cartCount" style="display:none;"><span id="cartCountNum">0</span> items</span>
                    </label>
                    <div id="cartContainer">
                        <div class="cart-wrap" id="cartWrap" style="display:none;">
                            <table class="cart-table" id="cartTable">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="th-qty">Qty</th>
                                        <th>Notes</th>
                                        <th class="th-act"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody"></tbody>
                            </table>
                        </div>
                        <div class="cart-empty" id="cartEmpty">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                            Click items above to add them
                        </div>
                    </div>
                </div>
            </div>
            @error('submit')
                <p style="color:#ef4444;font-size:13px;margin:12px 0 0;padding:10px;background:rgba(239,68,68,0.12);border-radius:8px;">{{ $message }}</p>
            @enderror
            <div class="nexora-modal-actions">
                <button type="button" onclick="closeRequestModal()" class="nexora-modal-btn-secondary">Cancel</button>
                <button type="submit" class="nexora-modal-btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    var restockItems = [];
    var defectItems = [];
    var cart = [];

    function fetchRestockItems() {
        var list = document.getElementById('itemList');
        list.innerHTML = '<div class="il-empty">Loading items…</div>';
        fetch('{{ route('inventory.requests.restock-items') }}')
            .then(function(r) { return r.json(); })
            .then(function(items) {
                restockItems = items;
                renderRestockItems();
            })
            .catch(function() {
                list.innerHTML = '<div class="il-empty">Failed to load items</div>';
            });
    }

    function renderRestockItems() {
        var list = document.getElementById('itemList');
        var oos = restockItems.filter(function(i) { return i.status === 'Out of Stock'; });
        var ls = restockItems.filter(function(i) { return i.status === 'Low Stock'; });
        var html = '';
        if (oos.length) {
            html += '<div class="il-group-header">Out of Stock</div>';
            oos.forEach(function(item) {
                html += '<div class="il-item" data-id="' + item.id + '" onclick="addRestockToCart(' + item.id + ')">'
                    + '<div><span class="il-name">' + escapeHtml(item.name) + '</span><span class="il-sku">' + escapeHtml(item.sku) + '</span></div>'
                    + '<span class="il-stock il-out">0 avail.</span></div>';
            });
        }
        if (ls.length) {
            html += '<div class="il-group-header">Low Stock</div>';
            ls.forEach(function(item) {
                html += '<div class="il-item" data-id="' + item.id + '" onclick="addRestockToCart(' + item.id + ')">'
                    + '<div><span class="il-name">' + escapeHtml(item.name) + '</span><span class="il-sku">' + escapeHtml(item.sku) + '</span></div>'
                    + '<span class="il-stock il-low">' + item.total_available + ' avail.</span></div>';
            });
        }
        if (!oos.length && !ls.length) {
            html = '<div class="il-empty">No items need restocking</div>';
        }
        list.innerHTML = html;
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function fetchReplacementItems() {
        var list = document.getElementById('defectList');
        list.innerHTML = '<div class="il-empty">Loading defects…</div>';
        return fetch('{{ route('inventory.requests.replacement-items') }}')
            .then(function(r) { return r.json(); })
            .then(function(items) {
                defectItems = items;
                renderReplacementItems();
            })
            .catch(function() {
                list.innerHTML = '<div class="il-empty">Failed to load defects</div>';
            });
    }

    function renderReplacementItems() {
        var list = document.getElementById('defectList');
        if (!defectItems.length) {
            list.innerHTML = '<div class="il-empty">No open defects</div>';
            return;
        }
        var html = '';
        defectItems.forEach(function(item) {
            html += '<div class="il-item" data-id="' + item.id + '" onclick="addReplacementToCart(' + item.id + ')">'
                + '<div><span class="il-name">' + escapeHtml(item.part_name) + '</span>'
                + (item.quantity > 1 ? '<span class="il-sku">x' + item.quantity + '</span>' : '')
                + (item.source ? '<span class="il-sku">' + escapeHtml(item.source) + '</span>' : '')
                + '</div></div>';
        });
        list.innerHTML = html;
    }

    function addRestockToCart(id) {
        var item = restockItems.find(function(i) { return i.id === id; });
        if (!item) return;
        cart.push({
            type: 'restock',
            item_id: item.id,
            part_name: item.name,
            quantity: 1,
            notes: ''
        });
        renderCart();
    }

    function addReplacementToCart(id) {
        var item = defectItems.find(function(i) { return i.id === id; });
        if (!item) return;
        cart.push({
            type: 'replacement',
            defect_id: item.id,
            part_name: item.part_name,
            quantity: item.quantity,
            notes: ''
        });
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateCartQty(index, qty) {
        cart[index].quantity = parseInt(qty) || 1;
    }

    function adjustQty(index, delta) {
        var newQty = (cart[index].quantity || 1) + delta;
        if (newQty < 1) newQty = 1;
        cart[index].quantity = newQty;
        renderCart();
    }

    function updateCartNotes(index, notes) {
        cart[index].notes = notes;
    }

    function renderCart() {
        var tbody = document.getElementById('cartBody');
        var wrap = document.getElementById('cartWrap');
        var empty = document.getElementById('cartEmpty');
        var count = document.getElementById('cartCount');
        var countNum = document.getElementById('cartCountNum');
        if (!cart.length) {
            tbody.innerHTML = '';
            wrap.style.display = 'none';
            empty.style.display = '';
            count.style.display = 'none';
            return;
        }
        wrap.style.display = '';
        empty.style.display = 'none';
        count.style.display = '';
        countNum.textContent = cart.length;
        var html = '';
        cart.forEach(function(item, i) {
            var typeClass = item.type === 'restock' ? 'restock' : 'replacement';
            var typeLabel = item.type === 'restock' ? 'RST' : 'RPL';
            html += '<tr>'
                + '<td><span class="cart-type ' + typeClass + '">' + typeLabel + '</span>' + escapeHtml(item.part_name) + '</td>'
                + '<td class="cart-qty-w"><div class="cart-stepper"><button type="button" onclick="adjustQty(' + i + ',-1)" tabindex="-1">−</button><input type="number" name="items[' + i + '][quantity]" value="' + item.quantity + '" min="1" onchange="updateCartQty(' + i + ',this.value)" class="cart-qty"><button type="button" onclick="adjustQty(' + i + ',1)" tabindex="-1">+</button></div></td>'
                + '<td><input type="text" name="items[' + i + '][notes]" value="' + escapeHtml(item.notes) + '" placeholder="Add note…" onchange="updateCartNotes(' + i + ',this.value)" class="cart-input cart-notes"></td>'
                + '<td><button type="button" onclick="removeFromCart(' + i + ')" class="cart-remove" title="Remove item">&times;</button></td>'
                + '</tr>';
            html += '<input type="hidden" name="items[' + i + '][type]" value="' + item.type + '">';
            html += '<input type="hidden" name="items[' + i + '][part_name]" value="' + escapeHtml(item.part_name) + '">';
            if (item.item_id) html += '<input type="hidden" name="items[' + i + '][item_id]" value="' + item.item_id + '">';
            if (item.defect_id) html += '<input type="hidden" name="items[' + i + '][defect_id]" value="' + item.defect_id + '">';
        });
        tbody.innerHTML = html;
    }

    function setRequestType(type) {
        if (document.getElementById('requestType').value === type) return;
        document.getElementById('requestType').value = type;
        document.getElementById('typeRestockBtn').classList.toggle('active', type === 'restock');
        document.getElementById('typeReplacementBtn').classList.toggle('active', type === 'replacement');
        document.getElementById('restockFields').classList.toggle('active', type === 'restock');
        document.getElementById('replacementFields').classList.toggle('active', type === 'replacement');
        document.getElementById('headerIconRestock').style.display = type === 'restock' ? '' : 'none';
        document.getElementById('headerIconReplacement').style.display = type === 'replacement' ? '' : 'none';
        cart = [];
        renderCart();
    }

    function openRequestModal() {
        document.getElementById('requestModal').classList.add('open');
        fetchRestockItems();
        fetchReplacementItems();
    }

    function closeRequestModal() {
        document.getElementById('requestModal').classList.remove('open');
        document.getElementById('requestForm').reset();
        cart = [];
        renderCart();
        document.getElementById('restockFields').classList.add('active');
        document.getElementById('replacementFields').classList.remove('active');
        document.getElementById('typeRestockBtn').classList.add('active');
        document.getElementById('typeReplacementBtn').classList.remove('active');
        document.getElementById('headerIconRestock').style.display = '';
        document.getElementById('headerIconReplacement').style.display = 'none';
    }

    document.getElementById('requestModal').addEventListener('click', function (e) {
        if (e.target === this) closeRequestModal();
    });

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() { openRequestModal(); });
    @endif

    function filterRequests() {
        var q = document.getElementById('searchInput').value.toLowerCase();
        var st = document.getElementById('statusFilter').value.toLowerCase();
        var rows = document.querySelectorAll('.req-row');
        var visible = 0;
        rows.forEach(function(r) {
            var matchSearch = !q || r.getAttribute('data-search').includes(q);
            var matchStatus = !st || r.getAttribute('data-status') === st;
            r.style.display = (matchSearch && matchStatus) ? '' : 'none';
            if (matchSearch && matchStatus) visible++;
        });
        var empty = document.getElementById('emptyRow');
        if (empty) empty.style.display = visible > 0 ? 'none' : '';
    }
</script>
@endsection
