<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassOfferingStudent extends Model
{
    /**
     * Pivot table for explicit per-class-offering enrollment. Lives in its own
     * model (rather than as a `belongsToMany` only) so we can attach fields
     * like `assigned_by` and `assigned_at` with proper timestamps and
     * relationships, and so callers can use the rich model API.
     */
    protected $table = 'class_offering_student';

    protected $fillable = [
        'teacher_subject_id',
        'student_id',
        'school_year_id',
        'assigned_by',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
