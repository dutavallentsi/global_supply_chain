<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\EconomicIndicator;
use App\Services\WorldBankService;
use Illuminate\Database\Seeder;

class EconomicIndicatorSeeder extends Seeder
{
    /**
     * NOTE: Untuk SEMUA negara (bukan hanya yang punya pelabuhan), supaya
     * fitur Country Comparison bisa menampilkan data ekonomi untuk negara
     * manapun yang dipilih user. Proses ini berat (~250 negara x 5 indikator
     * = ~1.250 request ke World Bank API) sehingga diberi jeda antar-negara
     * untuk menghindari koneksi ditolak/direset oleh server.
     */
    public function run(WorldBankService $service): void
    {
        $countries = Country::whereNotNull('cca3')->orderBy('name')->get();
        $total = $countries->count();
        $success = 0;
        $empty = 0;

        $this->command->info("Memulai pengambilan data ekonomi untuk {$total} negara...");
        $bar = $this->command->getOutput()->createProgressBar($total);

        foreach ($countries as $index => $country) {
            $rows = $service->getAllIndicators($country->cca3, 5);

            foreach ($rows as $row) {
                EconomicIndicator::updateOrCreate(
                    ['country_id' => $country->id, 'year' => $row['year']],
                    $row
                );
            }

            if (count($rows) > 0) {
                $success++;
            } else {
                $empty++;
            }

            $bar->advance();

            // Jeda singkat setiap 10 negara supaya tidak membanjiri API
            if (($index + 1) % 10 === 0) {
                usleep(500000); // 0.5 detik
            }
        }

        $bar->finish();
        $this->command->newLine(2);
        $this->command->info("Selesai. Berhasil: {$success} negara. Tidak ada data: {$empty} negara.");
        $this->command->line('Catatan: negara kecil/teritori seringkali memang tidak punya data World Bank sama sekali (wajar).');
    }
}