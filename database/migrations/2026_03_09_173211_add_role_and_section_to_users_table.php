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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('email')->constrained('roles')->onDelete('set null');
            $table->foreignId('section_id')->nullable()->after('role_id')->constrained('sections')->onDelete('set null');
            $table->tinyInteger('shift')->nullable()->after('section_id')->comment('1 or 2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['role_id', 'section_id', 'shift']);
        });
    }
};
