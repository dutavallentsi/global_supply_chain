<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Console\Command;

class SyncExchangeRates extends Command
{
    protected $signature = 'scm:sync-exchange-rates {--base=USD}';
    protected $description = 'Ambil kurs terbaru dari ExchangeRate API dan simpan sebagai snapshot harian untuk grafik naik-turun devisa';

    public function handle(ExchangeRateService $service): int
    {
        $base = $this->option('base');
        $rates = $service->getLatestRates($base);

        if (! $rates) {
            $this->error('Gagal mengambil data kurs.');
            return self::FAILURE;
        }

        $today = now()->toDateString();
        $currencyCodes = Country::whereNotNull('currency_code')->pluck('currency_code')->unique();

        $count = 0;
        foreach ($currencyCodes as $target) {
            if (! isset($rates[$target])) {
                continue;
            }

            ExchangeRate::updateOrCreate(
                ['base_currency' => $base, 'target_currency' => $target, 'rate_date' => $today],
                ['rate' => $rates[$target]]
            );
            $count++;
        }

        $this->info("Berhasil menyimpan {$count} kurs mata uang untuk tanggal {$today}.");
        return self::SUCCESS;
    }
}