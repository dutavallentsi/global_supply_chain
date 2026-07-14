<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi ke Open-Meteo API (gratis, tanpa API key).
 * Docs: https://open-meteo.com/en/docs
 */
class OpenMeteoService
{
    protected string $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Ambil kondisi cuaca terkini + risiko badai untuk satu titik koordinat.
     * Dipakai untuk pelabuhan asal/tujuan/transit.
     */
    public function getCurrentWeather(float $lat, float $lon): ?array
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl, [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
                'timezone' => 'auto',
            ]);

            if (! $response->successful()) {
                Log::warning('Open-Meteo request failed', ['status' => $response->status()]);
                return null;
            }

            $current = $response->json('current');

            return [
                'temperature_c' => $current['temperature_2m'] ?? null,
                'precipitation_mm' => $current['precipitation'] ?? null,
                'wind_speed_kmh' => $current['wind_speed_10m'] ?? null,
                'weather_code' => $current['weather_code'] ?? null,
                'storm_risk_level' => $this->classifyStormRisk(
                    $current['wind_speed_10m'] ?? 0,
                    $current['precipitation'] ?? 0,
                    $current['weather_code'] ?? 0
                ),
            ];
        } catch (\Throwable $e) {
            Log::error('OpenMeteoService error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Klasifikasi sederhana risiko badai berdasarkan angin, curah hujan,
     * dan weather_code (WMO code, >=95 artinya thunderstorm).
     */
    protected function classifyStormRisk(float $windKmh, float $precipMm, int $weatherCode): string
    {
        if ($weatherCode >= 95 || $windKmh >= 60 || $precipMm >= 20) {
            return 'severe';
        }
        if ($windKmh >= 40 || $precipMm >= 10) {
            return 'high';
        }
        if ($windKmh >= 20 || $precipMm >= 3) {
            return 'medium';
        }
        return 'low';
    }
}