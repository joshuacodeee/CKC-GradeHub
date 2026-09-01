<?php

namespace App\Models;

use App\Support\GradingScale;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'teacher_assignment_id',
        'student_id',
        'enrollment_id',
        'subject_id',
        'teacher_id',
        'prelim',
        'midterm',
        'final',
        'final_grade',
        'remarks',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'prelim' => 'decimal:2',
            'midterm' => 'decimal:2',
            'final' => 'decimal:2',
            'final_grade' => 'decimal:2',
            'remarks' => 'string',
            'status' => 'string',
            'submitted_at' => 'datetime',
        ];
    }

    public function teacherAssignment()
    {
        return $this->belongsTo(TeacherSubject::class, 'teacher_assignment_id');
    }

    public function studentUser()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /** Convenience alias kept for views that call $grade->student. */
    public function student()
    {
        return $this->studentUser();
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Eloquent relationship to the subject. After the migration,
     * `subject_id` is denormalized onto grades for cheap reads, but the
     * canonical source is the linked teacher assignment.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * CKC grade equivalent (1.0–5.0) computed from `final_grade`.
     * Returns null when no final grade has been recorded yet.
     */
    public function getGradeEquivalentAttribute(): ?float
    {
        return GradingScale::equivalent($this->final_grade);
    }
}
