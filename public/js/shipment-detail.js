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
                { label: 'Cuaca', data: riskHistoryData.map(r => r.weather_risk), borderColor: '#0dcaf0', tension: 0.3 },
                { label: 'Kemacetan Pelabuhan', data: riskHistoryData.map(r => r.port_congestion_risk), borderColor: '#6f42c1', tension: 0.3 },
                { label: 'Geopolitik', data: riskHistoryData.map(r => r.geopolitical_risk), borderColor: '#fd7e14', tension: 0.3 },
                { label: 'Kurs', data: riskHistoryData.map(r => r.currency_risk), borderColor: '#198754', tension: 0.3 },
                { label: 'Inflasi', data: riskHistoryData.map(r => r.inflation_risk), borderColor: '#dc3545', tension: 0.3 },
                { label: 'Total', data: riskHistoryData.map(r => r.total_risk_score), borderColor: '#000000', borderWidth: 2, tension: 0.3 },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { min: 0, max: 100 } },
        },
    });
});