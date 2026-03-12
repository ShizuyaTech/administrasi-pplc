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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nrp')->unique()->comment('Nomor Registrasi Pegawai');
            $table->string('name');
            $table->foreignId('section_id')->constrained('sections')->onDelete('restrict');
            $table->string('position')->comment('Jabatan');
            $table->enum('shift', ['Shift A', 'Shift B', 'Non Shift'])->default('Non Shift');
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['section_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
