@extends('layouts.app')

@section('title', 'Port Location')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <i class="fa-solid fa-location-dot"></i> Peta Seluruh Pelabuhan
        </div>
        <div class="card-body p-0">
            <div id="map-full" style="height: 640px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/port-location.js') }}"></script>
@endpush