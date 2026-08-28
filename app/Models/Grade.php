<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'teacher_id',
        'prelim',
        'midterm',
        'final',
        'final_grade',
        'remarks',
        'status',
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
        ];
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student()
    {
        return $this->hasOneThrough(
            User::class,
            Enrollment::class,
            'id',
            'id',
            'enrollment_id',
            'student_id'
        );
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
