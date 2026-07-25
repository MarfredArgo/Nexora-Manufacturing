@extends('bi::layouts.app')

@section('content')
    <div class="tab-content active-tab" style="display:block;">
        <div class="subheader-bar">
            <div class="subheader-title">
                <h3 id="deptTitle">Department Analytics</h3>
                <p id="deptDesc">Deep dive into each department's key performance indicators and trends.</p>
            </div>
            <div class="subheader-controls">
                <select id="deptSelector" class="control-date-selector chart-range-select" onchange="switchDepartment()"
                    style="width:280px;">
                    <option value="finance">Finance &amp; Accounting</option>
                    <option value="inventory">Inventory &amp; Warehouse</option>
                    <option value="procurement">Procurement</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="fulfillment">Order Fulfillment</option>
                    <option value="ecommerce">E-Commerce &amp; CRM</option>
                </select>
            </div>
        </div>
        <div class="content-container">
            {{-- Stats Row --}}
            <div class="kpi-grid" id="deptStats"></div>

            {{-- Charts --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="ui-card">
                    <div class="card-header">
                        <div class="card-title" id="deptCard1Title">Trend Overview</div>
                    </div>
                    <div class="placeholder-graph-box chart-box"><canvas id="deptChart1"></canvas></div>
                </div>
                <div class="ui-card">
                    <div class="card-header">
                        <div class="card-title" id="deptCard2Title">Performance Chart</div>
                    </div>
                    <div class="placeholder-graph-box chart-box"><canvas id="deptChart2"></canvas></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const clientScope = @json(request()->integer('client_id') ?: null);
        const deptApiBase = @json(url('/bi/api/department'));
        const scopedUrl = (url) => url + (clientScope ? (url.includes('?') ? '&' : '?') + 'client_id=' + clientScope : '');

        const deptDescs = {
            finance: 'Revenue, expenses, and invoice status across the finance ledger.',
            inventory: 'Stock levels, valuation, and category breakdown.',
            procurement: 'Purchase orders, supplier activity, and spending.',
            manufacturing: 'Work order status and production throughput.',
            fulfillment: 'Delivery performance, tracking, and shipping status.',
            ecommerce: 'Catalog value, product availability, and storefront activity.',
        };

        const moneyLabel = /revenue|expense|value|overdue|invoiced|profit|amount|sales|paid/i;
        const iconFor = (label) => {
            const l = label.toLowerCase();
            if (moneyLabel.test(l)) return 'dollar-sign';
            if (l.includes('order')) return 'shopping-cart';
            if (l.includes('stock') || l.includes('item') || l.includes('sku')) return 'package';
            if (l.includes('overdue') || l.includes('delay')) return 'clock-alert';
            if (l.includes('work')) return 'hammer';
            if (l.includes('product') || l.includes('catalog')) return 'tags';
            return 'bar-chart-3';
        };
        const fmtStat = (label, value) => {
            if (typeof value !== 'number') return value;
            return (moneyLabel.test(label) ? '₱' : '') + Number(value).toLocaleString();
        };

        function buildStats(stats) {
            if (!Array.isArray(stats) || stats.length === 0) {
                return '<p style="color:var(--slate-500);text-align:center;padding:1rem;grid-column:1/-1;">No stats available for this department.</p>';
            }
            return stats.slice(0, 5).map(s => `
                <div class="kpi-card">
                    <div class="kpi-icon-container"><i data-lucide="${iconFor(s.label)}" class="kpi-icon"></i></div>
                    <div class="kpi-details">
                        <div class="kpi-label">${s.label}</div>
                        <div class="kpi-value">${fmtStat(s.label, s.value)}</div>
                        <div class="kpi-change change-up"></div>
                    </div>
                </div>`).join('');
        }

        let deptChart1 = null;
        let deptChart2 = null;
        let currentDeptData = null;

        function destroyCharts() {
            if (deptChart1) { deptChart1.destroy(); deptChart1 = null; }
            if (deptChart2) { deptChart2.destroy(); deptChart2 = null; }
        }

        function renderChart(canvasId, chartData) {
            if (!chartData || !chartData.data || chartData.data.length === 0) return null;

            const ctx = document.getElementById(canvasId);
            if (!ctx) return null;

            const labels = chartData.data.map(d => {
                const raw = d.label || d.date || '';
                if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                    return new Date(raw + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }
                return raw;
            });
            const values = chartData.data.map(d => d.value ?? d.total ?? 0);
            const colors = ['#1B6FC8', '#4A9EE8', '#7BBEF0', '#16A34A', '#D97706', '#DC2626', '#0EA5E9', '#EAB308'];
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const gridColor = isDark ? '#64748B' : '#E2E8F0';

            if (chartData.type === 'doughnut') {
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 0 }] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } }
                    }
                });
            }

            if (chartData.type === 'bar') {
                return new Chart(ctx, {
                    type: 'bar',
                    data: { labels, datasets: [{ data: values, backgroundColor: colors[0], borderRadius: 4, maxBarThickness: 40 }] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: gridColor }, border: { color: gridColor }, ticks: { precision: 0 }, grace: '10%' },
                            x: { grid: { display: false }, border: { color: gridColor } }
                        }
                    }
                });
            }

            return new Chart(ctx, {
                type: 'line',
                data: { labels, datasets: [{ data: values, borderColor: '#1B6FC8', backgroundColor: 'rgba(27,111,200,0.1)', tension: 0.35, fill: true, pointRadius: 3, pointBackgroundColor: '#1B6FC8' }] },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: gridColor }, border: { color: gridColor }, ticks: { precision: 0 }, grace: '10%' },
                        x: { grid: { display: false }, border: { color: gridColor } }
                    }
                }
            });
        }

        async function switchDepartment() {
            const dept = document.getElementById('deptSelector').value;
            document.getElementById('deptDesc').textContent = deptDescs[dept] || '';
            document.getElementById('deptStats').innerHTML = '<p style="color:var(--slate-500);text-align:center;padding:1rem;grid-column:1/-1;">Loading…</p>';
            destroyCharts();

            try {
                const res = await fetch(scopedUrl(deptApiBase + '/' + dept));
                const data = await res.json();
                currentDeptData = data;

                document.getElementById('deptTitle').textContent = data.title || 'Department Analytics';
                document.getElementById('deptCard1Title').textContent = data.chart1?.label || 'Overview';
                document.getElementById('deptCard2Title').textContent = data.chart2?.label || 'Breakdown';
                document.getElementById('deptStats').innerHTML = buildStats(data.stats);

                deptChart1 = renderChart('deptChart1', data.chart1);
                deptChart2 = renderChart('deptChart2', data.chart2);

                lucide.createIcons();
            } catch (e) {
                document.getElementById('deptStats').innerHTML = '<p style="color:var(--danger);text-align:center;padding:1rem;grid-column:1/-1;">Failed to load data</p>';
            }
        }

        function refreshCharts() {
            if (!currentDeptData) return;
            destroyCharts();
            deptChart1 = renderChart('deptChart1', currentDeptData.chart1);
            deptChart2 = renderChart('deptChart2', currentDeptData.chart2);
        }

        window.addEventListener('themechange', () => refreshCharts());
        document.addEventListener('DOMContentLoaded', () => switchDepartment());
    </script>
@endsection
