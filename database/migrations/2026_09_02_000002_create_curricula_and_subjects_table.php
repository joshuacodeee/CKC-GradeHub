<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->string('course');
            $table->string('year_level');
            $table->enum('semester', ['First Semester', 'Second Semester', 'Summer']);
            // Null school year = applies to all years. When set, this curriculum
            // takes precedence over the year-agnostic one for that year.
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // school_year_id is part of the uniqueness key but we want at most one
            // null per (course, year, semester) — handled at the application level
            // since MySQL treats NULLs as distinct in unique indexes.
            $table->unique(['course', 'year_level', 'semester', 'school_year_id'], 'curricula_unique');
        });

        Schema::create('curriculum_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            // Optional override: when a subject within a curriculum belongs to a
            // different year level than the curriculum's own (e.g. an irregular
            // back-subject). Null = applies to the curriculum's year level.
            $table->string('year_level')->nullable();
            $table->timestamps();

            $table->unique(['curriculum_id', 'subject_id', 'year_level'], 'curriculum_subjects_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subjects');
        Schema::dropIfExists('curricula');
    }
};
