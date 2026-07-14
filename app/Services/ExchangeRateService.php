<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi ke ExchangeRate-API (https://www.exchangerate-api.com/).
 * Simpan API key di .env: EXCHANGE_RATE_API_KEY
 */
class ExchangeRateService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://v6.exchangerate-api.com/v6';

    public function __construct()
    {
        $this->apiKey = config('services.exchangerate.key');
    }

    /**
     * Ambil semua kurs terbaru relatif terhadap satu mata uang basis.
     * Return: ['USD' => 1, 'IDR' => 16200, 'EUR' => 0.92, ...]
     */
    public function getLatestRates(string $baseCurrency = 'USD'): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/{$this->apiKey}/latest/{$baseCurrency}");

            if (! $response->successful() || $response->json('result') !== 'success') {
                Log::warning('ExchangeRate API failed', ['body' => $response->body()]);
                return null;
            }

            return $response->json('conversion_rates');
        } catch (\Throwable $e) {
            Log::error('ExchangeRateService error: ' . $e->getMessage());
            return null;
        }
    }

    public function getPairRate(string $base, string $target): ?float
    {
        $rates = $this->getLatestRates($base);
        return $rates[$target] ?? null;
    }

    /**
     * Hitung volatilitas kurs (standar deviasi) dari data historis
     * yang tersimpan di tabel exchange_rates selama N hari terakhir.
     * Dipakai sebagai input currency_risk.
     */
    public function calculateVolatility(string $base, string $target, int $days = 30): float
    {
        $rates = \App\Models\ExchangeRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->where('rate_date', '>=', now()->subDays($days))
            ->orderBy('rate_date')
            ->pluck('rate')
            ->map(fn ($r) => (float) $r)
            ->toArray();

        if (count($rates) < 2) {
            return 0.0;
        }

        $mean = array_sum($rates) / count($rates);
        $variance = array_sum(array_map(fn ($r) => ($r - $mean) ** 2, $rates)) / count($rates);
        $stdDev = sqrt($variance);

        // Normalisasi jadi persentase terhadap rata-rata (coefficient of variation)
        return $mean > 0 ? round(($stdDev / $mean) * 100, 2) : 0.0;
    }
}