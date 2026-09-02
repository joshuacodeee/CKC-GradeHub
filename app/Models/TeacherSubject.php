<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'section_id',
        'school_year_id',
        'semester',
        'class_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'semester' => 'string',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Friendly display string for the school year + semester pair.
     * Used in tables and dropdowns throughout the app.
     */
    public function schoolYearDisplay(): string
    {
        $year = $this->schoolYear?->name ?? '—';
        $sem = $this->semester ?? '—';
        return "{$year} — {$sem}";
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'teacher_assignment_id');
    }

    /**
     * Backward-compat section-based students. For regular students this
     * mirrors the explicit class_offering_student rows; for irregulars it
     * catches the rest of the section who haven't been promoted to a
     * class offering. Prefer `studentsViaOffering()` when you only need
     * the explicit ones.
     */
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Enrollment::class,
            'section_id',
            'id',
            'section_id',
            'student_id'
        )->where('role', 'student');
    }

    /**
     * The explicit per-class-offering membership rows. These are the rows
     * that drive "which students appear in the teacher's grade sheet" once
     * the new enrollment workflow lands.
     */
    public function offeringStudents()
    {
        return $this->hasMany(ClassOfferingStudent::class);
    }

    /**
     * Students explicitly enrolled in this class offering via the pivot.
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'class_offering_student', 'teacher_subject_id', 'student_id')
            ->withPivot(['school_year_id', 'assigned_by', 'assigned_at'])
            ->withTimestamps();
    }
}
