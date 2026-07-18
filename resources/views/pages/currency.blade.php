@extends('layouts.app')

@section('title', 'Currency')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-money-bill-transfer"></i> Tren Kurs Mata Uang (30 hari terakhir)</span>
            <select class="form-select form-select-sm w-auto" id="select-currency-target">
                <option value="">Memuat...</option>
            </select>
        </div>
        <div class="card-body" style="height: 420px;">
            <canvas id="chart-currency-page"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/currency-page.js') }}"></script>
@endpush