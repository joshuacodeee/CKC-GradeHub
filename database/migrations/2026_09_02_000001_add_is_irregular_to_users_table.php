<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: this column was added directly to the users table in a prior
        // failed run (the migrator crashed before the row was recorded). Re-running
        // must be safe.
        if (! Schema::hasColumn('users', 'is_irregular')) {
            Schema::table('users', function (Blueprint $table) {
                // Irregular students are not auto-enrolled into every class offering
                // of their section. The admin/registrar assigns them to specific class
                // offerings via the manage-enrollment page. Default false keeps every
                // existing account behaving as a regular student.
                $table->boolean('is_irregular')->default(false)->after('block');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_irregular')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_irregular');
            });
        }
    }
};
