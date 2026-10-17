<?php

namespace App\Services;

use App\Models\ClassOfferingStudent;
use App\Models\Curriculum;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes the per-student enrollment sync. Two layers are kept in step:
 *
 *   1. Section-level enrollment (`enrollments`) — at most one row per
 *      (student, school year). The student belongs to a section.
 *
 *   2. Class-offering enrollment (`class_offering_student`) — one row per
 *      (student, class offering). The student is in a specific teacher
 *      assignment. Regular students get one row per active offering in
 *      their section. Irregular students get only the rows an admin
 *      explicitly assigned.
 *
 * This service is intentionally side-effect safe to call multiple times:
 * it is idempotent. Returns a small summary so callers can show a
 * "synced: added N, removed M" status message.
 */
class EnrollmentSyncService
{
    /**
     * The current school year, resolved once. Caller may override.
     */
    public function currentSchoolYear(): SchoolYear
    {
        return SchoolYear::where('is_current', true)->first()
            ?? SchoolYear::where('is_active', true)->latest()->first()
            ?? SchoolYear::latest()->first();
    }

    /**
     * Run the full sync for a single student.
     *
     * @return array{added_offerings:int, removed_offerings:int, section_synced:bool, semester:string|null}
     */
    public function syncForStudent(User $student, ?SchoolYear $schoolYear = null, ?User $assignedBy = null): array
    {
        $schoolYear = $schoolYear ?: $this->currentSchoolYear();
        $result = [
            'added_offerings' => 0,
            'removed_offerings' => 0,
            'section_synced' => false,
            'semester' => $schoolYear?->semester,
        ];

        if (!$student->is_active || $student->role !== 'student') {
            return $result;
        }

        // 1) Section-level sync: keep the student's section row in step with
        // their course / year / block.
        $result['section_synced'] = $this->syncSectionEnrollment($student, $schoolYear);

        // 2) Class-offering sync: for irregulars this means removing any
        // auto-enrolled rows; for regulars it means ensuring they have a
        // row in every active class offering their section is responsible
        // for. The detailed adding/cleaning happens in the helpers below.
        if ($student->is_irregular) {
            $result['removed_offerings'] = $this->removeAutoEnrollmentsForIrregular($student);
        } else {
            $sync = $this->addOfferingsFromCurriculum($student, $schoolYear, $assignedBy);
            $result['added_offerings'] = $sync['added'];
            $result['removed_offerings'] = $sync['removed'];
        }

        return $result;
    }

