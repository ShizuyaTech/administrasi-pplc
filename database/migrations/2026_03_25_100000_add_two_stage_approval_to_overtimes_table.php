<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Supervisor approval fields
            $table->foreignId('supervisor_id')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            $table->timestamp('supervisor_approved_at')->nullable()->after('supervisor_id');
            $table->text('supervisor_rejection_reason')->nullable()->after('supervisor_approved_at');
            
            // Manager approval fields
            $table->foreignId('manager_id')->nullable()->after('supervisor_rejection_reason')->constrained('users')->onDelete('set null');
            $table->timestamp('manager_approved_at')->nullable()->after('manager_id');
            $table->text('manager_rejection_reason')->nullable()->after('manager_approved_at');
        });
        
        // Update existing status enum to include new statuses
        DB::statement("ALTER TABLE overtimes MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'supervisor_approved', 'fully_approved', 'rejected_by_supervisor', 'rejected_by_manager') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
            $table->dropColumn('supervisor_approved_at');
            $table->dropColumn('supervisor_rejection_reason');
            
            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
            $table->dropColumn('manager_approved_at');
            $table->dropColumn('manager_rejection_reason');
        });
        
        // Revert status enum back to original
        DB::statement("ALTER TABLE overtimes MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
