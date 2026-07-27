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
        Schema::create('coating_case_level_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coating_case_id')->constrained('coating_cases')->cascadeOnDelete();
            $table->integer('level'); // 1, 2, 3
            $table->string('action'); // submitted, approved, rejected
            $table->integer('reset_to_level')->nullable(); // 1, 2, 3 if rejected
            $table->text('remarks')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coating_case_level_logs');
    }
};
