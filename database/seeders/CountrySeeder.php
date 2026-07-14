<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Services\RestCountriesService;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Menarik data negara langsung dari RestCountries API dan menyimpannya.
     * Bisa dijalankan: php artisan db:seed --class=CountrySeeder
     */
    public function run(RestCountriesService $service): void
    {
        $countries = $service->getAll();

        foreach ($countries as $data) {
            if (! $data['cca2']) {
                continue;
            }

            Country::updateOrCreate(
                ['cca2' => $data['cca2']],
                $data
            );
        }

        $this->command->info('Berhasil import ' . count($countries) . ' negara.');
    }
}