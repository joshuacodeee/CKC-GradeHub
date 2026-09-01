<?php

namespace App\Support;

/**
 * Single source of truth for the CKC grading scale.
 *
 * The official Christ the King College grading equivalent table
 * (1.0 at 95–100, 5.0 at below 60, passing = 75 / 3.0).
 *
 * Used by the Grade model for `grade_equivalent`, by the teacher
 * controller when saving `remarks`, and rendered in the admin
 * settings page.
 */
class GradingScale
{
    /** Lowest passing grade (75% → 3.0). */
    public const PASSING_GRADE = 75.0;

    /**
     * Map from a final numeric grade (0–100) to the CKC grade equivalent.
     *
     * 95–100 → 1.0
     *  94   → 1.1
     *  93   → 1.2
     *  92   → 1.3
     *  91   → 1.4
     *  90   → 1.5
     *  …
     *  75   → 3.0
     * < 75 → 5.0 (Failed)
     *
     * @return float Grade equivalent (1.0–5.0)
     */
    public static function equivalent(float|int|null $percentage): ?float
    {
        if ($percentage === null) {
            return null;
        }

        $pct = (float) $percentage;

        if ($pct < 0) {
            return 5.0;
        }
        if ($pct < self::PASSING_GRADE) {
            return 5.0;
        }
        if ($pct >= 95) {
            return 1.0;
        }

        // For 94 down to 75, the equivalent steps from 1.1 to 3.0 in 0.1 increments.
        // (94 → 1.1, 93 → 1.2, …, 75 → 3.0 → that's 20 steps of 0.1)
        $diff = 94 - (int) round($pct); // how far below 94 we are
        if ($diff < 0) $diff = 0;
        $equivalent = 1.1 + ($diff * 0.1);
        // Clamp at 3.0 (passing floor).
        return min($equivalent, 3.0);
    }

    /**
     * "Passed" / "Failed" remark for a numeric grade.
     */
    public static function remark(float|int|null $percentage): ?string
    {
        if ($percentage === null) {
            return null;
        }
        return (float) $percentage >= self::PASSING_GRADE ? 'Passed' : 'Failed';
    }

    /**
     * Full scale as a table for display.
     * Each row: [min%, max%, equivalent, status].
     *
     * @return array<int, array{percentage: string, equivalent: string, status: string, tone: string}>
     */
    public static function table(): array
    {
        $rows = [];

        // 95-100
        $rows[] = [
            'percentage' => '95–100%',
            'equivalent' => '1.0',
            'status'     => 'Passed',
            'tone'       => 'success',
        ];

        // 94 down to 75 (1.1 → 3.0 in 0.1 increments)
        $equivalent = 1.1;
        for ($pct = 94; $pct >= 75; $pct--) {
            $rows[] = [
                'percentage' => $pct . '%',
                'equivalent' => number_format($equivalent, 1),
                'status'     => 'Passed',
                'tone'       => $pct >= 90 ? 'success' : ($pct >= 80 ? 'info' : 'warning'),
            ];
            $equivalent += 0.1;
        }

        // Below 75
        $rows[] = [
            'percentage' => 'Below 75%',
            'equivalent' => '5.0',
            'status'     => 'Failed',
            'tone'       => 'danger',
        ];

        return $rows;
    }
}
