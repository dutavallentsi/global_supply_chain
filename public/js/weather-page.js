document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('select-port')?.addEventListener('change', function () {
        const id = this.value;
        if (!id) {
            document.getElementById('weather-card').classList.add('d-none');
            document.getElementById('weather-empty-state').classList.remove('d-none');
            return;
        }

        fetch(`/api/weather/${id}`, { headers: { Accept: 'application/json' } })
            .then(res => {
                if (!res.ok) throw new Error('Gagal mengambil data cuaca.');
                return res.json();
            })
            .then(data => {
                document.getElementById('weather-empty-state').classList.add('d-none');
                document.getElementById('weather-card').classList.remove('d-none');

                document.getElementById('weather-port-name').textContent = data.port.name;
                document.getElementById('weather-port-country').textContent = data.port.country;
                document.getElementById('weather-temp').textContent = `${data.weather.temperature_c ?? '-'}°C`;
                document.getElementById('weather-precip').textContent = `${data.weather.precipitation_mm ?? '-'} mm`;
                document.getElementById('weather-wind').textContent = `${data.weather.wind_speed_kmh ?? '-'} km/h`;
                document.getElementById('weather-risk').textContent = (data.weather.storm_risk_level ?? '-').toUpperCase();
            })
            .catch(err => {
                alert(err.message);
                console.error(err);
            });
    });
});