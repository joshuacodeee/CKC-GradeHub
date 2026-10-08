<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Earlier Schema::table() modifications on several application tables
     * silently dropped the AUTO_INCREMENT attribute on the `id` primary key.
     * That made every INSERT into those tables fail with MySQL error 1364
     * ("Field 'id' doesn't have a default value"). The previous migration
     * already restored `users.id`; this one repairs the remaining tables.
     *
     * Affected tables: sections, enrollments, failed_jobs, grades, jobs,
     * notification_reads, school_years, settings, subjects, teacher_subjects.
     */
    public function up(): void
    {
        $tables = [
            'sections',
            'enrollments',
            'failed_jobs',
            'grades',
            'jobs',
            'notification_reads',
            'school_years',
            'settings',
            'subjects',
            'teacher_subjects',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
        }
    }

    public function down(): void
    {
        $tables = [
            'sections',
            'enrollments',
            'failed_jobs',
            'grades',
            'jobs',
            'notification_reads',
            'school_years',
            'settings',
            'subjects',
            'teacher_subjects',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL");
        }
    }
};
