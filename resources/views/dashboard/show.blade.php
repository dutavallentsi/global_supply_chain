@extends('layouts.app')

@section('title', 'Detail Pengiriman - ' . $shipment->code)

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengiriman {{ $shipment->code }}</h4>
        <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Informasi Umum</h6>
                    <table class="table table-sm mb-0">
                        <tr><th>Produk</th><td>{{ $shipment->product_name }} ({{ $shipment->quantity }} unit)</td></tr>
                        <tr><th>Asal</th><td>{{ $shipment->originCountry->name }} - {{ $shipment->originPort->name ?? '-' }}</td></tr>
                        <tr><th>Tujuan</th><td>{{ $shipment->destinationCountry->name }} - {{ $shipment->destinationPort->name ?? '-' }}</td></tr>
                        <tr><th>Nilai Transaksi</th><td>{{ $shipment->transaction_currency }} {{ number_format($shipment->amount, 2) }}</td></tr>
                        <tr><th>Berangkat</th><td>{{ $shipment->departure_date->format('d M Y') }}</td></tr>
                        <tr><th>Estimasi Tiba</th><td>{{ $shipment->estimated_arrival_date->format('d M Y') }}</td></tr>
                        <tr><th>Status</th><td>{{ ucfirst(str_replace('_',' ',$shipment->status)) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header">Tren Skor Risiko (30 perhitungan terakhir)</div>
                <div class="card-body">
                    <canvas id="chart-risk-history" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
const riskHistoryData = @json($shipment->riskScores->reverse()->values());
</script>
<script src="{{ asset('js/shipment-detail.js') }}"></script>
@endpush