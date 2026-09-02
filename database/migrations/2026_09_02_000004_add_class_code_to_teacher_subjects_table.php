<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            // Human-friendly identifier so registrars and irregular students
            // can refer to a specific class offering without ambiguity.
            // Nullable for legacy rows; backfilled below.
            $table->string('class_code', 64)->nullable()->unique()->after('semester');
        });

        // Backfill: generate a deterministic-ish code for every existing row
        // so the unique index doesn't trip when we later add NOT NULL.
        if (Schema::hasTable('teacher_subjects')) {
            foreach (DB::table('teacher_subjects')->whereNull('class_code')->get() as $row) {
                DB::table('teacher_subjects')
                    ->where('id', $row->id)
                    ->update(['class_code' => self::generateCode((int) $row->id)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropUnique(['class_code']);
            $table->dropColumn('class_code');
        });
    }

    /**
     * Compact, readable code: a short subject+year prefix and a 4-char
     * random tail. Used both by this backfill and by the controller when
     * auto-filling new rows.
     */
    public static function generateCode(int $idHint = 0): string
    {
        $tail = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        $seq = $idHint > 0 ? $idHint : random_int(1000, 9999);
        return sprintf('CLS-%d-%s', $seq, $tail);
    }
};
