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
    ];

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

    public function grades()
    {
        return $this->hasManyThrough(
            Grade::class,
            Enrollment::class,
            'section_id',
            'enrollment_id',
            'section_id',
            'id'
        )->whereColumn('grades.subject_id', 'teacher_subjects.subject_id');
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
