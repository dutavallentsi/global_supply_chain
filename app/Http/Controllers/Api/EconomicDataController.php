<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;

class EconomicDataController extends Controller
{
    /**
     * Data ekonomi terbaru satu negara (GDP, inflasi, populasi, mata uang)
     * untuk kartu "Country Comparison".
     * GET /api/economic/{country}
     */
    public function latest(Country $country)
    {
        $indicator = $country->economicIndicators()->orderByDesc('year')->first();

        return response()->json([
            'country' => [
                'id' => $country->id,
                'name' => $country->name,
                'region' => $country->region,
                'cca2' => $country->cca2,
                'flag_url' => $country->flag_url,
                'currency_code' => $country->currency_code,
                'currency_name' => $country->currency_name,
            ],
            'indicator' => $indicator ? [
                'year' => $indicator->year,
                'gdp_usd' => $indicator->gdp_usd,
                'inflation_rate' => $indicator->inflation_rate,
                'population' => $indicator->population,
                'exports_value_usd' => $indicator->exports_value_usd,
                'imports_value_usd' => $indicator->imports_value_usd,
            ] : null,
        ]);
    }
}