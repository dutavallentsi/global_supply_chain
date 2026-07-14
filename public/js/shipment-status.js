// shipment-status.js — logika edit status & hapus pengiriman

document.addEventListener('DOMContentLoaded', function () {
    bindEditStatusButtons();
    bindDeleteButtons();

    document.getElementById('edit-select-status')?.addEventListener('change', function () {
        toggleActualArrivalField(this.value);
    });

    document.getElementById('form-edit-status')?.addEventListener('submit', handleSubmitEditStatus);
});

/* ---------------- BUKA MODAL EDIT (isi data dari tombol yang diklik) ---------------- */
function bindEditStatusButtons() {
    document.querySelectorAll('.btn-edit-status').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-shipment-id').value = this.dataset.id;
            document.getElementById('edit-shipment-code').textContent = this.dataset.code;
            document.getElementById('edit-select-status').value = this.dataset.status;
            document.getElementById('edit-input-actual-arrival').value = this.dataset.actualArrival || '';
            document.getElementById('edit-textarea-notes').value = this.dataset.notes || '';

            toggleActualArrivalField(this.dataset.status);
            hideEditFormAlert();

            new bootstrap.Modal(document.getElementById('modalEditStatus')).show();
        });
    });
}

function toggleActualArrivalField(status) {
    const wrapper = document.getElementById('edit-actual-arrival-wrapper');
    wrapper.style.display = (status === 'arrived') ? 'block' : 'none';
}

/* ---------------- SUBMIT EDIT STATUS (AJAX PATCH) ---------------- */
function handleSubmitEditStatus(e) {
    e.preventDefault();

    const id = document.getElementById('edit-shipment-id').value;
    const btn = document.getElementById('btn-submit-edit-status');

    const payload = {
        status: document.getElementById('edit-select-status').value,
        notes: document.getElementById('edit-textarea-notes').value,
    };

    const actualArrival = document.getElementById('edit-input-actual-arrival').value;
    if (payload.status === 'arrived' && actualArrival) {
        payload.actual_arrival_date = actualArrival;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    hideEditFormAlert();

    fetch(`/api/shipments/${id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    })
        .then(async (response) => {
            const body = await response.json().catch(() => null);

            if (!response.ok) {
                let message = 'Gagal menyimpan perubahan.';
                if (body?.errors) {
                    message = Object.values(body.errors).flat().join(' | ');
                } else if (body?.message) {
                    message = body.message;
                }
                throw new Error(message);
            }

            window.location.reload();
        })
        .catch((err) => {
            showEditFormAlert(err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan Perubahan';
        });
}

function showEditFormAlert(message) {
    const box = document.getElementById('edit-form-alert');
    box.textContent = message;
    box.classList.remove('d-none');
}

function hideEditFormAlert() {
    document.getElementById('edit-form-alert')?.classList.add('d-none');
}

/* ---------------- HAPUS PENGIRIMAN (AJAX DELETE) ---------------- */
function bindDeleteButtons() {
    document.querySelectorAll('.btn-delete-shipment').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const code = this.dataset.code;

            if (!confirm(`Yakin ingin menghapus pengiriman "${code}"? Tindakan ini tidak bisa dibatalkan.`)) {
                return;
            }

            this.disabled = true;

            fetch(`/api/shipments/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' },
            })
                .then((response) => {
                    if (!response.ok && response.status !== 204) {
                        throw new Error('Gagal menghapus pengiriman.');
                    }
                    window.location.reload();
                })
                .catch((err) => {
                    alert(err.message);
                    this.disabled = false;
                });
        });
    });
}