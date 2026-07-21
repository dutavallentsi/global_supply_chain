document.addEventListener('DOMContentLoaded', function () {
    const selectPort    = document.getElementById('select-port');
    const weatherCard   = document.getElementById('weather-card');
    const emptyState    = document.getElementById('weather-empty-state');
    const errorState    = document.getElementById('weather-error-state');

    selectPort?.addEventListener('change', function () {
        const id = this.value;
        if (!id) {
            weatherCard.classList.add('d-none');
            emptyState.classList.remove('d-none');
            if (errorState) errorState.classList.add('d-none');
            return;
        }

        // Tampilkan loading
        emptyState.classList.add('d-none');
        weatherCard.classList.add('d-none');
        if (errorState) errorState.classList.add('d-none');

        // Tampilkan teks loading sementara
        const loadingEl = document.getElementById('weather-loading');
        if (loadingEl) loadingEl.classList.remove('d-none');

        fetch(`/api/weather/${id}`, { headers: { Accept: 'application/json' } })
            .then(res => {
                if (loadingEl) loadingEl.classList.add('d-none');
                if (!res.ok) {
                    return res.json().then(err => {
                        // Tampilkan error state
                        if (errorState) {
                            const msgEl = document.getElementById('weather-error-msg');
                            if (msgEl) {
                                msgEl.textContent = err.message || 'Gagal mengambil data cuaca.';
                            }
                            errorState.classList.remove('d-none');
                        } else {
                            emptyState.classList.remove('d-none');
                            console.error('Weather error:', err.message);
                        }
                        throw new Error(err.message);
                    });
                }
                return res.json();
            })
            .then(data => {
                if (!data || !data.weather) return;

                emptyState.classList.add('d-none');
                weatherCard.classList.remove('d-none');

                document.getElementById('weather-port-name').textContent    = data.port.name;
                document.getElementById('weather-port-country').textContent = data.port.country;

                const flagEl = document.getElementById('weather-flag');
                if (data.port.flag_url) {
                    flagEl.src = data.port.flag_url;
                    flagEl.classList.remove('d-none');
                } else {
                    flagEl.classList.add('d-none');
                }

                document.getElementById('weather-temp').textContent    = `${data.weather.temperature_c ?? '-'}°C`;
                document.getElementById('weather-precip').textContent  = `${data.weather.precipitation_mm ?? '-'} mm`;
                document.getElementById('weather-wind').textContent    = `${data.weather.wind_speed_kmh ?? '-'} km/h`;
                document.getElementById('weather-risk').textContent    = (data.weather.storm_risk_level ?? '-').toUpperCase();
            })
            .catch(err => {
                if (loadingEl) loadingEl.classList.add('d-none');
                console.error('Weather fetch error:', err);
            });
    });
});