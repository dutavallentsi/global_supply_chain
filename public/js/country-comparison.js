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

                if (data.indicator) {
                    const gdpTrillion = (data.indicator.gdp_usd / 1e12).toFixed(2);
                    document.getElementById('detail-gdp').textContent = `$${gdpTrillion}T`;
                    document.getElementById('detail-inflation').textContent = `${data.indicator.inflation_rate?.toFixed(2) ?? '-'}%`;
                    document.getElementById('detail-population').textContent = Number(data.indicator.population).toLocaleString('id-ID');
                    document.getElementById('detail-year').textContent = data.indicator.year;
                } else {
                    document.getElementById('detail-gdp').textContent = '-';
                    document.getElementById('detail-inflation').textContent = '-';
                    document.getElementById('detail-population').textContent = '-';
                    document.getElementById('detail-year').textContent = '-';
                }
            })
            .catch(err => console.error('Gagal memuat data ekonomi:', err));
    });
});