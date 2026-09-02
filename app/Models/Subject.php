<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'units',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'teacher_id')
            ->where('role', 'teacher');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'teacher_subjects', 'subject_id', 'section_id');
    }

    public function curricula()
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_subjects', 'subject_id', 'curriculum_id')
            ->withPivot('year_level')
            ->withTimestamps();
    }
}
