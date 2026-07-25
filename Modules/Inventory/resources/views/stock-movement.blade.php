@extends('inventory::layouts.dashboard')

@section('title', 'Movements')

@section('content')
<div class="inv-page">
    <!-- KPI tiles -->
    <div class="kpi-row">
        <div class="kpi-tile" style="--accent:#22c55e;">
            <div class="kpi-head">
                <span class="kpi-label">Total Inbound</span>
                <span class="kpi-icon" style="background:rgba(34,197,94,0.15);color:#22c55e;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($totals['inbound']) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#ef4444;">
            <div class="kpi-head">
                <span class="kpi-label">Total Outbound</span>
                <span class="kpi-icon" style="background:rgba(239,68,68,0.15);color:#ef4444;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format(abs($totals['outbound'])) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:#3b82f6;">
            <div class="kpi-head">
                <span class="kpi-label">Transfer</span>
                <span class="kpi-icon" style="background:rgba(59,130,246,0.15);color:#3b82f6;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8l4 4-4 4"/><path d="M7 16l-4-4 4-4"/></svg>
                </span>
            </div>
            <p class="kpi-value">{{ number_format($totals['transfer']) }}</p>
        </div>
        <div class="kpi-tile" style="--accent:{{ $totals['net'] >= 0 ? '#22c55e' : '#ef4444' }};">
            <div class="kpi-head">
                <span class="kpi-label">Net Change</span>
                <span class="kpi-icon" style="background:{{ $totals['net'] >= 0 ? 'rgba(34,197,94,0.15);color:#22c55e' : 'rgba(239,68,68,0.15);color:#ef4444' }};">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
                </span>
            </div>
            <p class="kpi-value" style="color:{{ $totals['net'] >= 0 ? '#4ade80' : '#f87171' }};">{{ ($totals['net'] > 0 ? '+' : '') . number_format($totals['net']) }}</p>
        </div>
    </div>

    <!-- Data panel -->
    <div class="data-panel">
        <div class="panel-head">
            <span class="panel-title">Movement History</span>
            <span class="panel-count">{{ number_format($movements->total()) }} records</span>
        </div>

        <form method="GET" action="{{ route('inventory.stock-movement') }}" id="filters-form">
            <div class="data-toolbar">
                <div class="tb-search">
                    <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Name, Reference...">
                </div>
                <select name="type" class="tb-select" onchange="document.getElementById('filters-form').submit();">
                    <option value="">Type</option>
                    <option value="inbound" {{ request('type') === 'inbound' ? 'selected' : '' }}>Inbound</option>
                    <option value="outbound" {{ request('type') === 'outbound' ? 'selected' : '' }}>Outbound</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
                <select name="warehouse" class="tb-select" onchange="document.getElementById('filters-form').submit();">
                    <option value="">Warehouses</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ data_get($warehouse, 'id') }}" {{ request('warehouse') == data_get($warehouse, 'id') ? 'selected' : '' }}>{{ data_get($warehouse, 'name') }}</option>
                @endforeach
                </select>
                <select name="date_range" class="tb-select" onchange="document.getElementById('filters-form').submit();">
                    <option value="">Date Range</option>
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                </select>
                @if(request()->anyFilled(['search', 'type', 'warehouse', 'date_range', 'reference']))
                    <a href="{{ route('inventory.stock-movement') }}" class="tb-clear" title="Clear all filters">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>

        <div class="responsive-table" style="min-width:0;">
            <table class="data-grid">
                <thead>
                    <tr>
                        <th data-sort="type" class="col-c">TYPE <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="item_name">ITEM NAME <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="sku">SKU <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="quantity" class="col-r">QUANTITY <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="warehouse">WAREHOUSE <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="reference">REFERENCE <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="performed_by">PERFORMED BY <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                        <th data-sort="created_at" class="col-r">DATE AND TIME <span class="sort-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3.5l-6.5 7h13L12 3.5z"/><path d="M12 20.5l6.5-7h-13l6.5 7z"/></svg></span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td class="col-c">
                                @if ($movement->type === 'inbound')
                                    <span class="mv-badge" style="background:#F0FFF5;color:#0CAE57;border:1px solid rgba(12,174,87,0.5);">Inbound</span>
                                @elseif ($movement->type === 'outbound')
                                    <span class="mv-badge" style="background:#F0FFF5;color:#DC2626;border:1px solid rgba(220,38,38,0.5);">Outbound</span>
                                @elseif ($movement->type === 'adjustment')
                                    <span class="mv-badge" style="background:#F0FFF5;color:#D97706;border:1px solid rgba(217,119,6,0.5);">Adjustment</span>
                                @else
                                    <span class="mv-badge" style="background:#F0FFF5;color:#3B82F6;border:1px solid rgba(59,130,246,0.5);">Transfer</span>
                                @endif
                            </td>
                            <td class="cell-strong">{{ $movement->item?->name ?? 'N/A' }}</td>
                            <td class="cell-muted">{{ $movement->item?->sku ?? '—' }}</td>
                            <td class="col-r">
                                @if ($movement->type === 'inbound')
                                    <span class="mv-qty" style="color:#0CAE57;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                                        {{ number_format($movement->quantity) }}
                                    </span>
                                @elseif ($movement->type === 'outbound')
                                    <span class="mv-qty" style="color:#DC2626;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                                        {{ number_format($movement->quantity) }}
                                    </span>
                                @elseif ($movement->type === 'adjustment')
                                    @if ($movement->quantity >= 0)
                                        <span class="mv-qty" style="color:#0CAE57;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                                            {{ number_format(abs($movement->quantity)) }}
                                        </span>
                                    @else
                                        <span class="mv-qty" style="color:#DC2626;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                                            {{ number_format(abs($movement->quantity)) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="mv-qty" style="color:#2563EB;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8l4 4-4 4"/><path d="M7 16l-4-4 4-4"/></svg>
                                        {{ number_format($movement->quantity) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($movement->type === 'transfer')
                                    {{ $movement->transfer_warehouses_display ?? 'N/A' }}
                                @else
                                    {{ $movement->warehouse?->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="cell-muted">{{ $movement->reference ?? '-' }}</td>
                            <td>{{ $movement->performer?->username ?? $movement->performer?->name ?? 'System' }}</td>
                            <td class="col-r cell-muted">{{ $movement->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="8">No stock movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel-foot">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection
