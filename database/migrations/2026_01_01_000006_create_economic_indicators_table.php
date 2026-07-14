<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('economic_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('gdp_usd', 20, 2)->nullable();
            $table->decimal('inflation_rate', 6, 3)->nullable();      // persen
            $table->bigInteger('population')->nullable();
            $table->decimal('exports_value_usd', 20, 2)->nullable();
            $table->decimal('imports_value_usd', 20, 2)->nullable();
            $table->timestamps();

            $table->unique(['country_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('economic_indicators');
    }
};