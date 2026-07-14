// app.js — dipakai di semua halaman (layout utama)

document.addEventListener('DOMContentLoaded', function () {
    const label = document.getElementById('last-updated-label');
    if (label) {
        label.textContent = 'Diperbarui: ' + new Date().toLocaleTimeString('id-ID');
    }

    const btnRefreshAll = document.getElementById('btn-refresh-all');
    if (btnRefreshAll) {
        btnRefreshAll.addEventListener('click', function () {
            window.location.reload();
        });
    }
});

// Helper AJAX sederhana (bisa diganti axios/fetch native)
function scmFetch(url, options = {}) {
    return fetch(url, {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        ...options,
    }).then(res => {
        if (!res.ok) throw new Error('Request failed: ' + res.status);
        return res.json();
    });
}