// analytics-charts.js
// Renders the QC verdict donut chart on the Analytics page.
function initAnalyticsDonut() {
    const ctx = document.getElementById('qcVerdictDonut');
    if (!ctx || !window.qcAnalyticsData || !window.Chart) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: window.qcAnalyticsData.verdictLabels,
            datasets: [{
                data: window.qcAnalyticsData.verdictCounts,
                backgroundColor: window.qcAnalyticsData.verdictColors,
                borderColor: '#132B52',
                borderWidth: 3,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: { legend: { display: false } }
        }
    });
}

if (window.Chart) {
    initAnalyticsDonut();
} else {
    window.addEventListener('load', initAnalyticsDonut);
}
