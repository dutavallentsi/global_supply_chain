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
     * bersumber dari data publik UN/LOCODE), mencakup Asia, Eropa,
     * Amerika, Afrika, Timur Tengah, dan Oseania.
     */
    public function run(): void
    {
        $ports = [
            // ===== Asia =====
            ['country' => 'ID', 'name' => 'Tanjung Priok', 'unlocode' => 'IDJKT', 'lat' => -6.1044, 'lng' => 106.8827],
            ['country' => 'ID', 'name' => 'Tanjung Perak', 'unlocode' => 'IDSUB', 'lat' => -7.2011, 'lng' => 112.7370],
            ['country' => 'CN', 'name' => 'Port of Shanghai', 'unlocode' => 'CNSHA', 'lat' => 31.2304, 'lng' => 121.4737],
            ['country' => 'CN', 'name' => 'Port of Shenzhen', 'unlocode' => 'CNSZX', 'lat' => 22.5431, 'lng' => 114.0579],
            ['country' => 'SG', 'name' => 'Port of Singapore', 'unlocode' => 'SGSIN', 'lat' => 1.2644, 'lng' => 103.8200],
            ['country' => 'JP', 'name' => 'Port of Yokohama', 'unlocode' => 'JPYOK', 'lat' => 35.4437, 'lng' => 139.6380],
            ['country' => 'KR', 'name' => 'Port of Busan', 'unlocode' => 'KRPUS', 'lat' => 35.1796, 'lng' => 129.0756],
            ['country' => 'IN', 'name' => 'Jawaharlal Nehru Port', 'unlocode' => 'INNSA', 'lat' => 18.9490, 'lng' => 72.9525],
            ['country' => 'LK', 'name' => 'Port of Colombo', 'unlocode' => 'LKCMB', 'lat' => 6.9497, 'lng' => 79.8442],
            ['country' => 'BD', 'name' => 'Port of Chittagong', 'unlocode' => 'BDCGP', 'lat' => 22.3255, 'lng' => 91.8060],
            ['country' => 'PK', 'name' => 'Port of Karachi', 'unlocode' => 'PKKHI', 'lat' => 24.8138, 'lng' => 66.9822],
            ['country' => 'VN', 'name' => 'Ho Chi Minh Port', 'unlocode' => 'VNSGN', 'lat' => 10.7756, 'lng' => 106.7019],
            ['country' => 'PH', 'name' => 'Port of Manila', 'unlocode' => 'PHMNL', 'lat' => 14.5906, 'lng' => 120.9647],
            ['country' => 'TH', 'name' => 'Laem Chabang Port', 'unlocode' => 'THLCH', 'lat' => 13.0827, 'lng' => 100.8833],
            ['country' => 'MY', 'name' => 'Port Klang', 'unlocode' => 'MYPKG', 'lat' => 3.0000, 'lng' => 101.4000],

            // ===== Timur Tengah =====
            ['country' => 'AE', 'name' => 'Jebel Ali Port', 'unlocode' => 'AEJEA', 'lat' => 25.0119, 'lng' => 55.0617],
            ['country' => 'SA', 'name' => 'Jeddah Islamic Port', 'unlocode' => 'SAJED', 'lat' => 21.4858, 'lng' => 39.1925],
            ['country' => 'TR', 'name' => 'Port of Istanbul', 'unlocode' => 'TRIST', 'lat' => 41.0082, 'lng' => 28.9784],

            // ===== Eropa =====
            ['country' => 'NL', 'name' => 'Port of Rotterdam', 'unlocode' => 'NLRTM', 'lat' => 51.9496, 'lng' => 4.1453],
            ['country' => 'DE', 'name' => 'Port of Hamburg', 'unlocode' => 'DEHAM', 'lat' => 53.5459, 'lng' => 9.9695],
            ['country' => 'BE', 'name' => 'Port of Antwerp', 'unlocode' => 'BEANR', 'lat' => 51.2603, 'lng' => 4.4025],
            ['country' => 'GB', 'name' => 'Port of Felixstowe', 'unlocode' => 'GBFXT', 'lat' => 51.9540, 'lng' => 1.3510],
            ['country' => 'FR', 'name' => 'Port of Le Havre', 'unlocode' => 'FRLEH', 'lat' => 49.4944, 'lng' => 0.1079],
            ['country' => 'ES', 'name' => 'Port of Barcelona', 'unlocode' => 'ESBCN', 'lat' => 41.3496, 'lng' => 2.1590],
            ['country' => 'GR', 'name' => 'Port of Piraeus', 'unlocode' => 'GRPIR', 'lat' => 37.9475, 'lng' => 23.6367],

            // ===== Amerika =====
            ['country' => 'US', 'name' => 'Port of Los Angeles', 'unlocode' => 'USLAX', 'lat' => 33.7405, 'lng' => -118.2723],
            ['country' => 'US', 'name' => 'Port of New York', 'unlocode' => 'USNYC', 'lat' => 40.6700, 'lng' => -74.0446],
            ['country' => 'US', 'name' => 'Port of Norfolk', 'unlocode' => 'USORF', 'lat' => 36.8468, 'lng' => -76.2852],
            ['country' => 'CA', 'name' => 'Port of Vancouver', 'unlocode' => 'CAVAN', 'lat' => 49.2827, 'lng' => -123.1207],
            ['country' => 'BR', 'name' => 'Port of Santos', 'unlocode' => 'BRSSZ', 'lat' => -23.9608, 'lng' => -46.3339],
            ['country' => 'PE', 'name' => 'Port of Callao', 'unlocode' => 'PECLL', 'lat' => -12.0566, 'lng' => -77.1181],

            // ===== Afrika =====
            ['country' => 'ZA', 'name' => 'Port of Durban', 'unlocode' => 'ZADUR', 'lat' => -29.8587, 'lng' => 31.0218],
            ['country' => 'KE', 'name' => 'Port of Mombasa', 'unlocode' => 'KEMBA', 'lat' => -4.0435, 'lng' => 39.6682],
            ['country' => 'EG', 'name' => 'Port of Alexandria', 'unlocode' => 'EGALY', 'lat' => 31.2001, 'lng' => 29.9187],

            // ===== Oseania =====
            ['country' => 'AU', 'name' => 'Port of Melbourne', 'unlocode' => 'AUMEL', 'lat' => -37.8136, 'lng' => 144.9631],
            ['country' => 'AU', 'name' => 'Port of Sydney', 'unlocode' => 'AUSYD', 'lat' => -33.8688, 'lng' => 151.2093],
            ['country' => 'NZ', 'name' => 'Port of Auckland', 'unlocode' => 'NZAKL', 'lat' => -36.8485, 'lng' => 174.7633],
        ];

        $imported = 0;
        $skipped = 0;

        foreach ($ports as $p) {
            $country = Country::where('cca2', $p['country'])->first();
            if (! $country) {
                $skipped++;
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
            $imported++;
        }

        $this->command->info("Berhasil seed {$imported} pelabuhan" . ($skipped > 0 ? " ({$skipped} dilewati karena negara tidak ditemukan)." : '.'));
    }
}