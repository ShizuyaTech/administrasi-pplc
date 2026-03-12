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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique(); // Nomor kartu (e.g., 1234-5678-9012)
            $table->enum('card_type', ['flazz', 'brizzi', 'e-toll', 'other'])->default('flazz'); // Tipe kartu
            $table->decimal('current_balance', 15, 2)->default(0); // Saldo saat ini
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status kartu
            $table->foreignId('section_id')->constrained()->onDelete('cascade'); // Kartu per seksi
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
