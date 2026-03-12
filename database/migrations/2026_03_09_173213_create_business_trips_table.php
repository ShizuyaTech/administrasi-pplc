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
        Schema::create('business_trips', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number')->unique();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('destination');
            $table->date('departure_date');
            $table->date('return_date');
            $table->text('purpose');
            $table->string('transport');
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->enum('status', ['draft', 'approved', 'completed', 'cancelled'])->default('draft');
            $table->string('attachment')->nullable()->comment('File path');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['section_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_trips');
    }
};
