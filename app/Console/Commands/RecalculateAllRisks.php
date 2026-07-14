<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Services\RiskCalculatorService;
use Illuminate\Console\Command;

class RecalculateAllRisks extends Command
{
    protected $signature = 'scm:recalculate-risks';
    protected $description = 'Hitung ulang skor risiko untuk semua shipment yang masih aktif (pending/in_transit/delayed)';

    public function handle(RiskCalculatorService $calculator): int
    {
        $shipments = Shipment::whereIn('status', ['pending', 'in_transit', 'delayed'])->get();

        $bar = $this->output->createProgressBar($shipments->count());
        foreach ($shipments as $shipment) {
            $calculator->calculateForShipment($shipment);
            $bar->advance();
        }
        $bar->finish();

        $this->newLine();
        $this->info("Selesai menghitung ulang risiko untuk {$shipments->count()} pengiriman.");

        return self::SUCCESS;
    }
}