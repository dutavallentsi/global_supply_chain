<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ChartDataController extends Controller
{
    /**
     * Data historis kurs mata uang (untuk Chart.js line chart naik-turun).
     * GET /api/charts/exchange-rate?base=USD&target=IDR&days=30
     */
    public function exchangeRateHistory(Request $request)
    {
        $base = strtoupper($request->query('base', 'USD'));
        $target = strtoupper($request->query('target', 'IDR'));
        $days = (int) $request->query('days', 30);

        $rows = ExchangeRate::where('base_currency', $base)
            ->where('target_currency', $target)
            ->where('rate_date', '>=', now()->subDays($days))
            ->orderBy('rate_date')
            ->get(['rate_date', 'rate']);

        return response()->json([
            'base' => $base,
            'target' => $target,
            'labels' => $rows->pluck('rate_date')->map(fn ($d) => $d->format('Y-m-d')),
            'values' => $rows->pluck('rate'),
        ]);
    }

    /**
     * Data devisa/ekonomi per negara (GDP, inflasi, ekspor-impor) untuk
     * grafik perbandingan antar negara.
     * GET /api/charts/economic/{country}
     */
    public function economicIndicators(Country $country)
    {
        $rows = $country->economicIndicators()->orderBy('year')->get();

        return response()->json([
            'country' => $country->name,
            'labels' => $rows->pluck('year'),
            'gdp' => $rows->pluck('gdp_usd'),
            'inflation_rate' => $rows->pluck('inflation_rate'),
            'exports' => $rows->pluck('exports_value_usd'),
            'imports' => $rows->pluck('imports_value_usd'),
        ]);
    }
}