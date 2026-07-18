document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('select-country')?.addEventListener('change', function () {
        const id = this.value;
        if (!id) {
            document.getElementById('country-detail-card').classList.add('d-none');
            document.getElementById('country-empty-state').classList.remove('d-none');
            return;
        }

        fetch(`/api/economic/${id}`, { headers: { Accept: 'application/json' } })
            .then(res => res.json())
            .then(data => {
                document.getElementById('country-empty-state').classList.add('d-none');
                document.getElementById('country-detail-card').classList.remove('d-none');

                document.getElementById('detail-flag').src = data.country.flag_url || '';
                document.getElementById('detail-name').textContent = data.country.name;
                document.getElementById('detail-region').textContent = data.country.region || '-';
                document.getElementById('detail-currency').textContent = data.country.currency_code || '-';

                const ind = data.indicator;

                if (ind && ind.gdp_usd) {
                    const gdpTrillion = (Number(ind.gdp_usd) / 1e12).toFixed(2);
                    document.getElementById('detail-gdp').textContent = `$${gdpTrillion}T`;
                } else {
                    document.getElementById('detail-gdp').textContent = '-';
                }

                if (ind && ind.inflation_rate !== null && ind.inflation_rate !== undefined) {
                    document.getElementById('detail-inflation').textContent = `${Number(ind.inflation_rate).toFixed(2)}%`;
                } else {
                    document.getElementById('detail-inflation').textContent = '-';
                }

                if (ind && ind.population) {
                    document.getElementById('detail-population').textContent = Number(ind.population).toLocaleString('id-ID');
                } else {
                    document.getElementById('detail-population').textContent = '-';
                }

                document.getElementById('detail-year').textContent = (ind && ind.year) ? ind.year : '-';
            })
            .catch(err => console.error('Gagal memuat data ekonomi:', err));
    });
});