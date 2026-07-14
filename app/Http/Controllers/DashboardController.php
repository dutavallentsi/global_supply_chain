<?php

namespace App\Http\Controllers;

use App\Models\Shipment;

class DashboardController extends Controller
{
    public function index()
    {
        $summary = [
            'total_shipments' => Shipment::count(),
            'in_transit' => Shipment::where('status', 'in_transit')->count(),
            'delayed' => Shipment::where('status', 'delayed')->count(),
            'arrived' => Shipment::where('status', 'arrived')->count(),
        ];

        $shipments = Shipment::with([
            'originCountry', 'destinationCountry',
            'originPort', 'destinationPort', 'latestRiskScore',
        ])->orderByDesc('created_at')->paginate(10);

        // Shipment aktif (belum tiba/dibatalkan) dengan skor risiko terbaru high/critical,
        // untuk ditampilkan sebagai alert peringatan di atas dashboard.
        $highRiskShipments = Shipment::with(['destinationCountry', 'latestRiskScore'])
            ->whereIn('status', ['pending', 'in_transit', 'delayed'])
            ->whereHas('latestRiskScore', fn ($q) => $q->whereIn('risk_level', ['high', 'critical']))
            ->get()
            ->sortByDesc(fn ($s) => $s->latestRiskScore->total_risk_score)
            ->values();

        return view('dashboard.index', compact('summary', 'shipments', 'highRiskShipments'));
    }

    public function show(Shipment $shipment)
    {
        $shipment->load([
            'originCountry', 'destinationCountry',
            'originPort', 'destinationPort',
            'riskScores' => fn ($q) => $q->orderByDesc('calculated_at')->limit(30),
        ]);

        return view('dashboard.show', compact('shipment'));
    }
}
