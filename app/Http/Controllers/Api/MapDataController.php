<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Shipment;

class MapDataController extends Controller
{
    /**
     * Semua titik pelabuhan (untuk marker dasar di peta Leaflet).
     * GET /api/map/ports
     */
    public function ports()
    {
        return Port::with('country', 'latestWeather')->get()->map(fn ($port) => [
            'id' => $port->id,
            'name' => $port->name,
            'country' => $port->country->name,
            'lat' => (float) $port->latitude,
            'lng' => (float) $port->longitude,
            'storm_risk_level' => $port->latestWeather->storm_risk_level ?? 'unknown',
        ]);
    }

    /**
     * Rute + posisi terkini setiap shipment aktif (untuk digambar sebagai
     * polyline origin -> current -> destination di Leaflet).
     * GET /api/map/shipments
     */
    public function shipments()
    {
        return Shipment::with(['originPort', 'destinationPort', 'latestRiskScore'])
            ->whereIn('status', ['pending', 'in_transit', 'delayed'])
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'code' => $s->code,
                'status' => $s->status,
                'origin' => $s->originPort ? [
                    'lat' => (float) $s->originPort->latitude,
                    'lng' => (float) $s->originPort->longitude,
                    'name' => $s->originPort->name,
                ] : null,
                'destination' => $s->destinationPort ? [
                    'lat' => (float) $s->destinationPort->latitude,
                    'lng' => (float) $s->destinationPort->longitude,
                    'name' => $s->destinationPort->name,
                ] : null,
                'current' => ($s->current_latitude && $s->current_longitude) ? [
                    'lat' => (float) $s->current_latitude,
                    'lng' => (float) $s->current_longitude,
                ] : null,
                'risk_level' => $s->latestRiskScore->risk_level ?? 'low',
            ]);
    }
}