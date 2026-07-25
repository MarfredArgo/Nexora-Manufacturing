@extends('inventory::layouts.dashboard')

@section('title', 'Dashboard')

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush

@push('styles')
<style>
    .pbar { background: rgba(0,0,0,0.15); border-radius: 99px; height: 6px; overflow: hidden; }
    .pbar-inner { height: 100%; border-radius: 99px; }
    tr.trow { border-bottom: 1px solid rgba(255,255,255,0.06); cursor: default; }
    tr.trow:last-child { border-bottom: none; }
    tr.trow:hover { background: rgba(255,255,255,0.04); }

    /* Fade-in transition when landing on dashboard after login */
    .responsive-grid-dashboard {
        animation: dashboardFadeIn 0.6s ease-out;
    }
    @keyframes dashboardFadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="inv-page">
<div class="responsive-grid-dashboard">
    <!-- Row 1: 3 stat cards + Critical Alerts -->
    <div class="kpi-tile" style="--accent:#4a9ee8;align-self:start;">
        <div class="kpi-head">
            <span class="kpi-label">Total Items</span>
            <span class="kpi-icon" style="background:rgba(74,158,232,0.15);color:#4a9ee8;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg>
            </span>
        </div>
        <p class="kpi-value">{{ number_format($totalItems) }}</p>
    </div>
    <div class="kpi-tile" style="--accent:#2dd4a8;align-self:start;">
        <div class="kpi-head">
            <span class="kpi-label">Total Stock Unit</span>
            <span class="kpi-icon" style="background:rgba(45,212,168,0.15);color:#2dd4a8;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5"/><path d="M3 17l9 5 9-5"/></svg>
            </span>
        </div>
        <p class="kpi-value">{{ number_format($totalStockUnits) }}</p>
    </div>
    <div class="kpi-tile" style="--accent:#f59e0b;align-self:start;">
        <div class="kpi-head">
            <span class="kpi-label">Low Stock Alerts</span>
            <span class="kpi-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4m0 4h.01"/></svg>
            </span>
        </div>
        <p class="kpi-value">{{ number_format($lowStockAlerts) }}</p>
    </div>
    <!-- Critical Alerts card: spans 2 rows -->
    <div class="stat-card" style="grid-row: span 2; display:flex; flex-direction:column; gap:12px; overflow-y:auto; max-height: 520px;">
        <p style="font-size:15px; white-space: nowrap; margin-bottom:4px;">Critical Alerts</p>

        @forelse ($criticalAlerts as $alert)
            <div style="background:#ffffff;border-radius:16px;padding:14px 16px;">
                <div style="display:flex;justify-content:space-between;align-items:start;">
                    <div>
                        <p style="font-size:14px;font-weight:600;color:#0b1e3d;">{{ $alert['name'] }}</p>
                    </div>
                    <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;{{ $alert['type'] === 'out_of_stock' ? 'background:#fee2e2;color:#dc2626;' : 'background:#fef3c7;color:#d97706;' }}">{{ $alert['type'] === 'out_of_stock' ? 'OUT' : 'LOW' }}</span>
                </div>
                <p style="font-size:11px;color:#64748b;margin-bottom:8px;">{{ $alert['warehouse'] }}</p>
                @php
                    $onHand = $alert['on_hand'];
                    $threshold = $alert['threshold'];
                    if ($alert['type'] === 'out_of_stock') {
                        $percentage = 0;
                    } elseif ($threshold > 0 && $onHand > 0) {
                        $percentage = min(100, ($onHand / ($threshold * 2)) * 100);
                    } else {
                        $percentage = 0;
                    }
                    $hue = min(120, round($percentage * 1.2));
                @endphp
                <div class="pbar"><div class="pbar-inner" style="width:{{ $percentage }}%;background:hsl({{ $hue }}, 80%, 45%);"></div></div>
                <div style="display:flex;justify-content:space-between;margin-top:6px;">
                    <span style="font-size:11px;color:#0b1e3d;">{{ number_format($onHand) }} units</span>
                    <span style="font-size:11px;color:#0b1e3d;">threshold {{ number_format($threshold) }}</span>
                </div>
            </div>
        @empty
            <p style="font-size:13px;color:#94a3b8;">No active alerts.</p>
        @endforelse
    </div>
    <!-- Row 2: Chart spanning 2 columns (beside Critical Alerts) -->
    <div class="stat-card span-2">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
            <p style="font-size:15px;font-weight:600;">Stock Movement Trend</p>
            <select id="trendPeriod" style="background: #1b3a6b; color: #e2e8f0; border: 1px solid #2d4a7a; border-radius: 8px; padding: 6px 12px; font-size: 13px; font-family: 'Inter', sans-serif; cursor: pointer; outline: none;">
                <option value="this_week">This week</option>
                <option value="last_week">Last week</option>
                <option value="this_month">This month</option>
                <option value="last_month">Last month</option>
            </select>
        </div>
        <div class="table-wrapper">
            <div class="chart-container">
                <canvas id="stockMovementChart"></canvas>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <p style="font-size:14px;font-weight:600;margin-bottom:15px; white-space: nowrap;">Warehouse Distribution</p>
        <div class="chart-container">
            <canvas id="warehouseChart"></canvas>
        </div>
    </div>

    <div class="content-card span-4">
        <p class="section-heading">Recent Stock Movement</p>
        <div class="table-wrapper">
            <table class="data-grid" style="width: 100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>TYPE</th>
                        <th>ITEM NAME</th>
                        <th class="col-r">QUANTITY</th>
                        <th>WAREHOUSE</th>
                        <th>REFERENCE</th>
                        <th class="col-r">DATE &amp; TIME</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentMovements as $movement)
                        <tr class="trow">
                            <td style="text-align: center; padding: 12px 8px; color: #000000; font-size:13px;">
                                @if ($movement['type'] === 'inbound')
                                    <span style="display:inline-block;padding:4px 16px;border-radius:14px;background:#F0FFF5;color:#0CAE57;border:1px solid rgba(12,174,87,0.5);font-size:12px;font-weight:500;">Inbound</span>
                                @elseif ($movement['type'] === 'outbound')
                                    <span style="display:inline-block;padding:4px 16px;border-radius:14px;background:#FFF5F5;color:#DC2626;border:1px solid rgba(220,38,38,0.5);font-size:12px;font-weight:500;">Outbound</span>
                                @elseif ($movement['type'] === 'adjustment')
                                    <span style="display:inline-block;padding:4px 16px;border-radius:14px;background:#FEF3C7;color:#D97706;border:1px solid rgba(217,119,6,0.5);font-size:12px;font-weight:500;">Adjustment</span>
                                @elseif ($movement['type'] === 'transfer')
                                    <span style="display:inline-block;padding:4px 16px;border-radius:14px;background:#EFF6FF;color:#2563EB;border:1px solid rgba(37,99,235,0.5);font-size:12px;font-weight:500;">Transfer</span>
                                @else
                                    <span style="display:inline-block;padding:4px 16px;border-radius:14px;background:#E2E8F0;color:#64748B;border:1px solid rgba(100,116,139,0.5);font-size:12px;font-weight:500;">Other</span>
                                @endif
                            </td>
                            <td style="text-align: center; padding: 12px 8px; color: #000000; font-size:13px;">{{ $movement['item_name'] }}</td>
                            <td style="text-align: center; padding: 12px 8px; color: #000000; font-size:13px; font-weight:600;">
                                @if ($movement['type'] === 'inbound')
                                    <span style="display:inline-flex;align-items:center;gap:4px;color:#0CAE57;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                                        {{ number_format($movement['quantity']) }}
                                    </span>
                                @elseif ($movement['type'] === 'outbound')
                                    <span style="display:inline-flex;align-items:center;gap:4px;color:#DC2626;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                                        {{ number_format(abs($movement['quantity'])) }}
                                    </span>
                                @elseif ($movement['type'] === 'adjustment')
                                    @if ($movement['quantity'] >= 0)
                                        <span style="display:inline-flex;align-items:center;gap:4px;color:#0CAE57;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                                            {{ number_format(abs($movement['quantity'])) }}
                                        </span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;gap:4px;color:#DC2626;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                                            {{ number_format(abs($movement['quantity'])) }}
                                        </span>
                                    @endif
                                @elseif ($movement['type'] === 'transfer')
                                    <span style="display:inline-flex;align-items:center;gap:4px;color:#2563EB;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8l4 4-4 4"/><path d="M7 16l-4-4 4-4"/></svg>
                                        {{ number_format($movement['quantity']) }}
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:4px;color:#64748B;">
                                        {{ number_format($movement['quantity']) }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center; padding: 12px 8px; color: #000000; font-size:13px;">{{ $movement['warehouse'] }}</td>
                            <td style="text-align: center; padding: 12px 8px; color: #000000; font-size:13px;">{{ $movement['reference'] }}</td>
                            <td style="text-align: center; padding: 12px 8px; color: #000000; font-size:13px;">{{ $movement['date'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #64748b; font-size:13px;">No recent stock movements.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    Chart.defaults.plugins.datalabels = { display: false };

    const ctx = document.getElementById('stockMovementChart').getContext('2d');
    let trendChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($trendData['labels']),
            datasets: [
                {
                    label: 'Inbound',
                    data: @json($trendData['inbound']),
                    backgroundColor: '#22c55e',
                    borderRadius: 4,
                },
                {
                    label: 'Outbound',
                    data: @json($trendData['outbound']),
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                },
                {
                    label: 'Adjustment',
                    data: @json($trendData['adjustments']),
                    backgroundColor: '#f59e0b',
                    borderRadius: 4,
                },
                {
                    label: 'Transfer',
                    data: @json($trendData['transfers']),
                    backgroundColor: '#9ca3af',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#e2e8f0', font: { size: 12 }, usePointStyle: true, pointStyle: 'circle' }
                },
                datalabels: { display: false }
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: { color: '#94a3b8' },
                    grid: { color: 'rgba(255,255,255,0.06)' }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    min: 0,
                    title: {
                        display: true,
                        text: 'Units Moved',
                        color: '#e2e8f0',
                        font: { size: 12, weight: 600 }
                    },
                    ticks: {
                        color: '#94a3b8',
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value;
                            }
                        }
                    },
                    grid: { color: 'rgba(255,255,255,0.06)' }
                }
            }
        }
    });

    document.getElementById('trendPeriod').addEventListener('change', function() {
        fetch('{{ route("inventory.index.trend-data") }}?period=' + this.value)
            .then(r => r.json())
            .then(data => {
                trendChart.data.labels = data.labels;
                trendChart.data.datasets[0].data = data.inbound;
                trendChart.data.datasets[1].data = data.outbound;
                trendChart.data.datasets[2].data = data.adjustments;
                trendChart.data.datasets[3].data = data.transfers;
                trendChart.update();
            });
    });

    const warehouseCtx = document.getElementById('warehouseChart').getContext('2d');
    new Chart(warehouseCtx, {
        type: 'pie',
        data: {
            labels: @json($warehouseDistribution->pluck('name')),
            datasets: [{
                data: @json($warehouseDistribution->pluck('total')),
                backgroundColor: ['#4a9ee8', '#2dd4a8', '#f0a93e', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1,
            layout: { padding: { top: 24, bottom: 8, left: 8, right: 8 } },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#e2e8f0', font: { size: 12 }, padding: 10, usePointStyle: true, pointStyle: 'circle' }
                },
                datalabels: {
                    display: true,
                    color: '#ffffff',
                    font: { weight: 'bold', size: 14 },
                    anchor: 'end',
                    align: 'start',
                    offset: -10,
                    formatter: function(value) { return value; }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
@endpush
