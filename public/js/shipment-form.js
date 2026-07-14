// shipment-form.js — logika form "Tambah Pengiriman" di modal dashboard

let allCountries = [];
let allPorts = [];

document.addEventListener('DOMContentLoaded', function () {
    loadReferenceData();
    setDefaultDates();
    suggestShipmentCode();

    document.getElementById('select-origin-country')?.addEventListener('change', function () {
        populatePortSelect('select-origin-port', this.value);
    });

    document.getElementById('select-destination-country')?.addEventListener('change', function () {
        populatePortSelect('select-destination-port', this.value);
    });

    document.getElementById('form-tambah-shipment')?.addEventListener('submit', handleSubmitShipment);
});

/* ---------------- LOAD DATA REFERENSI ---------------- */
function loadReferenceData() {
    Promise.all([
        scmFetch('/api/reference/countries'),
        scmFetch('/api/reference/ports'),
    ]).then(([countries, ports]) => {
        allCountries = countries;
        allPorts = ports;

        const originSelect = document.getElementById('select-origin-country');
        const destSelect = document.getElementById('select-destination-country');

        countries.forEach(country => {
            const option1 = new Option(`${country.name} (${country.cca2})`, country.id);
            const option2 = new Option(`${country.name} (${country.cca2})`, country.id);
            originSelect.add(option1);
            destSelect.add(option2);
        });
    }).catch(err => {
        console.error('Gagal memuat data referensi:', err);
        showFormAlert('Gagal memuat daftar negara/pelabuhan. Coba refresh halaman.');
    });
}

function populatePortSelect(selectId, countryId) {
    const select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Tidak ada pelabuhan / lewat darat</option>';

    if (!countryId) return;

    const portsInCountry = allPorts.filter(p => String(p.country_id) === String(countryId));

    if (portsInCountry.length === 0) {
        select.innerHTML = '<option value="">Belum ada pelabuhan untuk negara ini</option>';
        return;
    }

    portsInCountry.forEach(port => {
        select.add(new Option(port.name, port.id));
    });
}

/* ---------------- DEFAULT VALUES ---------------- */
function setDefaultDates() {
    const today = new Date().toISOString().split('T')[0];
    const in14days = new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

    document.getElementById('input-departure').value = today;
    document.getElementById('input-eta').value = in14days;
}

function suggestShipmentCode() {
    const year = new Date().getFullYear();
    const random = String(Math.floor(Math.random() * 9000) + 1000);
    document.getElementById('input-code').value = `SHP-${year}-${random}`;
}

/* ---------------- SUBMIT FORM (AJAX) ---------------- */
function handleSubmitShipment(e) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('btn-submit-shipment');
    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    // Field opsional: kalau kosong, jangan dikirim sebagai string kosong
    if (!payload.origin_port_id) delete payload.origin_port_id;
    if (!payload.destination_port_id) delete payload.destination_port_id;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    hideFormAlert();

    fetch('/api/shipments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    })
        .then(async (response) => {
            const body = await response.json().catch(() => null);

            if (!response.ok) {
                let message = 'Gagal menyimpan pengiriman. Periksa kembali data yang diisi.';
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
            showFormAlert(err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan Pengiriman';
        });
}

function showFormAlert(message) {
    const alertBox = document.getElementById('form-alert');
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');
}

function hideFormAlert() {
    document.getElementById('form-alert').classList.add('d-none');
}