    /**
     * Make sure the student has at most one (or zero) `enrollments` row
     * for the given school year, pointing at the section that matches
     * their profile. Returns true if anything changed.
     */
    public function syncSectionEnrollment(User $student, ?SchoolYear $schoolYear): bool
    {
        $currentEnrollment = Enrollment::where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->first();

        $targetSection = $this->findSectionForStudent($student, $schoolYear);

        // Already on the right section — no-op.
        if ($currentEnrollment && $targetSection && $currentEnrollment->section_id === $targetSection->id) {
            return false;
        }

        if ($currentEnrollment && (!$targetSection || $currentEnrollment->section_id !== $targetSection->id)) {
            $currentEnrollment->update(['status' => 'dropped']);
        }

        if ($targetSection) {
            $already = Enrollment::where('student_id', $student->id)
                ->where('section_id', $targetSection->id)
                ->where('status', 'enrolled')
                ->exists();
            if (!$already) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'section_id' => $targetSection->id,
                    'school_year_id' => $schoolYear?->id,
                    'status' => 'enrolled',
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Look up the section whose course / year / block matches the student
     * in the given school year. Returns null when nothing matches.
     */
    public function findSectionForStudent(User $student, ?SchoolYear $schoolYear): ?Section
    {
        $studentBlock = $student->block ?: null;

        return Section::query()
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('course', $student->course)
            ->where('year_level', $student->year_level)
            ->when($studentBlock === null, function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('block')->orWhere('block', '');
                });
            })
            ->when($studentBlock !== null, fn($q) => $q->where('block', $studentBlock))
            ->where('is_active', true)
            ->first();
    }

    /**
     * Sync class-offering memberships for a regular student using the
     * curriculum for their (course, year, semester). When no curriculum
     * is defined we fall back to "every active offering in the student's
     * section" — preserving the legacy behavior.
     */
    public function addOfferingsFromCurriculum(User $student, ?SchoolYear $schoolYear, ?User $assignedBy = null): array
    {
        $added = 0;
        $removed = 0;

        $semester = $schoolYear?->semester;
        $curriculum = $semester
            ? Curriculum::findFor((string) $student->course, (string) $student->year_level, $semester, $schoolYear?->id)
            : null;

        if ($curriculum) {
            $subjectIds = $curriculum->subjects()->pluck('subjects.id')->all();
            $offerings = $this->resolveOfferingsForSubjects($subjectIds, $student, $schoolYear);
        } else {
            // No curriculum — use legacy "everything in the section" rule.
            $section = $this->findSectionForStudent($student, $schoolYear);
            $offerings = $section
                ? TeacherSubject::where('section_id', $section->id)
                    ->where('status', 'active')
                    ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                    ->get()
                : collect();
        }

        $offeringIds = $offerings->pluck('id');

        // Drop auto-enrolled rows that no longer correspond to a current
        // offering (e.g. the curriculum changed). We only drop rows that
        // were *not* explicitly assigned (no `assigned_by` set) so a
        // registrar's hand-placed rows survive a re-sync.
        $stale = ClassOfferingStudent::where('student_id', $student->id)
            ->whereNull('assigned_by')
            ->when($offeringIds->isNotEmpty(), fn($q) => $q->whereNotIn('teacher_subject_id', $offeringIds))
            ->get();

        foreach ($stale as $row) {
            // Don't drop a row that already has a submitted grade — those
            // carry academic history that should not silently disappear.
            $hasSubmittedGrade = Grade::where('teacher_assignment_id', $row->teacher_subject_id)
                ->where('student_id', $student->id)
                ->where('status', 'submitted')
                ->exists();
            if (!$hasSubmittedGrade) {
                $row->delete();
                $removed++;
            }
        }

        // Add the new ones.
        foreach ($offerings as $offering) {
            $exists = ClassOfferingStudent::where('teacher_subject_id', $offering->id)
                ->where('student_id', $student->id)
                ->exists();
            if ($exists) {
                continue;
            }
            ClassOfferingStudent::create([
                'teacher_subject_id' => $offering->id,
                'student_id' => $student->id,
                'school_year_id' => $schoolYear?->id,
                'assigned_by' => $assignedBy?->id,
                'assigned_at' => $assignedBy ? now() : null,
            ]);
            $this->ensureDraftGrade($offering, $student);
            $added++;
        }

        return ['added' => $added, 'removed' => $removed];
    }

    /**
     * When a student is flipped to irregular, remove any auto-enrolled
     * rows they have. Rows that were explicitly assigned (have
     * `assigned_by`) are preserved.
     */
    public function removeAutoEnrollmentsForIrregular(User $student): int
    {
        $rows = ClassOfferingStudent::where('student_id', $student->id)
            ->whereNull('assigned_by')
            ->get();
        $removed = 0;
        foreach ($rows as $row) {
            // Keep rows that already carry a submitted grade.
            $hasSubmittedGrade = Grade::where('teacher_assignment_id', $row->teacher_subject_id)
                ->where('student_id', $student->id)
                ->where('status', 'submitted')
                ->exists();
            if ($hasSubmittedGrade) {
                continue;
            }
            $row->delete();
            $removed++;
        }
        return $removed;
    }

    /**
     * Find one active class offering per subject. Prefer an offering in
     * the student's section; fall back to any active offering for the
     * same course/year/semester. This lets the sync work even before
     * every section has a teacher assigned.
     */
    protected function resolveOfferingsForSubjects(array $subjectIds, User $student, ?SchoolYear $schoolYear)
    {
        if (empty($subjectIds)) {
            return collect();
        }

        $section = $this->findSectionForStudent($student, $schoolYear);
        $semester = $schoolYear?->semester;

        $sectionOfferings = $section
            ? TeacherSubject::whereIn('subject_id', $subjectIds)
                ->where('section_id', $section->id)
                ->where('status', 'active')
                ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                ->when($semester, fn($q) => $q->where('semester', $semester))
                ->get()
            : collect();

        $found = $sectionOfferings->keyBy('subject_id');

        // Backfill with same-course/year offerings for subjects that have
        // no section offering yet.
        $missingSubjectIds = array_diff($subjectIds, $found->keys()->all());
        if (!empty($missingSubjectIds)) {
            $fallback = TeacherSubject::whereIn('subject_id', $missingSubjectIds)
                ->where('status', 'active')
                ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                ->when($semester, fn($q) => $q->where('semester', $semester))
                ->whereHas('section', function ($q) use ($student) {
                    $q->where('course', $student->course)
                        ->where('year_level', $student->year_level)
                        ->where('is_active', true);
                })
                ->get()
                ->groupBy('subject_id')
                ->map(fn($g) => $g->first());
            $found = $found->merge($fallback);
        }

        return $found->values();
    }

    /**
     * Create a draft Grade row for a (class, student) pair that doesn't
     * have one yet. The teacher will fill in the numbers and submit.
     *
     * `grades.enrollment_id` is still NOT NULL in the schema, so we make
     * sure a real Enrollment row exists for the (student, offering's
     * section) pair — creating one on the fly if needed. For irregular
     * students this is the bridge that lets the legacy grade row FK
     * remain satisfied without inventing a fake section membership.
     */
    public function ensureDraftGrade(TeacherSubject $offering, User $student): Grade
    {
        $enrollmentId = $this->resolveEnrollmentId($student, $offering, createIfMissing: true);

        return Grade::firstOrCreate(
            [
                'teacher_assignment_id' => $offering->id,
                'student_id' => $student->id,
            ],
            [
                'enrollment_id' => $enrollmentId,
                'subject_id' => $offering->subject_id,
                'teacher_id' => $offering->teacher_id,
                'status' => 'draft',
            ]
        );
    }

    /**
     * Best-effort link to a section-level enrollment row. If the student
     * has no enrollment in the offering's section, optionally create one
     * to satisfy the grades.enrollment_id FK. Returns null only when the
     * caller opts out and no row exists.
     */
    public function resolveEnrollmentId(User $student, TeacherSubject $offering, bool $createIfMissing = false): ?int
    {
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('section_id', $offering->section_id)
            ->where('status', 'enrolled')
            ->first();
        if ($enrollment) {
            return $enrollment->id;
        }
        if (!$createIfMissing) {
            return null;
        }
        $schoolYear = $offering->school_year_id
            ? SchoolYear::find($offering->school_year_id)
            : $this->currentSchoolYear();
        return Enrollment::create([
            'student_id' => $student->id,
            'section_id' => $offering->section_id,
            'school_year_id' => $schoolYear?->id,
            'status' => 'enrolled',
        ])->id;
    }
}
