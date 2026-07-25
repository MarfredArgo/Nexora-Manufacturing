@extends('inventory::layouts.dashboard')

@section('title', 'Adjustments')

@push('styles')
<style>
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; text-transform: uppercase; border:1px solid transparent; }
    .status-pending { background: #F0FFF5; color: #D97706; border-color: rgba(217,119,6,0.5); }
    .status-approved { background: #F0FFF5; color: #0CAE57; border-color: rgba(12,174,87,0.5); }
    .status-rejected { background: #F0FFF5; color: #DC2626; border-color: rgba(220,38,38,0.5); }
    .status-cancelled { background: #F0FFF5; color: #64748B; border-color: rgba(100,116,139,0.5); }

</style>
@endpush

@section('content')
<div class="inv-page">

    <!-- KPI tiles -->
    <div class="kpi-row cols-3">
        <div class="kpi-tile" style="--accent:#4a9ee8;">
            <div class="kpi-head">
                <span class="kpi-label">Total Adjustments</span>
                <span class="kpi-icon" style="background:rgba(74,158,232,0.15);color:#4a9ee8;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($totalCount) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:{{ $netAdjustedUnits >= 0 ? '#22c55e' : '#ef4444' }};">
            <div class="kpi-head">
                <span class="kpi-label">Net Adjusted Units</span>
                <span class="kpi-icon" style="background:{{ $netAdjustedUnits >= 0 ? 'rgba(34,197,94,0.15);color:#22c55e' : 'rgba(239,68,68,0.15);color:#ef4444' }};">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
                </span>
            </div>
            <p class="kpi-value" style="color:{{ $netAdjustedUnits >= 0 ? '#4ade80' : '#f87171' }};">{{ ($netAdjustedUnits >= 0 ? '+' : '') . number_format($netAdjustedUnits) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#f59e0b;">
            <div class="kpi-head">
                <span class="kpi-label">Pending Approval</span>
                <span class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($pendingCount) }}</p>
        </div>
    </div>

    <!-- Data panel -->
    <div class="data-panel">
        <div class="panel-head">
            <span class="panel-title">Adjustment History</span>
            <span class="panel-count">{{ number_format($adjustments->total()) }} records</span>
            <div class="panel-head-actions">
                <button type="button" onclick="openAdjustmentModal()" class="inv-btn inv-btn-primary inv-btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New Adjustment
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('inventory.stock-adjustments') }}" class="data-toolbar">
            <div class="tb-search">
                <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by Name...">
            </div>
            <select name="type" class="tb-select" onchange="this.form.submit()">
                <option value="">Type</option>
                <option value="increase" {{ ($filters['type'] ?? '') === 'increase' ? 'selected' : '' }}>Increase</option>
                <option value="decrease" {{ ($filters['type'] ?? '') === 'decrease' ? 'selected' : '' }}>Decrease</option>
            </select>
            <select name="reason" class="tb-select" onchange="this.form.submit()">
                <option value="">Reason</option>
                <option value="damage" {{ ($filters['reason'] ?? '') === 'damage' ? 'selected' : '' }}>Damage</option>
                <option value="expired" {{ ($filters['reason'] ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="recount" {{ ($filters['reason'] ?? '') === 'recount' ? 'selected' : '' }}>Recount</option>
                <option value="theft" {{ ($filters['reason'] ?? '') === 'theft' ? 'selected' : '' }}>Theft</option>
                <option value="correction" {{ ($filters['reason'] ?? '') === 'correction' ? 'selected' : '' }}>Correction</option>
            </select>
            <select name="warehouse" class="tb-select" onchange="this.form.submit()">
                <option value="">Warehouse</option>
                @foreach ($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ ($filters['warehouse'] ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                @endforeach
            </select>
            <select name="status" class="tb-select" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @if(array_filter($filters ?? []))
                <a href="{{ route('inventory.stock-adjustments') }}" class="tb-clear" title="Clear all filters">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </a>
            @endif
        </form>

        <div class="responsive-table" style="min-width:0;">
            <table class="data-grid">
                <thead>
                    <tr>
                        <th class="col-r">ADJ.ID</th>
                        <th>ITEM NAME</th>
                        <th>SKU</th>
                        <th>WAREHOUSE</th>
                        <th class="col-r">QUANTITY</th>
                        <th>TYPE</th>
                        <th>REASON</th>
                        <th>STATUS</th>
                        <th>APPROVED BY</th>
                        <th class="col-r">DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $adjustment)
                        <tr>
                            <td class="col-r cell-muted">{{ $adjustment->id }}</td>
                            <td class="cell-strong">{{ $adjustment->item?->name ?? 'Deleted' }}</td>
                            <td class="cell-muted">{{ $adjustment->item?->sku ?? '—' }}</td>
                            <td>{{ $adjustment->warehouse?->name ?? 'Deleted' }}</td>
                            <td class="col-r cell-strong">{{ $adjustment->quantity }}</td>
                            <td>{{ ucfirst($adjustment->type) }}</td>
                            <td>{{ ucfirst($adjustment->reason) }}</td>
                            <td>
                                <span class="status-badge status-{{ $adjustment->status }}">{{ ucfirst($adjustment->status) }}</span>
                            </td>
                            <td class="cell-muted">{{ $adjustment->approver?->name ?? '—' }}</td>
                            <td class="col-r cell-muted">{{ $adjustment->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                @error("adj_action_{$adjustment->id}")
                                    <p style="color:#ef4444;font-size:11px;margin:0 0 6px 0;">{{ $message }}</p>
                                @enderror
                                @if($adjustment->status === 'pending')
                                    <form method="POST" action="{{ route('inventory.stock-adjustments.approve', $adjustment) }}" style="display:inline;" onsubmit="return confirm('Approve this adjustment?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inv-btn inv-btn-success inv-btn-xs">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('inventory.stock-adjustments.reject', $adjustment) }}" style="display:inline;" onsubmit="return confirm('Reject this adjustment?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inv-btn inv-btn-danger inv-btn-xs">Reject</button>
                                    </form>
                                    <form method="POST" action="{{ route('inventory.stock-adjustments.cancel', $adjustment) }}" style="display:inline;" onsubmit="return confirm('Cancel this adjustment request?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inv-btn inv-btn-neutral inv-btn-xs">Cancel</button>
                                    </form>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="11">No stock adjustments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel-foot">
            {{ $adjustments->links() }}
        </div>
    </div>
</div>
    <div id="adjustmentModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="newAdjustmentTitle">
        <div class="nexora-modal">
            <div class="nexora-modal-logo"></div>
            <div class="nexora-modal-header">
                <div class="nexora-modal-heading">
                    <span class="nexora-modal-icon nexora-modal-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
                    </span>
                    <h2 id="newAdjustmentTitle" class="nexora-modal-title">New Stock Adjustment</h2>
                </div>
                <button type="button" onclick="closeAdjustmentModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
            </div>

            <form method="POST" action="{{ route('inventory.stock-adjustments.store') }}" novalidate>
                @csrf

                <div class="nexora-modal-form">
                    <div>
                        <label class="nexora-modal-label">Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id" class="nexora-modal-select" required>
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Item</label>
                        <select name="item_id" id="item_id" class="nexora-modal-select" required>
                            <option value="">Select Warehouse First</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('item_id')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Type</label>
                        <select name="type" class="nexora-modal-select" required>
                            <option value="">Select Type</option>
                            <option value="increase" {{ old('type') === 'increase' ? 'selected' : '' }}>Increase</option>
                            <option value="decrease" {{ old('type') === 'decrease' ? 'selected' : '' }}>Decrease</option>
                        </select>
                        @error('type')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Reason</label>
                        <select name="reason" class="nexora-modal-select" required>
                            <option value="">Select Reason</option>
                            <option value="damage" {{ old('reason') === 'damage' ? 'selected' : '' }}>Damage</option>
                            <option value="expired" {{ old('reason') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="recount" {{ old('reason') === 'recount' ? 'selected' : '' }}>Recount</option>
                            <option value="theft" {{ old('reason') === 'theft' ? 'selected' : '' }}>Theft</option>
                            <option value="correction" {{ old('reason') === 'correction' ? 'selected' : '' }}>Correction</option>
                        </select>
                        @error('reason')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Quantity</label>
                        <input type="number" name="quantity" id="adjustment_quantity" value="{{ old('quantity') }}" min="1" class="nexora-modal-input" placeholder="e.g. 50" required>
                        <span id="stock_indicator" style="font-size:11px;color:#64748b;display:none;margin-top:4px;"></span>
                        @error('quantity')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Notes (optional)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="nexora-modal-input" placeholder="Additional details...">
                    </div>
                </div>

                <div class="nexora-modal-actions">
                    <button type="button" onclick="closeAdjustmentModal()" class="nexora-modal-btn-secondary">Cancel</button>
                    <button type="submit" class="nexora-modal-btn-primary">Submit Adjustment</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const adjModal = document.getElementById('adjustmentModal');
    window.openAdjustmentModal = function() { adjModal.classList.add('open'); };
    window.closeAdjustmentModal = function() { adjModal.classList.remove('open'); };
    if (adjModal) adjModal.addEventListener('click', function(e) { if (e.target === this) window.closeAdjustmentModal(); });

    @if($errors->any())
        window.openAdjustmentModal();
    @endif

    const stockMap = @json($stockMap);
    const itemsByWarehouse = @json($itemsByWarehouse);
    const allItems = @json($items);
    const warehouseSelect = document.getElementById('warehouse_id');
    const itemSelect = document.getElementById('item_id');
    const typeSelect = document.querySelector('#adjustmentModal select[name="type"]');
    const quantityInput = document.getElementById('adjustment_quantity');
    const stockIndicator = document.getElementById('stock_indicator');

    function getCurrentStock() {
        const wh = warehouseSelect.value;
        const item = itemSelect.value;
        if (wh && item) {
            return stockMap[wh + '-' + item] ?? null;
        }
        return null;
    }

    function clamp() {
        const stock = getCurrentStock();
        const type = typeSelect.value;
        if (stock !== null && type === 'decrease') {
            const val = parseInt(quantityInput.value);
            if (!isNaN(val) && val > stock) {
                quantityInput.value = stock;
            }
        }
    }

    function updateIndicator() {
        const stock = getCurrentStock();
        stockIndicator.textContent = stock !== null ? 'Stock available: ' + stock : '';
        stockIndicator.style.display = stock !== null ? 'block' : 'none';
        clamp();
    }

    function filterItems() {
        const warehouseId = warehouseSelect.value;
        const currentItemId = itemSelect.value;

        itemSelect.innerHTML = '<option value="">Select Item</option>';

        let availableItems = [];
        if (warehouseId && itemsByWarehouse[warehouseId]) {
            availableItems = itemsByWarehouse[warehouseId];
        } else if (!warehouseId) {
            availableItems = allItems;
        }

        availableItems.forEach(function(item) {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            if (String(item.id) === currentItemId) {
                option.selected = true;
            }
            itemSelect.appendChild(option);
        });

        if (warehouseId && availableItems.length === 0) {
            itemSelect.innerHTML = '<option value="">No items in this warehouse</option>';
        }

        updateIndicator();
    }

    warehouseSelect.addEventListener('change', filterItems);
    itemSelect.addEventListener('change', updateIndicator);
    typeSelect.addEventListener('change', updateIndicator);
    quantityInput.addEventListener('input', clamp);
    quantityInput.addEventListener('change', clamp);

    // Run once on load in case old() has a value selected
    filterItems();
</script>
@endpush
