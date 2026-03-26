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
        Schema::table('employees', function (Blueprint $table) {
            // Drop existing foreign key first, then re-add as nullable
            $table->dropForeign(['role_id']);
            $table->unsignedBigInteger('role_id')->nullable()->change();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
        });
    }
};
