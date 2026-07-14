<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // kode tracking internal, misal SHP-2026-0001
            $table->string('product_name');
            $table->integer('quantity')->default(1);

            $table->foreignId('origin_country_id')->constrained('countries');
            $table->foreignId('destination_country_id')->constrained('countries');
            $table->foreignId('origin_port_id')->nullable()->constrained('ports');
            $table->foreignId('destination_port_id')->nullable()->constrained('ports');

            $table->string('transaction_currency', 3); // mata uang kontrak, misal USD
            $table->decimal('amount', 18, 2);           // nilai barang dalam transaction_currency

            $table->date('departure_date');
            $table->date('estimated_arrival_date');
            $table->date('actual_arrival_date')->nullable();

            $table->enum('status', ['pending', 'in_transit', 'delayed', 'arrived', 'cancelled'])
                  ->default('pending');

            $table->decimal('current_latitude', 10, 6)->nullable();
            $table->decimal('current_longitude', 10, 6)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};