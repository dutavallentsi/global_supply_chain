<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi ke World Bank Open Data API (https://api.worldbank.org).
 * Tidak butuh API key. Menggunakan kode ISO alpha-3 (cca3).
 */
class WorldBankService
{
    protected string $baseUrl = 'https://api.worldbank.org/v2';

    // Kode indikator resmi World Bank
    protected array $indicators = [
        'gdp' => 'NY.GDP.MKTP.CD',
        'inflation' => 'FP.CPI.TOTL.ZG',
        'population' => 'SP.POP.TOTL',
        'exports' => 'NE.EXP.GNFS.CD',
        'imports' => 'NE.IMP.GNFS.CD',
    ];

    /**
     * Ambil satu indikator untuk satu negara, beberapa tahun terakhir.
     */
    public function getIndicator(string $cca3, string $indicatorKey, int $years = 5): array
    {
        $code = $this->indicators[$indicatorKey] ?? null;
        if (! $code) {
            return [];
        }

        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/country/{$cca3}/indicator/{$code}", [
                'format' => 'json',
                'per_page' => $years,
                'mrnev' => $years, // most recent non-empty values
            ]);

            if (! $response->successful()) {
                return [];
            }

            $rows = $response->json()[1] ?? [];

            return collect($rows)
                ->filter(fn ($r) => $r['value'] !== null)
                ->map(fn ($r) => ['year' => (int) $r['date'], 'value' => (float) $r['value']])
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::error('WorldBankService error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil semua 5 indikator sekaligus untuk satu negara, digabung per tahun.
     * Return: [ ['year'=>2024,'gdp'=>...,'inflation_rate'=>...,'population'=>...,'exports'=>...,'imports'=>...], ... ]
     */
    public function getAllIndicators(string $cca3, int $years = 5): array
    {
        $gdp = collect($this->getIndicator($cca3, 'gdp', $years))->keyBy('year');
        $inflation = collect($this->getIndicator($cca3, 'inflation', $years))->keyBy('year');
        $population = collect($this->getIndicator($cca3, 'population', $years))->keyBy('year');
        $exports = collect($this->getIndicator($cca3, 'exports', $years))->keyBy('year');
        $imports = collect($this->getIndicator($cca3, 'imports', $years))->keyBy('year');

        $allYears = $gdp->keys()
            ->merge($inflation->keys())
            ->merge($population->keys())
            ->unique()
            ->sortDesc()
            ->values();

        return $allYears->map(function ($year) use ($gdp, $inflation, $population, $exports, $imports) {
            return [
                'year' => $year,
                'gdp_usd' => $gdp[$year]['value'] ?? null,
                'inflation_rate' => $inflation[$year]['value'] ?? null,
                'population' => $population[$year]['value'] ?? null,
                'exports_value_usd' => $exports[$year]['value'] ?? null,
                'imports_value_usd' => $imports[$year]['value'] ?? null,
            ];
        })->all();
    }
}