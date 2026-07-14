<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    /**
     * NOTE: MarineTraffic API resmi berbayar. Sebagai alternatif gratis,
     * kita seed manual daftar pelabuhan utama dunia (koordinat statis,
     * bersumber dari data publik UN/LOCODE). Bisa ditambah sesuai kebutuhan.
     */
    public function run(): void
    {
        $ports = [
            ['country' => 'ID', 'name' => 'Tanjung Priok', 'unlocode' => 'IDJKT', 'lat' => -6.1044, 'lng' => 106.8827],
            ['country' => 'ID', 'name' => 'Tanjung Perak', 'unlocode' => 'IDSUB', 'lat' => -7.2011, 'lng' => 112.7370],
            ['country' => 'CN', 'name' => 'Port of Shanghai', 'unlocode' => 'CNSHA', 'lat' => 31.2304, 'lng' => 121.4737],
            ['country' => 'CN', 'name' => 'Port of Shenzhen', 'unlocode' => 'CNSZX', 'lat' => 22.5431, 'lng' => 114.0579],
            ['country' => 'SG', 'name' => 'Port of Singapore', 'unlocode' => 'SGSIN', 'lat' => 1.2644, 'lng' => 103.8200],
            ['country' => 'US', 'name' => 'Port of Los Angeles', 'unlocode' => 'USLAX', 'lat' => 33.7405, 'lng' => -118.2723],
            ['country' => 'US', 'name' => 'Port of New York', 'unlocode' => 'USNYC', 'lat' => 40.6700, 'lng' => -74.0446],
            ['country' => 'NL', 'name' => 'Port of Rotterdam', 'unlocode' => 'NLRTM', 'lat' => 51.9496, 'lng' => 4.1453],
            ['country' => 'JP', 'name' => 'Port of Yokohama', 'unlocode' => 'JPYOK', 'lat' => 35.4437, 'lng' => 139.6380],
            ['country' => 'KR', 'name' => 'Port of Busan', 'unlocode' => 'KRPUS', 'lat' => 35.1796, 'lng' => 129.0756],
            ['country' => 'AE', 'name' => 'Jebel Ali Port', 'unlocode' => 'AEJEA', 'lat' => 25.0119, 'lng' => 55.0617],
            ['country' => 'IN', 'name' => 'Jawaharlal Nehru Port', 'unlocode' => 'INNSA', 'lat' => 18.9490, 'lng' => 72.9525],
        ];

        foreach ($ports as $p) {
            $country = Country::where('cca2', $p['country'])->first();
            if (! $country) {
                continue;
            }

            Port::updateOrCreate(
                ['unlocode' => $p['unlocode']],
                [
                    'country_id' => $country->id,
                    'name' => $p['name'],
                    'latitude' => $p['lat'],
                    'longitude' => $p['lng'],
                    'type' => 'sea',
                ]
            );
        }

        $this->command->info('Berhasil seed ' . count($ports) . ' pelabuhan sampel.');
    }
}