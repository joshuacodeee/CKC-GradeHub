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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('prelim', 5, 2)->nullable();
            $table->decimal('midterm', 5, 2)->nullable();
            $table->decimal('final', 5, 2)->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->enum('remarks', ['Passed', 'Failed'])->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamps();

            $table->unique(['enrollment_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};