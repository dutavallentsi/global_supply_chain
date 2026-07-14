@extends('layouts.app')

@section('title', 'Dashboard - SCM Risk Monitor')

@section('content')

    {{-- Alert Peringatan Risiko Tinggi --}}
    @if ($highRiskShipments->isNotEmpty())
        <div class="alert alert-danger d-flex align-items-start shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation fa-lg me-3 mt-1"></i>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-2">
                    ⚠ {{ $highRiskShipments->count() }} Pengiriman Berisiko Tinggi Butuh Perhatian
                </h6>
                <ul class="mb-0 ps-3">
                    @foreach ($highRiskShipments->take(5) as $hrShipment)
                        <li>
                            <a href="{{ route('dashboard.show', $hrShipment) }}" class="alert-link">
                                {{ $hrShipment->code }}
                            </a>
                            — tujuan {{ $hrShipment->destinationCountry->name }} —
                            skor <strong>{{ $hrShipment->latestRiskScore->total_risk_score }}
                            ({{ ucfirst($hrShipment->latestRiskScore->risk_level) }})</strong>
                        </li>
                    @endforeach
                </ul>
                @if ($highRiskShipments->count() > 5)
                    <div class="small text-muted mt-1">
                        + {{ $highRiskShipments->count() - 5 }} pengiriman berisiko tinggi lainnya
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Ringkasan angka --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small">Total Pengiriman</div>
                    <div class="fs-3 fw-bold">{{ $summary['total_shipments'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small">Dalam Perjalanan</div>
                    <div class="fs-3 fw-bold text-primary">{{ $summary['in_transit'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small">Terlambat</div>
                    <div class="fs-3 fw-bold text-danger">{{ $summary['delayed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small">Sudah Tiba</div>
                    <div class="fs-3 fw-bold text-success">{{ $summary['arrived'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Peta rute & pelabuhan --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <i class="fa-solid fa-map-location-dot"></i> Peta Rute & Kondisi Pelabuhan
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 420px;"></div>
                </div>
            </div>
        </div>

        {{-- Grafik kurs --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-money-bill-trend-up"></i> Tren Kurs Mata Uang</span>
                    <select id="select-currency-pair" class="form-select form-select-sm w-auto">
                        <option value="USD-IDR">USD → IDR</option>
                        <option value="USD-CNY">USD → CNY</option>
                        <option value="USD-EUR">USD → EUR</option>
                        <option value="USD-JPY">USD → JPY</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="chart-exchange-rate" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel shipment --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-boxes-packing"></i> Daftar Pengiriman & Skor Risiko</span>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahShipment">
                <i class="fa-solid fa-plus"></i> Tambah Pengiriman
            </button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Produk</th>
                        <th>Asal → Tujuan</th>
                        <th>ETA</th>
                        <th>Status</th>
                        <th>Skor Risiko</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shipments as $shipment)
                        @php $risk = $shipment->latestRiskScore; @endphp
                        <tr>
                            <td><a href="{{ route('dashboard.show', $shipment) }}">{{ $shipment->code }}</a></td>
                            <td>{{ $shipment->product_name }}</td>
                            <td>{{ $shipment->originCountry->name }} → {{ $shipment->destinationCountry->name }}</td>
                            <td>{{ $shipment->estimated_arrival_date->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ match($shipment->status) {
                                    'in_transit' => 'primary',
                                    'delayed' => 'danger',
                                    'arrived' => 'success',
                                    'cancelled' => 'secondary',
                                    default => 'warning',
                                } }}">
                                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                </span>
                            </td>
                            <td>
                                @if ($risk)
                                    <span class="badge bg-{{ match($risk->risk_level) {
                                        'critical' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        default => 'success',
                                    } }}">
                                        {{ $risk->total_risk_score }} - {{ ucfirst($risk->risk_level) }}
                                    </span>
                                @else
                                    <span class="text-muted small">Belum dihitung</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary btn-recalculate" data-id="{{ $shipment->id }}" title="Hitung ulang risiko">
                                        <i class="fa-solid fa-rotate"></i>
                                    </button>
                                    <button class="btn btn-outline-primary btn-edit-status"
                                            data-id="{{ $shipment->id }}"
                                            data-code="{{ $shipment->code }}"
                                            data-status="{{ $shipment->status }}"
                                            data-actual-arrival="{{ $shipment->actual_arrival_date?->format('Y-m-d') }}"
                                            data-notes="{{ $shipment->notes }}"
                                            title="Edit status">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-delete-shipment"
                                            data-id="{{ $shipment->id }}"
                                            data-code="{{ $shipment->code }}"
                                            title="Hapus pengiriman">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pengiriman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $shipments->links() }}
        </div>
    </div>

    {{-- Modal Tambah Pengiriman --}}
    <div class="modal fade" id="modalTambahShipment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form-tambah-shipment">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-box"></i> Tambah Pengiriman Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="form-alert" class="alert alert-danger d-none"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Pengiriman</label>
                                <input type="text" class="form-control" name="code" id="input-code" required
                                       placeholder="SHP-2026-0001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="product_name" required
                                       placeholder="Komponen Elektronik">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Negara Asal</label>
                                <select class="form-select" name="origin_country_id" id="select-origin-country" required>
                                    <option value="">Pilih negara...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Negara Tujuan</label>
                                <select class="form-select" name="destination_country_id" id="select-destination-country" required>
                                    <option value="">Pilih negara...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pelabuhan Asal</label>
                                <select class="form-select" name="origin_port_id" id="select-origin-port">
                                    <option value="">Pilih negara asal dahulu...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pelabuhan Tujuan</label>
                                <select class="form-select" name="destination_port_id" id="select-destination-port">
                                    <option value="">Pilih negara tujuan dahulu...</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jumlah (unit)</label>
                                <input type="number" class="form-control" name="quantity" value="1" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mata Uang Transaksi</label>
                                <select class="form-select" name="transaction_currency" required>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="CNY">CNY</option>
                                    <option value="JPY">JPY</option>
                                    <option value="IDR">IDR</option>
                                    <option value="SGD">SGD</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nilai Barang</label>
                                <input type="number" class="form-control" name="amount" step="0.01" min="0" required
                                       placeholder="25000">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Berangkat</label>
                                <input type="date" class="form-control" name="departure_date" id="input-departure" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estimasi Tiba (ETA)</label>
                                <input type="date" class="form-control" name="estimated_arrival_date" id="input-eta" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-shipment">
                            <i class="fa-solid fa-save"></i> Simpan Pengiriman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Status Pengiriman --}}
    <div class="modal fade" id="modalEditStatus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-edit-status">
                    <input type="hidden" id="edit-shipment-id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-pen"></i> Update Status — <span id="edit-shipment-code"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="edit-form-alert" class="alert alert-danger d-none"></div>

                        <div class="mb-3">
                            <label class="form-label">Status Pengiriman</label>
                            <select class="form-select" name="status" id="edit-select-status" required>
                                <option value="pending">Pending</option>
                                <option value="in_transit">Dalam Perjalanan</option>
                                <option value="delayed">Terlambat</option>
                                <option value="arrived">Sudah Tiba</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>

                        <div class="mb-3" id="edit-actual-arrival-wrapper" style="display:none;">
                            <label class="form-label">Tanggal Tiba Aktual</label>
                            <input type="date" class="form-control" name="actual_arrival_date" id="edit-input-actual-arrival">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea class="form-control" name="notes" id="edit-textarea-notes" rows="3"
                                      placeholder="Contoh: tertahan di bea cukai, cuaca buruk, dsb."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-edit-status">
                            <i class="fa-solid fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/shipment-form.js') }}"></script>
    <script src="{{ asset('js/shipment-status.js') }}"></script>
@endpush