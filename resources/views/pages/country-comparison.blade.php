@extends('layouts.app')

@section('title', 'Country Comparison')

@section('content')
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Pilih Negara</h6>
                    <select class="form-select" id="select-country">
                        <option value="">-- Pilih negara --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->cca2 }})</option>
                        @endforeach
                    </select>
                    <div class="form-text mt-2">
                        Hanya menampilkan negara yang memiliki data indikator ekonomi (World Bank).
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div id="country-detail-card" class="card shadow-sm border-0 d-none">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img id="detail-flag" src="" alt="" width="56" class="rounded shadow-sm">
                        <div>
                            <h4 class="mb-0" id="detail-name"></h4>
                            <div class="text-muted small" id="detail-region"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">GDP</div>
                                <div class="stat-value" id="detail-gdp">-</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">Inflasi</div>
                                <div class="stat-value" id="detail-inflation">-</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">Populasi</div>
                                <div class="stat-value" id="detail-population">-</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label">Mata Uang</div>
                                <div class="stat-value" id="detail-currency">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted small mt-3">Data tahun: <span id="detail-year">-</span></div>
                </div>
            </div>

            <div id="country-empty-state" class="card shadow-sm border-0">
                <div class="card-body text-center text-muted py-5">
                    <i class="fa-solid fa-earth-asia fa-2x mb-2"></i>
                    <div>Pilih negara di sebelah kiri untuk melihat detail ekonominya.</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/country-comparison.js') }}"></script>
@endpush