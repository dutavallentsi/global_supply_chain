<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPortsDataset extends Command
{
    protected $signature = 'scm:import-ports {path=storage/app/ports-dataset.json}';
    protected $description = 'Import massal data pelabuhan dari dataset World Port Index (JSON) ke tabel ports';

    /**
     * Alias nama negara yang sering beda penulisan antara dataset WPI
     * dan tabel countries kita (sumber: countries.dev).
     */
    protected array $countryAliases = [
        'united states' => 'United States of America',
        'usa' => 'United States of America',
        'south korea' => 'Korea (Republic of)',
        'korea, south' => 'Korea (Republic of)',
        'north korea' => "Korea (Democratic People's Republic of)",
        'russia' => 'Russian Federation',
        'vietnam' => 'Viet Nam',
        'uk' => 'United Kingdom of Great Britain and Northern Ireland',
        'united kingdom' => 'United Kingdom of Great Britain and Northern Ireland',
        'uae' => 'United Arab Emirates',
        'ivory coast' => "Côte d'Ivoire",
        'laos' => "Lao People's Democratic Republic",
        'syria' => 'Syrian Arab Republic',
        'iran' => 'Iran (Islamic Republic of)',
        'brunei' => 'Brunei Darussalam',
        'tanzania' => 'United Republic of Tanzania',
        'moldova' => 'Republic of Moldova',
        'bolivia' => 'Bolivia (Plurinational State of)',
        'venezuela' => 'Venezuela (Bolivarian Republic of)',
        'micronesia' => 'Micronesia (Federated States of)',
        'macedonia' => 'North Macedonia',
        'cape verde' => 'Cabo Verde',
        'swaziland' => 'Eswatini',
        'czech republic' => 'Czechia',
        'myanmar (burma)' => 'Myanmar',
        'burma' => 'Myanmar',
    ];

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan di: {$path}");
            $this->line('Pastikan sudah mengunduh ports-dataset.json ke lokasi tersebut.');
            return self::FAILURE;
        }

        $this->info('Membaca file JSON...');
        $json = json_decode(file_get_contents($path), true);

        $records = $json['ports'] ?? $json; // dukung format wrapped maupun array polos
        if (! is_array($records)) {
            $this->error('Format JSON tidak dikenali (diharapkan ada key "ports" berisi array).');
            return self::FAILURE;
        }

        $this->info('Total record di dataset: ' . count($records));

        // Index semua negara by lowercase name untuk pencocokan cepat
        $countriesByName = Country::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->toArray();

        // Unlocode yang sudah ada (untuk mencegah duplikat saat command dijalankan ulang)
        $existingCodes = DB::table('ports')->whereNotNull('unlocode')->pluck('unlocode')->flip()->toArray();

        $imported = 0;
        $skippedNoCountry = 0;
        $skippedNoCoords = 0;
        $skippedDuplicate = 0;
        $unmatchedCountries = [];

        $batch = [];
        $now = now();

        $bar = $this->output->createProgressBar(count($records));

        foreach ($records as $record) {
            $bar->advance();

            $lat = $record['latitude'] ?? null;
            $lng = $record['longitude'] ?? null;
            if ($lat === null || $lng === null) {
                $skippedNoCoords++;
                continue;
            }

            $countryName = trim($record['country'] ?? '');
            if ($countryName === '') {
                $skippedNoCountry++;
                continue;
            }

            $key = strtolower($countryName);
            $countryId = $countriesByName[$key]
                ?? $countriesByName[strtolower($this->countryAliases[$key] ?? '')]
                ?? null;

            if (! $countryId) {
                $skippedNoCountry++;
                $unmatchedCountries[$countryName] = ($unmatchedCountries[$countryName] ?? 0) + 1;
                continue;
            }

            $name = $record['wpi_port_name'] ?? $record['point_of_interest'] ?? 'Unnamed Port';
            $name = ucwords(strtolower($name));

            // Buat kode unik deterministik supaya aman dijalankan berulang tanpa duplikat
            $unlocode = isset($record['wpi_port_id'])
                ? 'W' . $record['wpi_port_id']
                : strtoupper(substr(md5($name . $lat . $lng), 0, 9));

            if (isset($existingCodes[$unlocode])) {
                $skippedDuplicate++;
                continue;
            }
            $existingCodes[$unlocode] = true; // tandai supaya tidak dobel dalam batch yang sama

            $batch[] = [
                'country_id' => $countryId,
                'name' => $name,
                'unlocode' => $unlocode,
                'latitude' => $lat,
                'longitude' => $lng,
                'type' => 'sea',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= 500) {
                DB::table('ports')->insert($batch);
                $imported += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('ports')->insert($batch);
            $imported += count($batch);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Berhasil impor: {$imported} pelabuhan baru.");
        $this->line("Dilewati (negara tidak cocok): {$skippedNoCountry}");
        $this->line("Dilewati (koordinat kosong): {$skippedNoCoords}");
        $this->line("Dilewati (duplikat): {$skippedDuplicate}");

        if (! empty($unmatchedCountries)) {
            arsort($unmatchedCountries);
            $this->newLine();
            $this->warn('Nama negara yang tidak cocok dengan tabel countries (10 teratas):');
            foreach (array_slice($unmatchedCountries, 0, 10, true) as $name => $count) {
                $this->line("  - {$name} ({$count} pelabuhan)");
            }
            $this->line('Tambahkan alias yang sesuai di $countryAliases pada command ini jika perlu, lalu jalankan ulang.');
        }

        return self::SUCCESS;
    }
}