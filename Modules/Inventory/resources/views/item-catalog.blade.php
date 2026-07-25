@extends('inventory::layouts.dashboard')

@section('title', 'Products')

@push('styles')
    <style>
        .expand-row {
            display: none;
            opacity: 0;
            transform: translateY(-6px);
            transition: opacity 0.2s ease, transform 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .expand-row.open {
            display: table-row;
            opacity: 1;
            transform: translateY(0);
        }
        .expand-toggle { cursor: pointer; transition: transform 0.2s ease; display: inline-block; }
        .expand-toggle.open { transform: rotate(90deg); }

        /* Catalog tables keep manual striping + the expandable sub-table, so
           they use .catalog-table (a lighter grid) rather than .data-grid to
           avoid its descendant rules leaking into the nested breakdown table. */
        .inv-page .catalog-table { width: 100%; border-collapse: collapse; }
        .inv-page .catalog-table > thead > tr > th {
            background: #13315c; color: #cdd9ee; font-size: 11px; font-weight: 700;
            letter-spacing: 0.05em; text-transform: uppercase; padding: 12px 8px;
        }

        /* Expandable sub-table */
        .expand-cell { padding: 0 !important; }
        .expand-inner { padding: 14px 16px 16px 40px; background: #f8fafc; }
        .expand-table { width: 100%; border-collapse: collapse; }
        .expand-table thead th {
            padding: 9px 10px 7px; font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: #64748b; border-bottom: 1.5px solid #e2e8f0;
            background: transparent;
        }
        .expand-table thead th:first-child { text-align: left; }
        .expand-table thead th:not(:first-child) { text-align: center; }
        .expand-table tbody tr { transition: background 0.15s ease; }
        .expand-table tbody tr:hover { background: #f1f5f9; }
        .expand-table tbody tr:last-child td { border-bottom: none; }
        .expand-table td {
            padding: 9px 10px; font-size: 12px; color: #0f172a;
            border-bottom: 1px solid #e2e8f0; vertical-align: middle;
        }
        .expand-table td:first-child { font-weight: 500; }
        .expand-table td:not(:first-child) { text-align: center; }
        .expand-table .td-reserved { color: #dc2626; font-weight: 600; }
        .expand-empty { text-align: center; padding: 14px; color: #94a3b8; font-size: 12px; }
        .threshold-wrap { display: inline-flex; align-items: center; gap: 6px; }
        .threshold-input {
            padding: 5px 8px; background: #fff; color: #0f172a;
            border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px;
            text-align: center; outline: none; font-family: 'Inter', sans-serif;
            width: 56px; transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .threshold-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }

        /* Delete warning modal — high-impact deterrent */
        .del-warning-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: rgba(239,68,68,0.18); color: #fca5a5;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .del-warning-icon svg { width: 24px; height: 24px; }
        .del-item-badge {
            display: inline-block; margin-top: 8px; padding: 6px 14px;
            background: rgba(239,68,68,0.12); color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.25); border-radius: 8px;
            font-size: 13px; font-weight: 600;
        }
        .del-consequences { margin: 16px 0 0; padding: 0; list-style: none; }
        .del-consequences li {
            position: relative; padding: 5px 0 5px 18px; font-size: 13px;
            color: rgba(255,255,255,0.72); line-height: 1.5;
        }
        .del-consequences li::before {
            content: ''; position: absolute; left: 0; top: 12px;
            width: 6px; height: 6px; border-radius: 50%; background: #ef4444;
        }
        .del-consequences li:last-child {
            margin-top: 8px; font-weight: 700; color: #fca5a5;
        }
        .del-consequences li:last-child::before { background: #fca5a5; }
        .del-divider {
            height: 1px; background: linear-gradient(90deg, transparent, rgba(239,68,68,0.3), transparent);
            margin: 18px 0 0;
        }
        .del-btn-permanent {
            background: #dc2626 !important; color: #fff !important; border-color: #991b1b !important;
            animation: delPulse 1.8s ease-in-out infinite;
        }
        .del-btn-permanent:hover { background: #b91c1c !important; animation: none !important; }
        @keyframes delPulse {
            0%, 100% { box-shadow: 0 4px 12px -4px rgba(220,38,38,0.4); }
            50% { box-shadow: 0 4px 20px 0 rgba(220,38,38,0.7); }
        }
    </style>
@endpush

@section('content')
<div class="inv-page">

    <!-- KPI tiles -->
    <div class="kpi-row cols-3">
        <div class="kpi-tile" style="--accent:#22c55e;">
            <div class="kpi-head">
                <span class="kpi-label">In Stock</span>
                <span class="kpi-icon" style="background:rgba(34,197,94,0.15);color:#22c55e;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($inStockCount) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#f59e0b;">
            <div class="kpi-head">
                <span class="kpi-label">Low Stock</span>
                <span class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($lowStockCount) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#ef4444;">
            <div class="kpi-head">
                <span class="kpi-label">Out of Stock</span>
                <span class="kpi-icon" style="background:rgba(239,68,68,0.15);color:#ef4444;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($outOfStockCount) }}</p>
        </div>
    </div>

    <!-- Inventory Items Card -->
    <div class="data-panel">
        <form method="GET" action="{{ route('inventory.item-catalog') }}" class="data-toolbar">
            <div class="inv-tabs" style="flex-shrink:0;" role="tablist" aria-label="Catalog section">
                <button type="button" id="catTabItems" class="inv-tab active" role="tab" aria-selected="true" onclick="switchCatTab('items')">Items</button>
                <button type="button" id="catTabPacking" class="inv-tab" role="tab" aria-selected="false" onclick="switchCatTab('packing')">Packing</button>
            </div>
            <div id="itemsFilters" style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <div class="tb-search">
                    <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Name, Category...">
                </div>
                <select name="category" class="tb-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="warehouse" class="tb-select" onchange="this.form.submit()">
                    <option value="">All Warehouse</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="tb-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="In Stock" {{ request('status') === 'In Stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="Low Stock" {{ request('status') === 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="Out of Stock" {{ request('status') === 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
                @if(request()->anyFilled(['search', 'category', 'warehouse', 'status']))
                    <a href="{{ route('inventory.item-catalog') }}" class="tb-clear">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Clear
                    </a>
                @endif
            </div>
        </form>

        <!-- Items Table -->
        <div id="itemsTableSection">
            <div class="responsive-table" style="min-width:0;">
                <table class="catalog-table" style="width:100%;table-layout:fixed;border-collapse:collapse;min-width:820px;">
                    <thead>
                        <tr>
                            <th style="width:30px;padding:12px 4px;"></th>
                            <th style="text-align:center;">SKU</th>
                            <th style="text-align:center;">ITEM NAME</th>
                            <th style="text-align:center;">CATEGORY</th>
                            <th style="text-align:center;">AVAILABLE</th>
                            <th style="text-align:center;">UNIT COST</th>
                            <th style="text-align:center;">STATUS</th>
                            <th style="text-align:center;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                        <tr style="background:{{ $loop->even ? '#F4F6FA' : '#ffffff' }}; border-top:1px solid #E2E8F0; cursor:pointer;" onclick="toggleExpand({{ $loop->index }})">
                            <td style="text-align:center;padding:12px 4px;">
                                <span class="expand-toggle" id="toggle-{{ $loop->index }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#64748b"><path d="M8 5v14l11-7z"/></svg>
                                </span>
                            </td>
                            <td style="text-align:center;padding:12px 4px;font-size:13px;color:#132B52;">{{ $item['sku'] ?? '—' }}</td>
                            <td style="text-align:center;padding:12px 4px;font-size:13px;color:#132B52;">{{ $item['name'] }}</td>
                            <td style="text-align:center;padding:12px 4px;font-size:13px;color:#5B7A9D;">{{ $item['category'] }}</td>
                            <td style="text-align:center;padding:12px 4px;font-size:13px;color:#132B52;font-weight:600;">{{ $item['total_available'] }}</td>
                            <td style="text-align:center;padding:12px 4px;font-size:13px;color:#132B52;">&#8369;{{ number_format($item['unit_cost'] ?? 0, 2) }}</td>
                            <td style="text-align:center;padding:12px 8px;">
                                @php
                                    $badgeColors = ['In Stock' => ['bg'=>'#F0FFF5','text'=>'#0CAE57','border'=>'rgba(12,174,87,0.5)'], 'Low Stock' => ['bg'=>'#F0FFF5','text'=>'#D97706','border'=>'rgba(217,119,6,0.5)'], 'Out of Stock' => ['bg'=>'#F0FFF5','text'=>'#DC2626','border'=>'rgba(220,38,38,0.5)']];
                                    $bc = $badgeColors[$item['status']] ?? ['bg'=>'#e2e8f0','text'=>'#64748b','border'=>'#cbd5e1'];
                                @endphp
                                <span style="background:{{ $bc['bg'] }};color:{{ $bc['text'] }};border:1px solid {{ $bc['border'] }};font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;">{{ $item['status'] }}</span>
                            </td>
                            <td style="text-align:center;padding:12px 4px;">
                                <button type="button" class="inv-btn inv-btn-quiet-danger inv-btn-icon inv-btn-sm" title="Delete item" aria-label="Delete {{ $item['name'] }}" onclick="event.stopPropagation();openDeleteModal({{ $item['id'] }}, '{{ $item['sku'] }}', '{{ addslashes($item['name']) }}')">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="expand-row" id="expand-{{ $loop->index }}">
                            <td colspan="8" class="expand-cell">
                                <div class="expand-inner">
                                    <table class="expand-table">
                                        <thead>
                                            <tr>
                                                <th>Warehouse</th>
                                                <th>Available</th>
                                                <th>Reserved</th>
                                                <th>Reorder Threshold</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($item['stock_breakdown'] ?? [] as $row)
                                            <tr>
                                                <td>{{ $row['warehouse'] }}</td>
                                                <td>{{ $row['available'] }}</td>
                                                <td class="td-reserved">{{ $row['reserved'] }}</td>
                                                <td onclick="event.stopPropagation()">
                                                    <form method="POST" action="{{ route('inventory.stock-levels.update', $row['stock_level_id']) }}" class="threshold-wrap">
                                                        @csrf @method('PATCH')
                                                        <input type="number" name="reorder_threshold" value="{{ $row['reorder_threshold'] }}" min="0" class="threshold-input">
                                                        <button type="submit" class="inv-btn inv-btn-primary inv-btn-xs">Save</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    @php $slColors = ['In Stock'=>['bg'=>'#F0FFF5','text'=>'#0CAE57','border'=>'rgba(12,174,87,0.5)'],'Low Stock'=>['bg'=>'#F0FFF5','text'=>'#D97706','border'=>'rgba(217,119,6,0.5)'],'Out of Stock'=>['bg'=>'#F0FFF5','text'=>'#DC2626','border'=>'rgba(220,38,38,0.5)']]; $slc = $slColors[$row['status']] ?? ['bg'=>'#e2e8f0','text'=>'#64748b','border'=>'#cbd5e1']; @endphp
                                                    <span style="background:{{ $slc['bg'] }};color:{{ $slc['text'] }};border:1px solid {{ $slc['border'] }};font-size:10px;font-weight:600;padding:3px 8px;border-radius:20px;">{{ $row['status'] }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="expand-empty">No stock records.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">No items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="panel-foot">
                {{ $items->links() }}
            </div>
        </div>

        <!-- Packing Materials Table -->
        <div id="packingTableSection" style="display:none;">
            <div class="responsive-table" style="min-width:0;">
                <table class="catalog-table" style="width:100%;table-layout:auto;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:center;">NAME</th>
                            <th style="text-align:center;">STOCK QTY</th>
                            <th style="text-align:center;">LOW STOCK AT</th>
                            <th style="text-align:center;">TYPE</th>
                            <th style="text-align:center;">BOX SIZE</th>
                            <th style="text-align:center;">STATUS</th>
                            <th style="text-align:center;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packingMaterials as $pm)
                        <tr style="border-bottom:1px solid #e2e8f0;background:{{ $loop->even ? '#F4F6FA' : '#fff' }};">
                            <td style="text-align:center;padding:12px 6px;font-size:13px;color:#132B52;font-weight:500;">{{ $pm->name }}</td>
                            <td style="text-align:center;padding:12px 6px;font-size:13px;color:#132B52;font-weight:600;">{{ $pm->stock_qty }}</td>
                            <td style="text-align:center;padding:12px 6px;font-size:13px;color:#5B7A9D;">{{ $pm->low_stock_threshold }}</td>
                            <td style="text-align:center;padding:12px 6px;font-size:13px;">
                                <span style="background:{{ $pm->is_box ? '#dbeafe' : '#e2e8f0' }};color:{{ $pm->is_box ? '#1e40af' : '#64748b' }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">{{ $pm->is_box ? 'Box' : 'Material' }}</span>
                            </td>
                            <td style="text-align:center;padding:12px 6px;font-size:13px;color:#5B7A9D;">{{ $pm->box_size ?? '—' }}</td>
                            <td style="text-align:center;padding:12px 6px;">
                                @php $pmStatus = $pm->stock_qty <= 0 ? 'Out of Stock' : ($pm->stock_qty <= $pm->low_stock_threshold ? 'Low Stock' : 'In Stock'); $pmColors = ['In Stock'=>['bg'=>'#F0FFF5','text'=>'#0CAE57','border'=>'rgba(12,174,87,0.5)'],'Low Stock'=>['bg'=>'#F0FFF5','text'=>'#D97706','border'=>'rgba(217,119,6,0.5)'],'Out of Stock'=>['bg'=>'#F0FFF5','text'=>'#DC2626','border'=>'rgba(220,38,38,0.5)']]; $pc = $pmColors[$pmStatus] ?? ['bg'=>'#e2e8f0','text'=>'#64748b','border'=>'#cbd5e1']; @endphp
                                <span style="background:{{ $pc['bg'] }};color:{{ $pc['text'] }};border:1px solid {{ $pc['border'] }};font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;">{{ $pmStatus }}</span>
                            </td>
                            <td style="text-align:center;padding:12px 6px;">
                                <form method="POST" action="{{ route('inventory.item-catalog.packing.destroy', $pm->id) }}" onsubmit="return confirm('Delete this packing material?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inv-btn inv-btn-quiet-danger inv-btn-icon inv-btn-sm" title="Delete packing material" aria-label="Delete {{ $pm->name }}">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">No packing materials yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="deleteWarningModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="deleteWarningTitle">
    <div class="nexora-modal nexora-modal-sm">
        <div class="nexora-modal-logo"></div>
        <div class="nexora-modal-header">
            <div class="nexora-modal-heading">
                <span class="del-warning-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01"/></svg>
                </span>
                <h2 id="deleteWarningTitle" class="nexora-modal-title" style="color:#f87171;font-size:18px;">Permanent Deletion</h2>
            </div>
            <button type="button" onclick="closeDeleteWarningModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
        </div>
        <div class="nexora-modal-text">
            <p style="margin:0;font-weight:600;color:#fca5a5;">This action <u>cannot</u> be undone.</p>
            <div class="del-item-badge">
                <span id="deleteWarningItemName"></span>
            </div>
            <div class="del-divider"></div>
            <ul class="del-consequences">
                <li>Stock levels across all warehouses will be destroyed</li>
                <li>Complete movement history will be erased</li>
                <li>All adjustments, transfers, and reservations will be permanently removed</li>
                <li>This item and every associated record will be lost forever</li>
            </ul>
        </div>
        <div class="nexora-modal-actions">
            <button type="button" onclick="closeDeleteWarningModal()" class="nexora-modal-btn-secondary">Cancel</button>
            <button type="button" onclick="proceedToPasswordModal()" class="nexora-modal-btn-primary del-btn-permanent">Yes, delete permanently</button>
        </div>
    </div>
</div>

<div id="deletePasswordModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="deletePasswordTitle">
    <div class="nexora-modal nexora-modal-sm">
        <div class="nexora-modal-logo"></div>
        <div class="nexora-modal-header">
            <div class="nexora-modal-heading">
                <span class="nexora-modal-icon nexora-modal-icon-red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </span>
                <h2 id="deletePasswordTitle" class="nexora-modal-title" style="color:#f87171;">Confirm Password</h2>
            </div>
            <button type="button" onclick="closeDeletePasswordModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
        </div>
        <div class="nexora-modal-text">
            <p style="margin:0 0 16px;">Enter your password to confirm deletion.</p>
            <input type="password" id="deletePasswordInput" class="nexora-modal-input" placeholder="Enter password" autocomplete="off" style="width:100%;">
            <div id="deletePasswordError" class="nexora-modal-error" style="display:none;"></div>
        </div>
        <form id="deleteForm" method="POST" action="" style="display:none;">
            @csrf @method('DELETE')
        </form>
        <div class="nexora-modal-actions">
            <button type="button" onclick="closeDeletePasswordModal()" class="nexora-modal-btn-secondary">Cancel</button>
            <button type="button" id="deleteConfirmBtn" onclick="confirmDelete()" class="nexora-modal-btn-primary nexora-modal-btn-danger">Confirm</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deleteItemId = null;
    let deleteItemSku = null;

    function openDeleteModal(id, sku, name) {
        deleteItemId = id;
        deleteItemSku = sku;
        document.getElementById('deleteWarningItemName').textContent = sku + ' - ' + name;
        document.getElementById('deleteWarningModal').classList.add('open');
    }

    function closeDeleteWarningModal() {
        document.getElementById('deleteWarningModal').classList.remove('open');
    }

    function proceedToPasswordModal() {
        closeDeleteWarningModal();
        document.getElementById('deletePasswordInput').value = '';
        document.getElementById('deletePasswordError').style.display = 'none';
        document.getElementById('deleteConfirmBtn').disabled = false;
        document.getElementById('deleteConfirmBtn').textContent = 'Confirm';
        document.getElementById('deletePasswordModal').classList.add('open');
        setTimeout(function () {
            document.getElementById('deletePasswordInput').focus();
        }, 250);
    }

    function closeDeletePasswordModal() {
        document.getElementById('deletePasswordModal').classList.remove('open');
    }

    function confirmDelete() {
        var password = document.getElementById('deletePasswordInput').value;
        var btn = document.getElementById('deleteConfirmBtn');
        var errorEl = document.getElementById('deletePasswordError');

        if (!password) {
            errorEl.textContent = 'Please enter your password.';
            errorEl.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Deleting...';
        errorEl.style.display = 'none';

        fetch('{{ url("inventory/item-catalog") }}' + '/' + deleteItemId + '/verify-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password: password })
        })
        .then(function (res) {
            if (res.ok) return res.json();
            return res.json().then(function (data) { throw data; });
        })
        .then(function () {
            document.getElementById('deleteForm').action = '{{ url("inventory/item-catalog") }}' + '/' + deleteItemId;
            document.getElementById('deleteForm').submit();
        })
        .catch(function (data) {
            errorEl.textContent = data.error || 'An error occurred. Please try again.';
            errorEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Confirm';
        });
    }

    document.getElementById('deleteWarningModal').addEventListener('click', function (e) {
        if (e.target === this) closeDeleteWarningModal();
    });

    document.getElementById('deletePasswordModal').addEventListener('click', function (e) {
        if (e.target === this) closeDeletePasswordModal();
    });

    document.getElementById('deletePasswordInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            confirmDelete();
        }
    });

    function toggleExpand(index) {
        document.getElementById('expand-' + index).classList.toggle('open');
        document.getElementById('toggle-' + index).classList.toggle('open');
    }

    function switchCatTab(tab) {
        const tabItems = document.getElementById('catTabItems');
        const tabPacking = document.getElementById('catTabPacking');
        const itemsSection = document.getElementById('itemsTableSection');
        const packingSection = document.getElementById('packingTableSection');
        const itemsFilters = document.getElementById('itemsFilters');

        const showItems = tab === 'items';

        tabItems.classList.toggle('active', showItems);
        tabPacking.classList.toggle('active', !showItems);
        tabItems.setAttribute('aria-selected', showItems ? 'true' : 'false');
        tabPacking.setAttribute('aria-selected', showItems ? 'false' : 'true');

        itemsSection.style.display = showItems ? 'block' : 'none';
        packingSection.style.display = showItems ? 'none' : 'block';
        itemsFilters.style.display = showItems ? 'flex' : 'none';
    }
</script>
@endpush
