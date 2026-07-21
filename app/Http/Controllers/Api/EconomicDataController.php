<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\EconomicIndicator;
use App\Services\WorldBankService;

class EconomicDataController extends Controller
{
    public function __construct(protected WorldBankService $worldBank) {}

    /**
     * Data ekonomi terbaru satu negara (GDP, inflasi, populasi, mata uang)
     * untuk kartu "Country Comparison".
     * GET /api/economic/{country}
     *
     * Prioritas: data lokal DB → fallback fetch World Bank API → simpan ke DB.
     */
    public function latest(Country $country)
    {
        $indicator = $country->economicIndicators()->orderByDesc('year')->first();

        // Jika tidak ada data di DB dan negara punya cca3, fetch dari World Bank
        if (! $indicator && $country->cca3) {
            $rows = $this->worldBank->getAllIndicators($country->cca3, 5);

            foreach ($rows as $row) {
                EconomicIndicator::updateOrCreate(
                    ['country_id' => $country->id, 'year' => $row['year']],
                    $row
                );
            }

            // Ambil ulang dari DB setelah disimpan
            $indicator = $country->economicIndicators()->orderByDesc('year')->first();
        }

        return response()->json([
            'country' => [
                'id'            => $country->id,
                'name'          => $country->name,
                'region'        => $country->region,
                'cca2'          => $country->cca2,
                'flag_url'      => $country->flag_url,
                'currency_code' => $country->currency_code,
                'currency_name' => $country->currency_name,
            ],
            'indicator' => $indicator ? [
                'year'              => $indicator->year,
                'gdp_usd'           => $indicator->gdp_usd,
                'inflation_rate'    => $indicator->inflation_rate,
                'population'        => $indicator->population,
                'exports_value_usd' => $indicator->exports_value_usd,
                'imports_value_usd' => $indicator->imports_value_usd,
            ] : null,
        ]);
    }
}