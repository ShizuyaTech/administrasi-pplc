<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('card_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_trip_id')->constrained()->onDelete('cascade'); // Relasi ke business trip
            $table->foreignId('card_id')->constrained()->onDelete('cascade'); // Kartu yang dipakai
            $table->decimal('initial_balance', 15, 2); // Saldo awal saat trip dimulai
            $table->decimal('usage_amount', 15, 2); // Jumlah pemakaian (tol + parkir)
            $table->decimal('final_balance', 15, 2); // Saldo akhir (auto calculated)
            $table->text('usage_notes')->nullable(); // Catatan pemakaian (rincian tol, parkir, dll)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_usages');
    }
};
