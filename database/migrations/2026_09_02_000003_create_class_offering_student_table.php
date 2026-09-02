<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_offering_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();
            // The admin/registrar who explicitly assigned this student. Null for
            // regular students whose enrollment came from the auto-sync flow.
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['teacher_subject_id', 'student_id'], 'cos_class_student_unique');
            $table->index(['student_id', 'school_year_id'], 'cos_student_sy_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_offering_student');
    }
};
