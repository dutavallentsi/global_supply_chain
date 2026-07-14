// dashboard.js — logika khusus halaman dashboard index

let map, exchangeRateChart;
const riskColor = { low: '#198754', medium: '#0dcaf0', high: '#ffc107', critical: '#dc3545', unknown: '#6c757d' };

document.addEventListener('DOMContentLoaded', function () {
    initMap();
    initExchangeRateChart();
    bindRecalculateButtons();

    document.getElementById('select-currency-pair')?.addEventListener('change', function () {
        const [base, target] = this.value.split('-');
        loadExchangeRateChart(base, target);
    });
});

/* ---------------- MAP (Leaflet.js) ---------------- */
function initMap() {
    map = L.map('map').setView([10, 110], 3);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    loadPorts();
    loadShipmentRoutes();
}

function loadPorts() {
    scmFetch('/api/map/ports').then(ports => {
        ports.forEach(port => {
            const color = riskColor[port.storm_risk_level] || riskColor.unknown;
            L.circleMarker([port.lat, port.lng], {
                radius: 6, color, fillColor: color, fillOpacity: 0.8,
            })
            .addTo(map)
            .bindPopup(`<b>${port.name}</b><br>${port.country}<br>Risiko cuaca: ${port.storm_risk_level}`);
        });
    }).catch(console.error);
}

function loadShipmentRoutes() {
    scmFetch('/api/map/shipments').then(shipments => {
        shipments.forEach(s => {
            if (!s.origin || !s.destination) return;

            const color = riskColor[s.risk_level] || riskColor.unknown;
            const points = [[s.origin.lat, s.origin.lng]];
            if (s.current) points.push([s.current.lat, s.current.lng]);
            points.push([s.destination.lat, s.destination.lng]);

            L.polyline(points, { color, weight: 2, dashArray: '6 4' })
                .addTo(map)
                .bindPopup(`<b>${s.code}</b><br>Status: ${s.status}<br>Risiko: ${s.risk_level}`);
        });
    }).catch(console.error);
}

/* ---------------- CHART (Chart.js) ---------------- */
function initExchangeRateChart() {
    loadExchangeRateChart('USD', 'IDR');
}

function loadExchangeRateChart(base, target) {
    scmFetch(`/api/charts/exchange-rate?base=${base}&target=${target}&days=30`).then(data => {
        const ctx = document.getElementById('chart-exchange-rate');

        if (exchangeRateChart) exchangeRateChart.destroy();

        exchangeRateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: `${data.base} → ${data.target}`,
                    data: data.values,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: false } },
            },
        });
    }).catch(console.error);
}

/* ---------------- RECALCULATE RISK (AJAX) ---------------- */
function bindRecalculateButtons() {
    document.querySelectorAll('.btn-recalculate').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            this.disabled = true;
            this.querySelector('i').classList.add('fa-spin');

            scmFetch(`/api/shipments/${id}/recalculate-risk`, { method: 'POST' })
                .then(() => window.location.reload())
                .catch(err => {
                    alert('Gagal menghitung ulang risiko: ' + err.message);
                    this.disabled = false;
                    this.querySelector('i').classList.remove('fa-spin');
                });
        });
    });
}