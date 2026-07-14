<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cca2', 2)->unique();   // ISO 3166-1 alpha-2 (dipakai RestCountries & ExchangeRate)
            $table->string('cca3', 3)->nullable();  // ISO 3166-1 alpha-3 (dipakai World Bank)
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->string('capital')->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->string('currency_name')->nullable();
            $table->json('languages')->nullable();
            $table->string('flag_url')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};