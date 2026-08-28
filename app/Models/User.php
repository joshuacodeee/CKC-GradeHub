<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'student_id',
        'course',
        'year_level',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include users of a given role.
     */
    public function scopeOfRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // ------------------------------------------------------------------
    // Student relationships
    // ------------------------------------------------------------------

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function grades()
    {
        return $this->hasManyThrough(Grade::class, Enrollment::class, 'student_id', 'enrollment_id', 'id', 'id');
    }

    public function section()
    {
        return $this->hasOneThrough(
            Section::class,
            Enrollment::class,
            'student_id',
            'id',
            'id',
            'section_id'
        );
    }

    // ------------------------------------------------------------------
    // Teacher relationships
    // ------------------------------------------------------------------

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherSubject::class, 'teacher_id');
    }

    public function taughtSubjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'teacher_id', 'subject_id');
    }

    public function assignedSections()
    {
        return $this->belongsToMany(Section::class, 'teacher_subjects', 'teacher_id', 'section_id');
    }

    // ------------------------------------------------------------------
    // Adviser relationship
    // ------------------------------------------------------------------

    public function advisedSections()
    {
        return $this->hasMany(Section::class, 'adviser_id');
    }
}
