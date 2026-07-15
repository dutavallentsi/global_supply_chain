document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('map-full');
    if (!mapEl) return;

    const map = L.map('map-full').setView([10, 110], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    const riskColor = { low: '#198754', medium: '#0dcaf0', high: '#ffc107', critical: '#dc3545', unknown: '#6c757d' };

    fetch('/api/map/ports', { headers: { Accept: 'application/json' } })
        .then(res => res.json())
        .then(ports => {
            ports.forEach(port => {
                const color = riskColor[port.storm_risk_level] || riskColor.unknown;
                L.circleMarker([port.lat, port.lng], { radius: 7, color, fillColor: color, fillOpacity: 0.85 })
                    .addTo(map)
                    .bindPopup(`<b>${port.name}</b><br>${port.country}<br>Risiko cuaca: ${port.storm_risk_level}`);
            });
        })
        .catch(err => console.error('Gagal memuat pelabuhan:', err));
});