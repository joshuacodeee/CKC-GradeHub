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
}
