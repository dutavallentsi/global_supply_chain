<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('port_id')->nullable()->constrained('ports')->nullOnDelete();
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->decimal('temperature_c', 5, 2)->nullable();
            $table->decimal('precipitation_mm', 6, 2)->nullable();
            $table->decimal('wind_speed_kmh', 6, 2)->nullable();
            $table->string('storm_risk_level')->default('low'); // low / medium / high / severe
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_snapshots');
    }
};