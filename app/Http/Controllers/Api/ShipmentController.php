<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\RiskCalculatorService;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(protected RiskCalculatorService $riskCalculator) {}

    public function index()
    {
        return Shipment::with(['originCountry', 'destinationCountry', 'latestRiskScore'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:shipments,code',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'origin_country_id' => 'required|exists:countries,id',
            'destination_country_id' => 'required|exists:countries,id|different:origin_country_id',
            'origin_port_id' => 'nullable|exists:ports,id',
            'destination_port_id' => 'nullable|exists:ports,id',
            'transaction_currency' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
            'departure_date' => 'required|date',
            'estimated_arrival_date' => 'required|date|after:departure_date',
        ]);

        $validated['status'] = 'pending';
        $shipment = Shipment::create($validated);

        // Hitung skor risiko awal begitu shipment dibuat
        $this->riskCalculator->calculateForShipment($shipment);

        return response()->json($shipment->load('latestRiskScore'), 201);
    }

    public function update(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,in_transit,delayed,arrived,cancelled',
            'current_latitude' => 'sometimes|numeric',
            'current_longitude' => 'sometimes|numeric',
            'actual_arrival_date' => 'sometimes|date',
            'notes' => 'sometimes|string|nullable',
        ]);

        $shipment->update($validated);

        return response()->json($shipment);
    }

    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return response()->json(null, 204);
    }

    /**
     * Trigger ulang perhitungan risiko untuk satu shipment (dipanggil AJAX
     * dari tombol "Refresh Risiko" di dashboard, atau oleh scheduler).
     */
    public function recalculateRisk(Shipment $shipment)
    {
        $riskScore = $this->riskCalculator->calculateForShipment($shipment);
        return response()->json($riskScore);
    }
}