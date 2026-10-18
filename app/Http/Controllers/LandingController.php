<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\User;
use App\Support\GradingScale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index()
    {
        $schoolYear = $this->resolveSchoolYear();

        // Student count: number of active students on the platform.
        $studentCount = Schema::hasTable('users')
            ? User::query()->where('role', 'student')->count()
            : 0;

        // Grade snapshot: pull the most recent graded subjects for the current school year
        // (or all years if no school year is set). Each subject appears once with the
        // most-recent final grade recorded, with the corresponding teacher, subject,
        // and student computed. We cap at 5 rows for the visible card.
        $snapshot = Schema::hasTable('grades') ? $this->buildGradeSnapshot($schoolYear, 5) : collect();

        // Aggregate stats for the floating chips.
        $gradesThisWeek = Schema::hasTable('grades')
            ? Grade::query()
                ->whereNotNull('submitted_at')
                ->where('submitted_at', '>=', now()->subWeek())
                ->count()
            : 0;

        $weightedAverage = Schema::hasTable('grades') ? $this->weightedAverage($schoolYear) : null;

        return view('landing', [
            'schoolYear'         => $schoolYear,
            'studentCount'       => $studentCount,
            'snapshot'           => $snapshot,
            'snapshotAverage'    => $weightedAverage,
            'gradesThisWeek'     => $gradesThisWeek,
            'gradingScale'       => GradingScale::table(),
            'passingGrade'       => GradingScale::PASSING_GRADE,
        ]);
    }

    private function resolveSchoolYear(): ?SchoolYear
    {
        if (! Schema::hasTable('school_years')) {
            return null;
        }

        return SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first()
            ?? SchoolYear::latest()->first();
    }

    /**
     * Build a list of recent graded subjects for the landing page term snapshot.
     * Each entry contains the subject code, subject name, final grade, and a
     * standing descriptor.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function buildGradeSnapshot(?SchoolYear $schoolYear, int $limit): Collection
    {
        $grades = Grade::query()
            ->with(['subject', 'teacher'])
            ->whereNotNull('final_grade')
            ->when($schoolYear, function ($q) use ($schoolYear) {
                $q->whereHas('teacherAssignment', fn ($sub) => $sub->where('school_year_id', $schoolYear->id));
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('submitted_at')
            ->get();

        // Reduce to one row per subject: keep the most recent final grade per code.
        $bySubject = $grades->groupBy(fn (Grade $g) => $g->subject?->code ?? '—')
            ->map(function (Collection $rows) {
                /** @var Grade $latest */
                $latest = $rows->first();
                $grade = (float) $latest->final_grade;
                $equivalent = GradingScale::equivalent($grade);
                return [
                    'code'       => $latest->subject?->code ?? '—',
                    'name'       => $latest->subject?->name ?? 'Subject',
                    'grade'      => $grade,
                    'equivalent' => $equivalent,
                    'standing'   => $this->standingFor($grade),
                    'tone'       => $this->toneFor($grade),
                ];
            })
            ->sortByDesc('grade')
            ->values()
            ->take($limit);

        return $bySubject;
    }

    private function standingFor(float $grade): string
    {
        return match (true) {
            $grade >= 95 => 'Highest honors standing',
            $grade >= 90 => 'Excellent standing',
            $grade >= 85 => 'Very Good standing',
            $grade >= 80 => 'Good standing',
            $grade >= 75 => 'Passing',
            default      => 'Needs improvement',
        };
    }

    private function toneFor(float $grade): string
    {
        return match (true) {
            $grade >= 90 => 'success',
            $grade >= 85 => 'info',
            $grade >= 75 => 'warning',
            default      => 'danger',
        };
    }

    private function weightedAverage(?SchoolYear $schoolYear): ?float
    {
        $avg = Grade::query()
            ->whereNotNull('final_grade')
            ->when($schoolYear, function ($q) use ($schoolYear) {
                $q->whereHas('teacherAssignment', fn ($sub) => $sub->where('school_year_id', $schoolYear->id));
            })
            ->avg('final_grade');

        return $avg === null ? null : round((float) $avg, 1);
    }
}
