@extends('layouts.app')

@section('title', 'Weather')

@section('content')
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Pilih Pelabuhan</h6>
                    <select class="form-select" id="select-port">
                        <option value="">-- Pilih pelabuhan --</option>
                        @foreach ($ports as $port)
                            <option value="{{ $port->id }}">{{ $port->name }} — {{ $port->country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div id="weather-card" class="card shadow-sm border-0 d-none">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img id="weather-flag" src="" alt="" width="56" class="rounded shadow-sm d-none">
                        <div>
                            <h5 class="mb-0" id="weather-port-name"></h5>
                            <div class="text-muted small" id="weather-port-country"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label"><i class="fa-solid fa-temperature-half"></i> Suhu</div>
                                <div class="stat-value" id="weather-temp">-</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label"><i class="fa-solid fa-cloud-rain"></i> Curah Hujan</div>
                                <div class="stat-value" id="weather-precip">-</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label"><i class="fa-solid fa-wind"></i> Angin</div>
                                <div class="stat-value" id="weather-wind">-</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-box">
                                <div class="stat-label"><i class="fa-solid fa-triangle-exclamation"></i> Risiko Badai</div>
                                <div class="stat-value" id="weather-risk">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="weather-empty-state" class="card shadow-sm border-0">
                <div class="card-body text-center text-muted py-5">
                    <i class="fa-solid fa-cloud-sun fa-2x mb-2"></i>
                    <div>Pilih pelabuhan untuk melihat kondisi cuaca real-time (Open-Meteo).</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/weather-page.js') }}"></script>
@endpush