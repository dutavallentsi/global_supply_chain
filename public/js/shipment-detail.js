// shipment-detail.js

document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chart-risk-history');

    if (!ctx || typeof riskHistoryData === 'undefined' || riskHistoryData.length === 0) return;

    const labels = riskHistoryData.map(r => new Date(r.calculated_at).toLocaleString('id-ID'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Cuaca', data: riskHistoryData.map(r => r.weather_risk), borderColor: '#06b6d4', tension: 0.35, fill: false },
                { label: 'Kemacetan Pelabuhan', data: riskHistoryData.map(r => r.port_congestion_risk), borderColor: '#8b5cf6', tension: 0.35, fill: false },
                { label: 'Geopolitik', data: riskHistoryData.map(r => r.geopolitical_risk), borderColor: '#f59e0b', tension: 0.35, fill: false },
                { label: 'Kurs', data: riskHistoryData.map(r => r.currency_risk), borderColor: '#3b82f6', tension: 0.35, fill: false },
                { label: 'Inflasi', data: riskHistoryData.map(r => r.inflation_risk), borderColor: '#ef4444', tension: 0.35, fill: false },
                { label: 'Total', data: riskHistoryData.map(r => r.total_risk_score), borderColor: '#ffffff', borderWidth: 3, tension: 0.35, fill: false },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        font: { family: 'Plus Jakarta Sans', weight: '600' }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.04)' },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans' } }
                },
                y: {
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(255, 255, 255, 0.04)' },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans' } }
                }
            },
        },
    });
});