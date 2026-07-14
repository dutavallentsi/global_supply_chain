<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();

            // Setiap komponen bernilai 0-100 (semakin tinggi semakin berisiko)
            $table->decimal('weather_risk', 5, 2)->default(0);
            $table->decimal('port_congestion_risk', 5, 2)->default(0);
            $table->decimal('geopolitical_risk', 5, 2)->default(0);
            $table->decimal('currency_risk', 5, 2)->default(0);
            $table->decimal('inflation_risk', 5, 2)->default(0);

            $table->decimal('total_risk_score', 5, 2)->default(0);
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');

            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index(['shipment_id', 'calculated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};