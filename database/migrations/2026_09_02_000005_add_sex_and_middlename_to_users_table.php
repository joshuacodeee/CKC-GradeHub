<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Middle name — used on the report card so the printed name reads
            // "Lastname, Firstname Middle". Nullable; students and teachers may
            // not have a recorded middle name yet.
            $table->string('middlename')->nullable()->after('lastname');

            // Sex — captured during registration and when an admin creates a
            // teacher account. Stored as free text (Male/Female) so the
            // application layer controls the allowed values; future-proofs
            // against additional categories without a schema change.
            $table->string('sex', 16)->nullable()->after('is_irregular');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['middlename', 'sex']);
        });
    }
};
