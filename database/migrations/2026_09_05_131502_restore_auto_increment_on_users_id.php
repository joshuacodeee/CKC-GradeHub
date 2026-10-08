<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Earlier Schema::table() modifications on the `users` table silently
     * dropped the AUTO_INCREMENT attribute on the `id` primary key, which
     * caused INSERTs to fail with "Field 'id' doesn't have a default value"
     * (MySQL error 1364). This migration restores the AUTO_INCREMENT flag
     * on the existing primary key column without rebuilding the table.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL');
    }
};
