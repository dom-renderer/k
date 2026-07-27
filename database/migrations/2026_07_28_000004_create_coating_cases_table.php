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
        Schema::create('coating_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('oa_number')->unique();
            $table->foreignId('sector_id')->constrained('sectors')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->text('other_information')->nullable();
            $table->integer('current_level')->default(1); // 1, 2, 3
            $table->string('status')->default('level_1_pending'); // draft, level_1_pending, level_1_rejected, level_2_pending, level_2_rejected, level_3_pending, level_3_rejected, closed
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coating_cases');
    }
};
