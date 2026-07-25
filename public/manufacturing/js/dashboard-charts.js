// dashboard-charts.js
// Renders the weekly builds bar chart on the Dashboard.
function initDashboardChart() {
    const ctx = document.getElementById('dashWeekChart');
    if (!ctx || !window.dashboardData || !window.Chart) return;

    const { days, weekCounts } = window.dashboardData;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                data: weekCounts,
                backgroundColor: '#1B6FC8',
                borderRadius: 4,
                borderSkipped: 'bottom',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => c.parsed.y + ' builds' } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#5B7A9D', font: { size: 11 } }, border: { color: '#1B3A6B' } },
                y: { grid: { display: true, color: '#869FB1' }, ticks: { color: '#5B7A9D', font: { size: 11 }, stepSize: 2 }, border: { display: false }, min: 0 }
            }
        }
    });
}

if (window.Chart) {
    initDashboardChart();
} else {
    window.addEventListener('load', initDashboardChart);
}
