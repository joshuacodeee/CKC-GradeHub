<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = [
        'name',
        'semester',
        'is_current',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }
}
