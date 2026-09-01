<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable()->after('name');
            $table->string('lastname')->nullable()->after('firstname');
        });

        // Backfill from the existing `name` column. Split on the first
        // whitespace run so that "Juan Dela Cruz" becomes firstname="Juan",
        // lastname="Dela Cruz". Single-word names put the whole string into
        // firstname and leave lastname empty.
        DB::table('users')->whereNotNull('name')->orderBy('id')->each(function ($user) {
            $parts = preg_split('/\s+/', trim((string) $user->name), 2);
            $firstname = $parts[0] ?? '';
            $lastname = $parts[1] ?? '';
            DB::table('users')->where('id', $user->id)->update([
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        DB::table('users')->orderBy('id')->each(function ($user) {
            $name = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
            DB::table('users')->where('id', $user->id)->update(['name' => $name]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'lastname']);
        });
    }
};
