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
        'firstname',
        'lastname',
        'email',
        'phone',
        'student_id',
        'course',
        'year_level',
        'block',
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

    /**
     * Convenience accessor: the user's full name built from the separate
     * firstname and lastname columns. Used by views, sorts, and any
     * remaining `$user->name` reads.
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->firstname ?? '') . ' ' . ($this->lastname ?? ''));
    }

    /**
     * Legacy `name` accessor — returns the same value as full_name. Kept so
     * any code path that still reads `$user->name` does not break.
     */
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    /**
     * Name formatted as "Lastname, Firstname" for use in lists where the
     * surname is the primary sort key (admin/teacher student tables, etc.).
     */
    public function getLastNameFirstAttribute(): string
    {
        $firstname = trim((string) ($this->firstname ?? ''));
        $lastname = trim((string) ($this->lastname ?? ''));

        if ($firstname === '' && $lastname === '') {
            return '';
        }

        if ($lastname === '') {
            return $firstname;
        }

        if ($firstname === '') {
            return $lastname;
        }

        return $lastname . ', ' . $firstname;
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
