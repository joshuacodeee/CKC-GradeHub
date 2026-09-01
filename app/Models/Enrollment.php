<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'section_id',
        'school_year_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Convenience singular alias — there is at most one grade row per
     * (enrollment, teacher_assignment) pair. Callers that want a single
     * grade (typically filtered by assignment id via withConstraint) can
     * use $enrollment->grade. Pair it with a whereHas / with constraint
     * so it doesn't return every grade for the enrollment.
     */
    public function grade()
    {
        return $this->hasOne(Grade::class);
    }
}
