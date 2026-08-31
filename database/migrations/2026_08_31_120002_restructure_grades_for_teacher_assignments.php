<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add the new columns. Keep legacy columns nullable for back-compat
        //    with anything that still reads them during the rollout.
        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedBigInteger('teacher_assignment_id')->nullable()->after('id');
            $table->unsignedBigInteger('student_id')->nullable()->after('teacher_assignment_id');
        });

        // 2) Backfill: derive student_id from the linked enrollment, and
        //    teacher_assignment_id from the teacher+subject+section of the
        //    enrollment (best-effort match against teacher_subjects).
        if (Schema::hasTable('enrollments') && Schema::hasTable('teacher_subjects')) {
            DB::statement("
                UPDATE grades g
                JOIN enrollments e ON e.id = g.enrollment_id
                SET g.student_id = e.student_id
                WHERE g.student_id IS NULL
            ");

            DB::statement("
                UPDATE grades g
                JOIN enrollments e ON e.id = g.enrollment_id
                JOIN teacher_subjects ts
                    ON ts.teacher_id = g.teacher_id
                   AND ts.subject_id = g.subject_id
                   AND ts.section_id = e.section_id
                SET g.teacher_assignment_id = ts.id
                WHERE g.teacher_assignment_id IS NULL
            ");
        }

        // 3) Drop the legacy unique index. The enrollment_id FK depends on
        //    it, so drop the FK first, then the index, then re-add a new
        //    FK on the new column if we want strict referential integrity.
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
        });
        Schema::table('grades', function (Blueprint $table) {
            $table->dropUnique(['enrollment_id', 'subject_id']);
        });

        // 4) Add the new unique key and foreign keys.
        Schema::table('grades', function (Blueprint $table) {
            $table->foreign('teacher_assignment_id')
                ->references('id')->on('teacher_subjects')
                ->nullOnDelete();
            $table->foreign('student_id')
                ->references('id')->on('users')
                ->nullOnDelete();
            $table->unique(['teacher_assignment_id', 'student_id'], 'grades_teacher_assignment_student_unique');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropUnique('grades_teacher_assignment_student_unique');
            $table->dropForeign(['teacher_assignment_id']);
            $table->dropForeign(['student_id']);
            $table->dropColumn(['teacher_assignment_id', 'student_id']);
            $table->unique(['enrollment_id', 'subject_id']);
            // Note: the enrollment_id FK is not re-added here because the
            // original migration created it; running down() on a fresh DB
            // will require re-creating the FK manually.
        });
    }
};
