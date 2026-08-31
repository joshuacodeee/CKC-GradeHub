<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->enum('semester', ['First Semester', 'Second Semester', 'Summer'])
                ->default('First Semester')
                ->after('school_year_id');
            $table->enum('status', ['active', 'archived'])
                ->default('active')
                ->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropColumn(['semester', 'status']);
        });
    }
};
