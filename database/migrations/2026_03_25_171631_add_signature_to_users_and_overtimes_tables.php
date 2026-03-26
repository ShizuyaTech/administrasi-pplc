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
        // Add signature paths to overtimes table (signature_path already exists in users table)
        Schema::table('overtimes', function (Blueprint $table) {
            $table->string('supervisor_signature_path')->nullable()->after('manager_approved_at');
            $table->string('manager_signature_path')->nullable()->after('supervisor_signature_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropColumn(['supervisor_signature_path', 'manager_signature_path']);
        });
    }
};
