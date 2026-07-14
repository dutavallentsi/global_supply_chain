<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\EconomicIndicator;
use App\Services\WorldBankService;
use Illuminate\Database\Seeder;

class EconomicIndicatorSeeder extends Seeder
{
    public function run(WorldBankService $service): void
    {
        // Fokus ke negara yang punya pelabuhan sample dulu, agar tidak terlalu banyak request
        $countries = Country::whereHas('ports')->get();

        foreach ($countries as $country) {
            if (! $country->cca3) {
                continue;
            }

            $rows = $service->getAllIndicators($country->cca3, 5);

            foreach ($rows as $row) {
                EconomicIndicator::updateOrCreate(
                    ['country_id' => $country->id, 'year' => $row['year']],
                    $row
                );
            }

            $this->command->info("Indikator ekonomi {$country->name}: " . count($rows) . ' tahun tersimpan.');
        }
    }
}