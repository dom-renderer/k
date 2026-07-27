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
        Schema::create('coating_case_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coating_case_id')->constrained('coating_cases')->cascadeOnDelete();
            $table->integer('level')->default(1); // 1, 2, 3
            $table->string('file_category')->default('photo'); // photo, document, pre_coating, after_coating, other
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('file_type')->nullable();
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
        Schema::dropIfExists('coating_case_files');
    }
};
