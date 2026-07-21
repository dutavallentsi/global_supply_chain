<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Services\OpenMeteoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function __construct(protected OpenMeteoService $weatherService) {}

    /**
     * Cuaca real-time untuk satu pelabuhan (dipanggil dari halaman Weather).
     * GET /api/weather/{port}
     * Jika Open-Meteo rate limit (429), coba fallback ke Open-Meteo API 2 (CDN).
     */
    public function current(Port $port)
    {
        $lat = (float) $port->latitude;
        $lon = (float) $port->longitude;

        $data = $this->weatherService->getCurrentWeather($lat, $lon);

        // Jika gagal karena rate limit, coba request langsung ke endpoint alternatif
        if (! $data) {
            $data = $this->tryAlternativeWeather($lat, $lon);
        }

        if (! $data) {
            return response()->json([
                'message' => 'Data cuaca tidak tersedia saat ini. Open-Meteo API sedang dalam batas limit harian. Coba lagi besok atau gunakan VPN.',
                'error_code' => 'RATE_LIMITED',
            ], 503);
        }

        return response()->json([
            'port' => [
                'id'       => $port->id,
                'name'     => $port->name,
                'country'  => $port->country->name,
                'flag_url' => $port->country->flag_url,
            ],
            'weather' => $data,
        ]);
    }

    /**
     * Fallback: coba endpoint Open-Meteo alternatif (previous-day data)
     * atau Open-Meteo Forecast dengan parameter berbeda.
     */
    protected function tryAlternativeWeather(float $lat, float $lon): ?array
    {
        try {
            // Coba dengan hourly daripada current (kadang endpoint berbeda)
            $response = Http::timeout(15)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'   => $lat,
                'longitude'  => $lon,
                'hourly'     => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
                'timezone'   => 'auto',
                'forecast_days' => 1,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $hourly = $response->json('hourly');
            if (empty($hourly)) {
                return null;
            }

            // Ambil data jam terakhir yang tersedia
            $idx = max(0, count($hourly['time'] ?? []) - 1);
            $windSpeed = (float) ($hourly['wind_speed_10m'][$idx] ?? 0);
            $precip    = (float) ($hourly['precipitation'][$idx] ?? 0);
            $wcode     = (int)   ($hourly['weather_code'][$idx] ?? 0);

            return [
                'temperature_c'    => $hourly['temperature_2m'][$idx] ?? null,
                'precipitation_mm' => $precip,
                'wind_speed_kmh'   => $windSpeed,
                'weather_code'     => $wcode,
                'storm_risk_level' => $this->classifyStormRisk($windSpeed, $precip, $wcode),
            ];
        } catch (\Throwable $e) {
            Log::error('WeatherController fallback error: ' . $e->getMessage());
            return null;
        }
    }

    protected function classifyStormRisk(float $windKmh, float $precipMm, int $weatherCode): string
    {
        if ($weatherCode >= 95 || $windKmh >= 60 || $precipMm >= 20) return 'severe';
        if ($windKmh >= 40 || $precipMm >= 10) return 'high';
        if ($windKmh >= 20 || $precipMm >= 3) return 'medium';
        return 'low';
    }
}