<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,         // 0. Buat akun admin default
            CountrySeeder::class,           // 1. Import semua negara dari RestCountries
            PortSeeder::class,              // 2. Seed pelabuhan sampel (butuh negara sudah ada)
            EconomicIndicatorSeeder::class, // 3. Ambil data World Bank untuk negara yang punya pelabuhan
        ]);
    }
}
