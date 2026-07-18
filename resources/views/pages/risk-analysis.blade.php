@extends('layouts.app')

@section('title', 'Risk Analysis')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <i class="fa-solid fa-triangle-exclamation"></i> Analisis Risiko Seluruh Pengiriman
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Rute</th>
                        <th>Status</th>
                        <th>Cuaca</th>
                        <th>Kemacetan</th>
                        <th>Geopolitik</th>
                        <th>Kurs</th>
                        <th>Inflasi</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shipments as $shipment)
                        @php $risk = $shipment->latestRiskScore; @endphp
                        <tr>
                            <td><a href="{{ route('dashboard.show', $shipment) }}">{{ $shipment->code }}</a></td>
                            <td class="small">{{ $shipment->originCountry->name }} → {{ $shipment->destinationCountry->name }}</td>
                            <td>
                                <span class="badge bg-{{ match($shipment->status) {
                                    'in_transit' => 'primary', 'delayed' => 'danger',
                                    'arrived' => 'success', 'cancelled' => 'secondary', default => 'warning',
                                } }}">{{ ucfirst(str_replace('_',' ',$shipment->status)) }}</span>
                            </td>
                            @if ($risk)
                                <td>{{ $risk->weather_risk }}</td>
                                <td>{{ $risk->port_congestion_risk }}</td>
                                <td>{{ $risk->geopolitical_risk }}</td>
                                <td>{{ $risk->currency_risk }}</td>
                                <td>{{ $risk->inflation_risk }}</td>
                                <td>
                                    <span class="badge bg-{{ match($risk->risk_level) {
                                        'critical' => 'danger', 'high' => 'warning',
                                        'medium' => 'info', default => 'success',
                                    } }}">{{ $risk->total_risk_score }} - {{ ucfirst($risk->risk_level) }}</span>
                                </td>
                            @else
                                <td colspan="6" class="text-muted small">Belum dihitung</td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data pengiriman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection