<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['schoolYear'])
            ->withCount(['enrollments' => function ($q) {
                $q->where('status', 'enrolled');
            }])
            ->latest()
            ->get();

        $schoolYears = SchoolYear::where('is_active', true)->latest()->get();

        return view('admin.sections', [
            'sections' => $sections,
            'schoolYears' => $schoolYears,
        ]);
    }

    public function create()
    {
        $schoolYears = SchoolYear::where('is_active', true)->latest()->get();

        return view('admin.sections-create', [
            'schoolYears' => $schoolYears,
        ]);
    }

    public function edit(Section $section)
    {
        $schoolYears = SchoolYear::where('is_active', true)->latest()->get();

        return view('admin.sections-edit', [
            'section' => $section,
            'schoolYears' => $schoolYears,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'in:BS Elementary Education,BS Secondary Education,BS Business Administration,BS Accountancy,BS Information Technology,BS Social Work,BS Hospitality Management,BS Nursing,BS Criminology'],
            'year_level' => ['required', 'in:1st Year,2nd Year,3rd Year,4th Year,5th Year'],
            'block' => ['nullable', 'string', 'max:50'],
            'school_year_id' => ['nullable', 'exists:school_years,id'],
            'is_active' => ['boolean'],
        ]);

        $section = Section::create($validated);

        // Retroactively enroll any existing student whose profile matches
        // this new section, so the count is correct on first view.
        $this->syncEnrollments($section);

        return redirect()->route('admin.sections')->with('success', 'Section created and students enrolled.');
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'in:BS Elementary Education,BS Secondary Education,BS Business Administration,BS Accountancy,BS Information Technology,BS Social Work,BS Hospitality Management,BS Nursing,BS Criminology'],
            'year_level' => ['required', 'in:1st Year,2nd Year,3rd Year,4th Year,5th Year'],
            'block' => ['nullable', 'string', 'max:50'],
            'school_year_id' => ['nullable', 'exists:school_years,id'],
            'is_active' => ['boolean'],
        ]);

        $section->update($validated);

        // After edits, re-pull matching students so the count is accurate.
        $this->syncEnrollments($section);

        return redirect()->route('admin.sections')->with('success', 'Section updated and students re-synced.');
    }

    /**
     * Manually re-sync the enrollments for one section. Useful for existing
     * sections that were created before this auto-enroll behavior existed.
     */
    public function sync(Section $section)
    {
        $added = $this->syncEnrollments($section);

        return redirect()->route('admin.sections')
            ->with('success', $added === 0
                ? 'Section is already in sync.'
                : "Section synced. {$added} student(s) enrolled.");
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('admin.sections')->with('success', 'Section deleted successfully.');
    }

    /**
     * Enroll every existing student whose profile matches the section's
     * course / year_level / block. Skips students already enrolled. Returns
     * the number of new enrollments created.
     *
     * Block matching is exact: a section with block "B" only matches
     * students with block "B". A section without a block only matches
     * students without a block. This keeps "BSIT 4" and "BSIT 4-B" from
     * sharing the same student pool.
     */
    protected function syncEnrollments(Section $section): int
    {
        $schoolYearId = $section->school_year_id;
        $sectionBlock = $section->block ?: null;

        $query = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->where('course', $section->course)
            ->where('year_level', $section->year_level);

        if ($sectionBlock === null) {
            $query->where(function ($q) {
                $q->whereNull('block')->orWhere('block', '');
            });
        } else {
            $query->where('block', $sectionBlock);
        }

        $candidates = $query->pluck('id');

        if ($candidates->isEmpty()) {
            return 0;
        }

        $alreadyEnrolledIds = Enrollment::where('section_id', $section->id)
            ->whereIn('student_id', $candidates)
            ->pluck('student_id');

        $toEnroll = $candidates->diff($alreadyEnrolledIds);

        foreach ($toEnroll as $studentId) {
            Enrollment::create([
                'student_id' => $studentId,
                'section_id' => $section->id,
                'school_year_id' => $schoolYearId,
                'status' => 'enrolled',
            ]);
        }

        return $toEnroll->count();
    }
}
