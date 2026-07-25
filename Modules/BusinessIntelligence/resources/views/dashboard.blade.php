@extends('bi::layouts.app')

@section('content')
    <div id="dashboard-view" class="tab-content active-tab" style="display:block;">
        <div class="subheader-bar">
            <div class="subheader-title">
                <h3>Executive Dashboard</h3>
                <p>Real-time overview of business performance, sales forecast, and operational efficiency.</p>
            </div>
            <div class="subheader-controls">
                <div class="control-date-selector">
                    <i data-lucide="calendar" class="control-icon-sm"></i>
                    {{ now()->format('M d') }} - {{ now()->addDays(7)->format('M d, Y') }}
                </div>
                <button id="syncNowBtn" class="control-btn" data-tip="Refresh Data" onclick="syncAllDepartments()">
                    <i data-lucide="refresh-cw" class="control-icon"></i>
                </button>
            </div>
        </div>
        <div class="content-container">
            <section class="kpi-grid">
                @foreach($kpis as $kpi)
                    <div class="kpi-card">
                        <div class="kpi-icon-container"><i data-lucide="{{ $kpi['icon'] }}" class="kpi-icon"></i></div>
                        <div class="kpi-details">
                            <div class="kpi-label">{{ $kpi['label'] }}</div>
                            <div class="kpi-value">{{ $kpi['value'] }}</div>
                            <div class="kpi-change {{ $kpi['change_class'] }}">{{ $kpi['change'] }}</div>
                        </div>
                    </div>
                @endforeach
            </section>

            <div class="dashboard-layout-grid">
                <div class="section-column">
                    {{-- Historical Sales Trend --}}
                    <div class="ui-card" style="flex: none;">
                        <div class="card-header">
                            <div class="card-title">Historical Sales Trend <span class="info-dot"
                                    data-tooltip="Tracks invoice revenue over the selected time period.">i</span></div>
                            <select id="salesRange" class="control-date-selector chart-range-select"
                                onchange="changeSalesRange()">
                                <option value="7d">7 Days</option>
                                <option value="1m">1 Month</option>
                                <option value="1y">1 Year</option>
                            </select>
                        </div>
                        <div class="placeholder-graph-box chart-box"><canvas id="salesTrendChart"></canvas></div>
                        <div class="forecast-sub-row">
                            <div class="sub-box">
                                <div class="sub-box-label"><i data-lucide="wallet" class="sub-icon"></i>Revenue (paid)</div>
                                <div class="sub-box-val" style="color: var(--success);">
                                    ₱{{ number_format($metrics['revenue'], 0) }}</div>
                            </div>
                            <div class="sub-box">
                                <div class="sub-box-label"><i data-lucide="file-text" class="sub-icon"></i>Invoiced</div>
                                <div class="sub-box-val" style="color: var(--corporate-blue);">
                                    ₱{{ number_format($metrics['invoiced'], 0) }}</div>
                            </div>
                            <div class="sub-box">
                                <div class="sub-box-label"><i data-lucide="trending-down" class="sub-icon"></i>Expenses</div>
                                <div class="sub-box-val" style="color: var(--warning);">
                                    ₱{{ number_format($metrics['expenses'], 0) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Products Driving Growth --}}
                    <div class="ui-card fixed-target-height-card" style="flex: 1;">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Products Driving Growth <span class="info-dot"
                                        data-tooltip="Top selling products ranked by units sold this month with inventory coverage and revenue data.">i</span>
                                </div>
                                <p style="font-size: 11px; color: var(--slate-500); margin-top: 2px;">Top 10 Products</p>
                            </div>
                        </div>
                        <div class="scrollable-card-body">
                            <table class="product-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Units Sold</th>
                                        <th>vs Last 30 Days</th>
                                        <th class="text-right">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $index => $product)
                                        @php
                                            $change = $product['prev_units'] > 0 ? round((($product['units_sold'] - $product['prev_units']) / $product['prev_units']) * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $index + 1 }}.</strong> {{ $product['name'] }}</td>
                                            <td>{{ number_format($product['units_sold']) }}</td>
                                            <td>
                                                <span class="{{ $change >= 0 ? 'change-up' : 'change-down' }}"
                                                    data-tip="{{ number_format($product['prev_units']) }} units prior 30 days → {{ number_format($product['units_sold']) }} units last 30 days">
                                                    {{ $change >= 0 ? '↑' : '↓' }} {{ abs($change) }}%
                                                </span>
                                            </td>
                                            <td class="text-right">₱{{ number_format($product['revenue']) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="text-align:center;color:var(--slate-500);padding:2rem;">No
                                                product data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="section-column">
                    {{-- Operational Efficiency --}}
                    <div class="ui-card" style="flex: none;">
                        <div class="card-header">
                            <div class="card-title">Operational Efficiency <span class="info-dot"
                                    data-tooltip="Comprehensive overview of operational health, manufacturing, and fulfillment performance.">i</span>
                            </div>
                        </div>
                        <div class="op-health-row">
                            <div class="op-health-card">
                                <div class="op-donut op-donut-lg">
                                    <svg viewBox="0 0 36 36" class="op-donut-svg">
                                        <path class="op-donut-track"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="op-donut-fill {{ $operationalEfficiency['overall']['class'] }}"
                                            stroke-dasharray="{{ $operationalEfficiency['overall']['percent'] }}, 100"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <span
                                        class="op-donut-text op-donut-text-lg">{{ $operationalEfficiency['overall']['percent'] }}%</span>
                                </div>
                                <div class="op-health-info">
                                    <h4>Overall Efficiency</h4>
                                    <div class="op-health-badge {{ $operationalEfficiency['overall']['class'] }}">
                                        {{ $operationalEfficiency['overall']['status'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="op-summary-card {{ $operationalEfficiency['overall']['class'] }}">
                                <div class="op-summary-header">
                                    @php
                                        $summaryIcon = match ($operationalEfficiency['overall']['class']) {
                                            'health-green' => 'check-circle',
                                            'health-yellow' => 'alert-circle',
                                            'health-orange' => 'alert-triangle',
                                            default => 'x-circle',
                                        };
                                    @endphp
                                    <i data-lucide="{{ $summaryIcon }}" class="op-summary-check"></i>
                                    <h4>Operations Summary</h4>
                                </div>
                                <p>{{ $operationalEfficiency['summary_text'] }}</p>
                            </div>
                        </div>
                        <div class="op-health-row">
                            <div class="op-dept-full-card">
                                <div class="op-health-card" style="border:none; padding:0 0 0.75rem 0;">
                                    <div class="op-donut">
                                        <svg viewBox="0 0 36 36" class="op-donut-svg">
                                            <path class="op-donut-track"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                            <path
                                                class="op-donut-fill {{ $operationalEfficiency['manufacturing']['class'] }}"
                                                stroke-dasharray="{{ $operationalEfficiency['manufacturing']['percent'] }}, 100"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        </svg>
                                        <span
                                            class="op-donut-text">{{ $operationalEfficiency['manufacturing']['percent'] }}%</span>
                                    </div>
                                    <div class="op-health-info">
                                        <h4>Manufacturing Health</h4>
                                        <div class="op-health-badge {{ $operationalEfficiency['manufacturing']['class'] }}">
                                            {{ $operationalEfficiency['manufacturing']['health'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="op-metrics-mini">
                                    @foreach($operationalEfficiency['manufacturing']['metrics'] as $metric)
                                        <div class="op-metric-item">
                                            <i data-lucide="{{ $metric['icon'] }}" class="op-metric-icon"></i>
                                            <span class="op-metric-label">{{ $metric['label'] }}</span>
                                            <span class="op-metric-val">{{ $metric['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="op-dept-full-card">
                                <div class="op-health-card" style="border:none; padding:0 0 0.75rem 0;">
                                    <div class="op-donut">
                                        <svg viewBox="0 0 36 36" class="op-donut-svg">
                                            <path class="op-donut-track"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                            <path class="op-donut-fill {{ $operationalEfficiency['fulfillment']['class'] }}"
                                                stroke-dasharray="{{ $operationalEfficiency['fulfillment']['percent'] }}, 100"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        </svg>
                                        <span
                                            class="op-donut-text">{{ $operationalEfficiency['fulfillment']['percent'] }}%</span>
                                    </div>
                                    <div class="op-health-info">
                                        <h4>Order Fulfillment Health</h4>
                                        <div class="op-health-badge {{ $operationalEfficiency['fulfillment']['class'] }}">
                                            {{ $operationalEfficiency['fulfillment']['health'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="op-metrics-mini">
                                    @foreach($operationalEfficiency['fulfillment']['metrics'] as $metric)
                                        <div class="op-metric-item">
                                            <i data-lucide="{{ $metric['icon'] }}" class="op-metric-icon"></i>
                                            <span class="op-metric-label">{{ $metric['label'] }}</span>
                                            <span class="op-metric-val">{{ $metric['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert Feed – separate card --}}
                    <div class="ui-card" style="display:flex; flex-direction:column; gap:0.75rem;">
                        <div class="op-risks-header">
                            <div class="op-risks-title-row">
                                <i data-lucide="alert-triangle" class="op-risks-icon"></i>
                                <h4>Alert Feed</h4>
                                <span class="op-health-badge" id="opRisksTotal"
                                    style="background: var(--slate-200); color: var(--deep-navy);">—</span>
                            </div>
                            <a href="{{ route('bi.live-monitor') }}" class="view-ai-btn">See All</a>
                        </div>
                        <div class="op-risks-counts" id="opRisksCounts">
                            <div class="op-risk-count op-risk-count-critical">
                                <span class="op-risk-count-num">—</span>
                                <span class="op-risk-count-label">Critical</span>
                            </div>
                            <div class="op-risk-count op-risk-count-warning">
                                <span class="op-risk-count-num">—</span>
                                <span class="op-risk-count-label">Warning</span>
                            </div>
                            <div class="op-risk-count op-risk-count-info">
                                <span class="op-risk-count-num">—</span>
                                <span class="op-risk-count-label">Info</span>
                            </div>
                        </div>
                        <span class="op-severity-label">Recent</span>
                        <div class="op-risks-mini-grid" id="opRisksMini"></div>
                        <div class="op-severity-section">
                            <div class="op-severity-bar" id="opRisksBar">
                                <div class="op-severity-seg health-red" style="width:33%;"></div>
                                <div class="op-severity-seg health-orange" style="width:33%;"></div>
                                <div class="op-severity-seg health-blue" style="width:34%;"></div>
                            </div>
                            <div class="op-severity-legend" id="opRisksLegend">
                                <span><span class="op-legend-dot health-red"></span>Critical —%</span>
                                <span><span class="op-legend-dot health-orange"></span>Warning —%</span>
                                <span><span class="op-legend-dot health-blue"></span>Info —%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const clientScope = @json(request()->integer('client_id') ?: null);
        const liveFeedUrl = @json(route('bi.live-feed'));
        const salesForecastUrl = @json(route('bi.sales-forecast'));
        const scopedUrl = (url) => url + (clientScope ? (url.includes('?') ? '&' : '?') + 'client_id=' + clientScope : '');

        let salesTrendChart;

        const verticalLinePlugin = {
            id: 'verticalLine',
            afterDraw(chart) {
                if (chart.tooltip?._active?.length) {
                    const activePoint = chart.tooltip._active[0];
                    const ctx = chart.ctx;
                    const x = activePoint.element.x;
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x, chart.scales.y.top);
                    ctx.lineTo(x, chart.scales.y.bottom);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = '#1B6FC8';
                    ctx.setLineDash([4, 4]);
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        function timeAgo(timestamp) {
            const seconds = Math.floor((Date.now() - new Date(timestamp).getTime()) / 1000);
            if (seconds < 10) return 'Just now';
            if (seconds < 60) return seconds + 's ago';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + 'm ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + 'h ago';
            return Math.floor(hours / 24) + 'd ago';
        }

        // Forces a fresh re-query of every department (busts the 60s cache)
        // by reloading with ?fresh=1; the client scope is preserved.
        function syncAllDepartments() {
            const icon = document.querySelector('#syncNowBtn .control-icon');
            icon?.classList.add('spin-icon');
            const url = new URL(window.location.href);
            url.searchParams.set('fresh', '1');
            window.location.href = url.toString();
        }

        async function fetchOpRisks() {
            try {
                const res = await fetch(scopedUrl(liveFeedUrl));
                const data = await res.json();
                updateOpRisks(data);
            } catch (e) { }
        }

        function updateOpRisks(data) {
            const summary = data.summary || { critical: 0, warning: 0, info: 0 };
            const total = summary.critical + summary.warning + summary.info;
            document.getElementById('opRisksTotal').textContent = total + ' Active';
            const counts = document.querySelectorAll('#opRisksCounts .op-risk-count-num');
            if (counts[0]) counts[0].textContent = summary.critical;
            if (counts[1]) counts[1].textContent = summary.warning;
            if (counts[2]) counts[2].textContent = summary.info;
            const totalSev = total > 0 ? total : 1;
            const pct = (n) => Math.round((n / totalSev) * 100);
            document.getElementById('opRisksBar').innerHTML = `
                <div class="op-severity-seg health-red" style="width:${pct(summary.critical)}%;"></div>
                <div class="op-severity-seg health-orange" style="width:${pct(summary.warning)}%;"></div>
                <div class="op-severity-seg health-blue" style="width:${pct(summary.info)}%;"></div>`;
            document.getElementById('opRisksLegend').innerHTML = `
                <span><span class="op-legend-dot health-red"></span>Critical ${pct(summary.critical)}%</span>
                <span><span class="op-legend-dot health-orange"></span>Warning ${pct(summary.warning)}%</span>
                <span><span class="op-legend-dot health-blue"></span>Info ${pct(summary.info)}%</span>`;
            const miniGrid = document.getElementById('opRisksMini');
            const alerts = data.alerts || [];
            if (alerts.length > 0) {
                miniGrid.innerHTML = alerts.slice(0, 3).map(a => `
                    <div class="op-risk-mini-card op-risk-mini-${a.severity}">
                        <div class="op-risk-mini-header">
                            <span class="op-risk-mini-category">${a.department}</span>
                            <span class="op-risk-mini-days" data-timestamp="${a.timestamp}">${timeAgo(a.timestamp)}</span>
                        </div>
                        <p class="op-risk-mini-issue">${a.title}</p>
                    </div>`).join('');
            } else {
                miniGrid.innerHTML = '<p style="color: var(--slate-500); font-size: 11px;">No active alerts for this client.</p>';
            }
        }

        function initSalesChart() {
            const ctx = document.getElementById('salesTrendChart');
            if (!ctx) return;
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const gridColor = isDark ? '#64748B' : '#E2E8F0';
            const tickColor = isDark ? '#94A3B8' : '#5B7A9D';
            salesTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Sales',
                        data: [0, 0, 0, 0, 0, 0, 0],
                        borderColor: '#1B6FC8',
                        backgroundColor: 'rgba(27,111,200,0.15)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#1B6FC8',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#243447' : '#fff',
                            titleColor: isDark ? '#E2E8F0' : '#0B1E3D',
                            bodyColor: isDark ? '#E2E8F0' : '#0B1E3D',
                            borderColor: gridColor,
                            borderWidth: 1,
                            cornerRadius: 6,
                            displayColors: false,
                            callbacks: { label: (c) => '₱' + Number(c.parsed.y).toLocaleString() }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            border: { display: true, color: gridColor },
                            ticks: { precision: 0, color: tickColor },
                            grace: '10%'
                        },
                        x: {
                            grid: { display: false },
                            border: { display: true, color: gridColor },
                            ticks: { color: tickColor }
                        }
                    }
                },
                plugins: [verticalLinePlugin]
            });
            changeSalesRange();
        }

        async function changeSalesRange() {
            const range = document.getElementById('salesRange')?.value || '7d';
            try {
                const res = await fetch(scopedUrl(salesForecastUrl + '?range=' + range));
                const data = await res.json();
                if (salesTrendChart) {
                    salesTrendChart.data.labels = data.labels;
                    salesTrendChart.data.datasets[0].data = data.sales;
                    salesTrendChart.update();
                }
            } catch (e) { }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSalesChart();
            fetchOpRisks();
            setInterval(fetchOpRisks, 30000);
            setInterval(() => {
                document.querySelectorAll('.op-risk-mini-days').forEach(el => {
                    const ts = el.getAttribute('data-timestamp');
                    if (ts) el.textContent = timeAgo(ts);
                });
            }, 10000);
        });
    </script>
@endsection
