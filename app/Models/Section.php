<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'course',
        'year_level',
        'block',
        'adviser_id',
        'school_year_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Enrollment::class,
            'section_id',
            'id',
            'id',
            'student_id'
        )->where('role', 'student');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function grades()
    {
        return $this->hasManyThrough(
            Grade::class,
            Enrollment::class,
            'section_id',
            'enrollment_id',
            'id',
            'id'
        );
    }

    public function studentCount()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    /** Display string with block when set, e.g. "BS Information Technology 1-A — Block B". */
    public function displayName(): string
    {
        return $this->name . ($this->block ? ' — Block ' . $this->block : '');
    }
}
