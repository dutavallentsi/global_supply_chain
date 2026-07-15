document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('btn-search-news')?.addEventListener('click', function () {
        const countryId = document.getElementById('select-news-country').value;
        const category = document.getElementById('select-news-category').value;
        const resultsBox = document.getElementById('news-results');

        if (!countryId) {
            alert('Pilih negara terlebih dahulu.');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mencari...';
        resultsBox.innerHTML = '';

        fetch(`/api/news?country_id=${countryId}&category=${category}`, { headers: { Accept: 'application/json' } })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Cari Berita';

                if (!data.articles || data.articles.length === 0) {
                    resultsBox.innerHTML = `
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center text-muted py-5">
                                <i class="fa-solid fa-newspaper fa-2x mb-2"></i>
                                <div>Tidak ada berita ditemukan untuk ${data.country} (kategori: ${data.category}).</div>
                            </div>
                        </div>`;
                    return;
                }

                let html = '<div class="list-group shadow-sm">';
                data.articles.forEach(article => {
                    const date = new Date(article.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    html += `
                        <a href="${article.url}" target="_blank" rel="noopener" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">${article.title}</h6>
                                <small class="text-muted">${date}</small>
                            </div>
                            <small class="text-muted">${article.source ?? 'Sumber tidak diketahui'}</small>
                        </a>`;
                });
                html += '</div>';
                resultsBox.innerHTML = html;
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Cari Berita';
                resultsBox.innerHTML = `<div class="alert alert-danger">Gagal mencari berita: ${err.message}</div>`;
            });
    });
});