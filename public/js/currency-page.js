let currencyPageChart;

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/reference/currencies', { headers: { Accept: 'application/json' } })
        .then(res => res.json())
        .then(currencies => {
            const select = document.getElementById('select-currency-target');
            select.innerHTML = '';

            currencies.filter(c => c !== 'USD').forEach(code => {
                select.add(new Option(`USD → ${code}`, code));
            });

            if (currencies.includes('IDR')) {
                select.value = 'IDR';
            }

            loadCurrencyChart(select.value);
        })
        .catch(err => console.error('Gagal memuat daftar mata uang:', err));

    document.getElementById('select-currency-target')?.addEventListener('change', function () {
        loadCurrencyChart(this.value);
    });
});

function loadCurrencyChart(target) {
    if (!target) return;

    fetch(`/api/charts/exchange-rate?base=USD&target=${target}&days=30`, { headers: { Accept: 'application/json' } })
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('chart-currency-page');
            if (currencyPageChart) currencyPageChart.destroy();

            currencyPageChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: `${data.base} → ${data.target}`,
                        data: data.values,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        tension: 0.3,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true } },
                    scales: { y: { beginAtZero: false } },
                },
            });
        })
        .catch(err => console.error('Gagal memuat data kurs:', err));
}