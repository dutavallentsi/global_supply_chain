@extends('layouts.app')

@section('title', 'Port Location')

@section('content')
    <div class="app-topbar">
        <h4><i class="fa-solid fa-location-dot me-2" style="color:var(--scm-primary)"></i>Port Location</h4>
        <span class="text-muted small fw-600">Peta seluruh pelabuhan beserta kondisi risiko cuaca terkini</span>
    </div>

    <div class="card shadow-sm border-0">
        {{-- Header dengan filter bar --}}
        <div class="card-header d-flex align-items-center gap-3 flex-wrap">
            <span class="fw-bold">
                <i class="fa-solid fa-map-location-dot"></i> Peta Seluruh Pelabuhan
            </span>

            <div class="map-filter-bar ms-auto">
                <span class="text-muted small fw-600 me-1">Filter:</span>

                <button class="filter-btn active" data-level="all">
                    <i class="fa-solid fa-layer-group" style="font-size:0.7rem"></i> Semua
                </button>
                <button class="filter-btn" data-level="low">
                    <span class="dot" style="background:#10b981"></span> Rendah
                </button>
                <button class="filter-btn" data-level="medium">
                    <span class="dot" style="background:#06b6d4"></span> Sedang
                </button>
                <button class="filter-btn" data-level="high">
                    <span class="dot" style="background:#fbbf24"></span> Tinggi
                </button>
                <button class="filter-btn" data-level="critical">
                    <span class="dot" style="background:#ef4444"></span> Kritis
                </button>
                <button class="filter-btn" data-level="unknown">
                    <span class="dot" style="background:#64748b"></span> Tidak Diketahui
                </button>

                <span id="port-count-wrap" class="ms-2">
                    Menampilkan <span id="port-count">—</span> pelabuhan
                </span>
            </div>
        </div>

        {{-- Peta penuh --}}
        <div class="card-body p-0">
            <div id="map-full" style="height: 640px;"></div>
        </div>

        {{-- Footer keterangan --}}
        <div class="card-footer">
            <div class="d-flex align-items-center gap-4 flex-wrap" style="font-size:0.8rem;color:var(--scm-text-muted)">
                <span><i class="fa-solid fa-circle-info me-1" style="color:var(--scm-primary)"></i>Klik marker pelabuhan untuk melihat detail kondisi cuaca</span>
                <span><i class="fa-solid fa-arrows-up-down-left-right me-1"></i>Scroll untuk zoom, drag untuk geser peta</span>
                <span class="ms-auto">
                    <i class="fa-solid fa-database me-1"></i>Sumber: Open-Meteo &amp; OpenStreetMap
                </span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/port-location.js') }}"></script>
@endpush
