@extends('inventory::layouts.dashboard')

@section('title', 'Transfers')

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
                <span class="kpi-label">Total Transfers</span>
                <span class="kpi-icon" style="background:rgba(74,158,232,0.15);color:#4a9ee8;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8l4 4-4 4"/><path d="M7 16l-4-4 4-4"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($totalCount) }}</p>
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
        <div class="kpi-tile" style="--accent:#22c55e;">
            <div class="kpi-head">
                <span class="kpi-label">Approved</span>
                <span class="kpi-icon" style="background:rgba(34,197,94,0.15);color:#22c55e;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($approvedCount) }}</p>
        </div>
    </div>

    <!-- Data panel -->
    <div class="data-panel">
        <div class="panel-head">
            <span class="panel-title">Transfer History</span>
            <span class="panel-count">{{ number_format($transfers->total()) }} records</span>
            <div class="panel-head-actions">
                <button type="button" onclick="openTransferModal()" class="inv-btn inv-btn-primary inv-btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New Transfer
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('inventory.stock-transfers') }}" class="data-toolbar">
            <div class="tb-search">
                <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by Name...">
            </div>
            <select name="status" class="tb-select" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="from_warehouse" class="tb-select" onchange="this.form.submit()">
                <option value="">From Warehouse</option>
                @foreach ($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ ($filters['from_warehouse'] ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                @endforeach
            </select>
            <select name="to_warehouse" class="tb-select" onchange="this.form.submit()">
                <option value="">To Warehouse</option>
                @foreach ($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ ($filters['to_warehouse'] ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                @endforeach
            </select>
            @if(array_filter($filters ?? []))
                <a href="{{ route('inventory.stock-transfers') }}" class="tb-clear" title="Clear all filters">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </a>
            @endif
        </form>

        <div class="responsive-table" style="min-width:0;">
            <table class="data-grid">
                <thead>
                    <tr>
                        <th>TRF.ID</th>
                        <th>ITEM NAME</th>
                        <th>SKU</th>
                        <th>FROM</th>
                        <th>TO</th>
                        <th class="col-r">QUANTITY</th>
                        <th>STATUS</th>
                        <th>APPROVED BY</th>
                        <th class="col-r">DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transfers as $transfer)
                        <tr>
                            <td class="cell-muted">{{ $transfer->reference }}</td>
                            <td class="cell-strong">{{ $transfer->item?->name ?? 'Deleted' }}</td>
                            <td class="cell-muted">{{ $transfer->item?->sku ?? '—' }}</td>
                            <td>{{ $transfer->fromWarehouse?->name ?? 'Deleted' }}</td>
                            <td>{{ $transfer->toWarehouse?->name ?? 'Deleted' }}</td>
                            <td class="col-r cell-strong">{{ $transfer->quantity }}</td>
                            <td>
                                <span class="status-badge status-{{ $transfer->status }}">{{ ucfirst($transfer->status) }}</span>
                            </td>
                            <td class="cell-muted">{{ $transfer->approver?->username ?? $transfer->approver?->name ?? '—' }}</td>
                            <td class="col-r cell-muted">{{ $transfer->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                @error("trf_action_{$transfer->id}")
                                    <p style="color:#ef4444;font-size:11px;margin:0 0 6px 0;">{{ $message }}</p>
                                @enderror
                                @if($transfer->status === 'pending')
                                    <form method="POST" action="{{ route('inventory.stock-transfers.approve', $transfer) }}" style="display:inline;" onsubmit="return confirm('Approve this transfer?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inv-btn inv-btn-success inv-btn-xs">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('inventory.stock-transfers.reject', $transfer) }}" style="display:inline;" onsubmit="return confirm('Reject this transfer?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inv-btn inv-btn-danger inv-btn-xs">Reject</button>
                                    </form>
                                    <form method="POST" action="{{ route('inventory.stock-transfers.cancel', $transfer) }}" style="display:inline;" onsubmit="return confirm('Cancel this transfer request?')">
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
                            <td colspan="10">No stock transfers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel-foot">
            {{ $transfers->links() }}
        </div>
    </div>
</div>

    <div id="transferModal" class="nexora-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="newTransferTitle">
        <div class="nexora-modal">
            <div class="nexora-modal-logo"></div>
            <div class="nexora-modal-header">
                <div class="nexora-modal-heading">
                    <span class="nexora-modal-icon nexora-modal-icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4M3 11V9a4 4 0 014-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 01-4 4H3"/></svg>
                    </span>
                    <h2 id="newTransferTitle" class="nexora-modal-title">New Stock Transfer</h2>
                </div>
                <button type="button" onclick="closeTransferModal()" class="nexora-modal-close" aria-label="Close">&times;</button>
            </div>

            <form method="POST" action="{{ route('inventory.stock-transfers.store') }}" novalidate>
                @csrf

                <div class="nexora-modal-form">
                    <div>
                        <label class="nexora-modal-label">From Warehouse</label>
                        <select name="from_warehouse_id" id="from_warehouse_id" class="nexora-modal-select" required>
                            <option value="">Select Source</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('from_warehouse_id')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Item</label>
                        <select name="item_id" id="item_id" class="nexora-modal-select" required>
                            <option value="">Select Source Warehouse First</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('item_id')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">To Warehouse</label>
                        <select name="to_warehouse_id" class="nexora-modal-select" required>
                            <option value="">Select Destination</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        @error('to_warehouse_id')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Quantity</label>
                        <input type="number" name="quantity" id="transfer_quantity" value="{{ old('quantity') }}" min="1" class="nexora-modal-input" placeholder="e.g. 50" required>
                        <span id="transfer_stock_indicator" style="font-size:11px;color:#64748b;display:none;margin-top:4px;"></span>
                        @error('quantity')<p class="nexora-modal-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="nexora-modal-label">Notes (optional)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="nexora-modal-input" placeholder="Additional details...">
                    </div>
                </div>

                <div class="nexora-modal-actions">
                    <button type="button" onclick="closeTransferModal()" class="nexora-modal-btn-secondary">Cancel</button>
                    <button type="submit" class="nexora-modal-btn-primary">Submit Transfer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const transferModal = document.getElementById('transferModal');
    function openTransferModal() { transferModal.classList.add('open'); }
    function closeTransferModal() { transferModal.classList.remove('open'); }
    transferModal.addEventListener('click', function(e) { if (e.target === this) closeTransferModal(); });

    @if($errors->any())
        openTransferModal();
    @endif

    const stockMap = @json($stockMap ?? []);
    const itemsByWarehouse = @json($itemsByWarehouse);
    const allItems = @json($items);
    const fromWarehouseSelect = document.getElementById('from_warehouse_id');
    const itemSelect = document.getElementById('item_id');
    const transferQuantity = document.getElementById('transfer_quantity');
    const transferStockIndicator = document.getElementById('transfer_stock_indicator');

    function getTransferStock() {
        const wh = fromWarehouseSelect.value;
        const item = itemSelect.value;
        if (wh && item) {
            return stockMap[wh + '-' + item] ?? null;
        }
        return null;
    }

    function clampTransferQuantity() {
        const stock = getTransferStock();
        if (stock !== null) {
            const val = parseInt(transferQuantity.value);
            if (!isNaN(val) && val > stock) {
                transferQuantity.value = stock;
            }
        }
    }

    function updateTransferIndicator() {
        const stock = getTransferStock();

        if (stock !== null) {
            transferStockIndicator.textContent = 'Stock available: ' + stock;
            transferStockIndicator.style.display = 'block';
        } else {
            transferStockIndicator.style.display = 'none';
        }
        clampTransferQuantity();
    }

    function filterItemsByWarehouse() {
        const warehouseId = fromWarehouseSelect.value;
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
            itemSelect.innerHTML = '<option value="">No stock available in this warehouse</option>';
        }

        updateTransferIndicator();
    }

    fromWarehouseSelect.addEventListener('change', filterItemsByWarehouse);
    itemSelect.addEventListener('change', updateTransferIndicator);
    transferQuantity.addEventListener('input', clampTransferQuantity);
    transferQuantity.addEventListener('change', clampTransferQuantity);

    // Run once on load in case old() has a value selected
    filterItemsByWarehouse();
</script>
@endpush
