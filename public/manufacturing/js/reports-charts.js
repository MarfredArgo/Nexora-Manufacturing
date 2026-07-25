// reports-charts.js
// Renders the status/weekly/parts charts on the Reports page.
function initReportsCharts() {
    if (!window.reportsData || !window.Chart) return;

    const {
        statusLabels, statusCounts, statusColors,
        weekLabels, weekBuilds, weekDefects,
        partsReady, partsSourcing, partsMissing
    } = window.reportsData;

    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{ data: statusCounts, backgroundColor: statusColors, borderRadius: 4, borderSkipped: 'bottom' }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#5B7A9D', font: { size: 11 } }, border: { color: '#1B3A6B' } },
                    y: { grid: {display: true, color: '#869FB1' }, ticks: { color: '#5B7A9D', font: { size: 11 }, stepSize: 1 }, border: { display: false }, min: 0 }
                }
            }
        });
    }

    const weeklyCtx = document.getElementById('weeklyChart');
    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: weekLabels,
                datasets: [
                    { label: 'Builds done', data: weekBuilds, borderColor: '#1B6FC8', backgroundColor: 'rgba(27,111,200,0.08)', borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#1B6FC8', pointBorderColor: '#F4F6FA', pointBorderWidth: 2, tension: 0.35, fill: true },
                    { label: 'Defects / cancelled', data: weekDefects, borderColor: '#DC2626', backgroundColor: 'rgba(220,38,38,0.06)', borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#DC2626', pointBorderColor: '#F4F6FA', pointBorderWidth: 2, tension: 0.35, fill: true }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#5B7A9D', font: { size: 11 } }, border: { color: '#1B3A6B' } },
                    y: { grid: { display: true, color: '#869FB1' }, ticks: { color: '#5B7A9D', font: { size: 11 }, stepSize: 2 }, border: { display: false }, min: 0 }
                }
            }
        });
    }

    const donutCtx = document.getElementById('partsDonut');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Ready', 'Sourcing', 'Missing'],
                datasets: [{ data: [partsReady, partsSourcing, partsMissing], backgroundColor: ['#16A34A', '#D97706', '#DC2626'], borderColor: '#1B3A6B', borderWidth: 3, hoverOffset: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } }
        });
    }
}

if (window.Chart) {
    initReportsCharts();
} else {
    window.addEventListener('load', initReportsCharts);
}
