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
        'middlename',
        'lastname',
        'email',
        'phone',
        'student_id',
        'course',
        'year_level',
        'block',
        'is_irregular',
        'sex',
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
            'is_irregular' => 'boolean',
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
     * Scope a query to sort users alphabetically by surname, then given
     * name. Used by every list of users in the app so the order is
     * consistent across admin, teacher, and student views. NULL names
     * sort last so partially populated records don't show up at the top.
     */
    public function scopeAlphabetical($query)
    {
        return $query
            ->orderByRaw('CASE WHEN lastname IS NULL OR lastname = "" THEN 1 ELSE 0 END')
            ->orderBy('lastname')
            ->orderByRaw('CASE WHEN firstname IS NULL OR firstname = "" THEN 1 ELSE 0 END')
            ->orderBy('firstname');
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

    /**
     * Name formatted as "Lastname, Firstname Middle" — used on the
     * student report card. When the middle name is missing the result
     * matches `last_name_first`, so existing records without a middle
     * name still render cleanly.
     */
    public function getLastNameFirstMiddleAttribute(): string
    {
        $base = $this->last_name_first;
        $middle = trim((string) ($this->middlename ?? ''));

        if ($middle === '' || $base === '') {
            return $base;
        }

        return $base . ' ' . $middle;
    }

    /**
     * Short course + year-level string for the report card, e.g.
     * "BSIT-4" for a 4th-year BSIT student, "BSEd-2" for a 2nd-year
     * BSEd student. Falls back to either component when the other is
     * missing so partial data still renders something useful.
     */
    public function getCourseYearLevelAttribute(): string
    {
        $short = $this->short_course_name;
        $yearDigit = $this->yearLevelDigit();

        if ($short === '' || $short === '—') {
            return $yearDigit;
        }
        if ($yearDigit === '') {
            return $short;
        }

        return $short . '-' . $yearDigit;
    }

    /**
     * First digit of the year-level string ("4th Year" → "4"). Empty
     * string when the year level is missing or non-numeric.
     */
    protected function yearLevelDigit(): string
    {
        $year = trim((string) ($this->year_level ?? ''));
        if ($year === '') {
            return '';
        }
        if (preg_match('/\d+/', $year, $m) === 1) {
            return $m[0];
        }
        return '';
    }

    /**
     * Short/display abbreviation for the user's long-form course name
     * (e.g. "BS Information Technology" → "BSIT"). Falls back to the
     * raw value when no abbreviation is mapped, so non-degree course
     * strings still render normally.
     */
    public function getShortCourseNameAttribute(): string
    {
        return Section::shortCourse((string) ($this->course ?? ''));
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

    /**
     * Class offerings this student is explicitly enrolled in. For regular
     * students this mirrors the section's teacher_subjects; for irregular
     * students it can include offerings from any year level or section.
     */
    public function classOfferings()
    {
        return $this->belongsToMany(TeacherSubject::class, 'class_offering_student', 'student_id', 'teacher_subject_id')
            ->withPivot(['school_year_id', 'assigned_by', 'assigned_at'])
            ->withTimestamps();
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
