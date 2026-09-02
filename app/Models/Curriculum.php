<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $fillable = [
        'course',
        'year_level',
        'semester',
        'school_year_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'semester' => 'string',
        ];
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function curriculumSubjects()
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    /**
     * The set of subjects the curriculum prescribes for its (course, year,
     * semester). Through the curriculum_subjects pivot so we can carry
     * per-subject year overrides when needed.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subjects', 'curriculum_id', 'subject_id')
            ->withPivot('year_level')
            ->withTimestamps();
    }

    /**
     * Find the active curriculum for a (course, year, semester) pair. If
     * multiple match (e.g. a school-year-scoped one and a year-agnostic
     * one), prefer the school-year-scoped entry.
     */
    public static function findFor(string $course, string $yearLevel, string $semester, ?int $schoolYearId = null): ?self
    {
        $query = self::query()
            ->where('course', $course)
            ->where('year_level', $yearLevel)
            ->where('semester', $semester)
            ->where('is_active', true);

        if ($schoolYearId !== null) {
            return $query
                ->where(function ($q) use ($schoolYearId) {
                    $q->where('school_year_id', $schoolYearId)
                        ->orWhereNull('school_year_id');
                })
                ->orderByRaw('school_year_id IS NULL') // non-null first
                ->first();
        }

        return $query->whereNull('school_year_id')->first();
    }
}
