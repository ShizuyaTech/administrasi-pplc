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
        Schema::create('break_times', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama jam istirahat, misal: Istirahat Siang');
            $table->time('start_time')->comment('Jam mulai istirahat');
            $table->time('end_time')->comment('Jam selesai istirahat');
            $table->boolean('is_active')->default(true)->comment('Status aktif/non-aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('break_times');
    }
};
