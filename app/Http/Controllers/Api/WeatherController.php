<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Services\OpenMeteoService;

class WeatherController extends Controller
{
    public function __construct(protected OpenMeteoService $weatherService) {}

    /**
     * Cuaca real-time untuk satu pelabuhan (dipanggil dari halaman Weather).
     * GET /api/weather/{port}
     */
    public function current(Port $port)
    {
        $data = $this->weatherService->getCurrentWeather((float) $port->latitude, (float) $port->longitude);

        if (! $data) {
            return response()->json(['message' => 'Gagal mengambil data cuaca dari Open-Meteo.'], 502);
        }

        return response()->json([
            'port' => [
                'id' => $port->id,
                'name' => $port->name,
                'country' => $port->country->name,
                'flag_url' => $port->country->flag_url,
            ],
            'weather' => $data,
        ]);
    }
}