<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Http\Request;

class AdminCurriculumController extends Controller
{
    public function index()
    {
        $curricula = Curriculum::with(['schoolYear', 'curriculumSubjects.subject'])
            ->latest()
            ->get();

        return view('admin.curricula', [
            'curricula' => $curricula,
            'schoolYears' => SchoolYear::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.curricula-create', [
            'schoolYears' => SchoolYear::orderBy('name')->get(),
            'courses' => \App\Http\Controllers\Auth\RegisterController::COURSES,
            'yearLevels' => \App\Http\Controllers\Auth\RegisterController::YEAR_LEVELS,
            'subjects' => Subject::where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateCurriculum($request);

        $curriculum = Curriculum::create($data);

        $this->syncSubjects($curriculum, $request->input('subjects', []));

        return redirect()->route('admin.curricula')->with('success', 'Curriculum created.');
    }

    public function edit(Curriculum $curriculum)
    {
        $curriculum->load('curriculumSubjects');

        return view('admin.curricula-edit', [
            'curriculum' => $curriculum,
            'schoolYears' => SchoolYear::orderBy('name')->get(),
            'courses' => \App\Http\Controllers\Auth\RegisterController::COURSES,
            'yearLevels' => \App\Http\Controllers\Auth\RegisterController::YEAR_LEVELS,
            'subjects' => Subject::where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $data = $this->validateCurriculum($request, $curriculum->id);
        $curriculum->update($data);

        $this->syncSubjects($curriculum, $request->input('subjects', []));

        return redirect()->route('admin.curricula')->with('success', 'Curriculum updated.');
    }

    public function destroy(Curriculum $curriculum)
    {
        $curriculum->delete();
        return redirect()->route('admin.curricula')->with('success', 'Curriculum deleted.');
    }

    /**
     * Replace the set of subjects on the curriculum with whatever the
     * admin submitted. The form keys rows by subject id, and a row
     * represents a selected subject when its `selected` checkbox is
     * checked. Each selected row may carry a year-level override.
     */
    protected function syncSubjects(Curriculum $curriculum, array $rows): void
    {
        $submitted = [];
        foreach ($rows as $subjectId => $row) {
            if (! is_array($row) || empty($row['selected'])) {
                continue;
            }
            $submitted[(int) $subjectId] = $row['year_level'] ?? null;
        }
        $existing = $curriculum->curriculumSubjects()->get()->keyBy('subject_id');

        foreach ($existing as $subjectId => $cs) {
            if (! array_key_exists($subjectId, $submitted)) {
                $cs->delete();
            }
        }

        foreach ($submitted as $subjectId => $yearLevel) {
            $cs = $existing->get($subjectId);
            $payload = ['year_level' => $yearLevel ?: null];
            if ($cs) {
                $cs->update($payload);
            } else {
                $curriculum->curriculumSubjects()->create([
                    'subject_id' => $subjectId,
                    'year_level' => $payload['year_level'],
                ]);
            }
        }
    }

    protected function validateCurriculum(Request $request, ?int $ignoreId = null): array
    {
        $courses = \App\Http\Controllers\Auth\RegisterController::COURSES;
        $yearLevels = \App\Http\Controllers\Auth\RegisterController::YEAR_LEVELS;

        return $request->validate([
            'course' => ['required', 'in:' . implode(',', $courses)],
            'year_level' => ['required', 'in:' . implode(',', $yearLevels)],
            'semester' => ['required', 'in:First Semester,Second Semester,Summer'],
            'school_year_id' => ['nullable', 'exists:school_years,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'course.in' => 'Please choose a course from the list.',
            'year_level.in' => 'Please select a valid year level.',
        ]) + ['is_active' => $request->boolean('is_active', true)];
    }
}
