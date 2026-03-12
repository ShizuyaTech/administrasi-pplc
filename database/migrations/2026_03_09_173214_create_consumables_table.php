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
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('name');
            $table->string('unit', 50)->comment('satuan: rim, pasang, roll, kg, dll');
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0)->comment('untuk alert stok menipis');
            $table->timestamps();
            
            $table->index(['section_id', 'current_stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};
