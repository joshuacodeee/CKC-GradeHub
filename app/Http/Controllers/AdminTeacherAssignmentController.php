<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherSubject::with(['teacher', 'subject', 'section', 'schoolYear'])
            ->latest();

        if ($tid = $request->query('teacher_id')) {
            $query->where('teacher_id', $tid);
        }
        if ($sid = $request->query('subject_id')) {
            $query->where('subject_id', $sid);
        }
        if ($secid = $request->query('section_id')) {
            $query->where('section_id', $secid);
        }
        if ($syid = $request->query('school_year_id')) {
            $query->where('school_year_id', $syid);
        }
        if ($sem = $request->query('semester')) {
            $query->where('semester', $sem);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.assignments', [
            'assignments' => $query->get(),
            'teachers' => User::where('role', 'teacher')->orderBy('lastname')->orderBy('firstname')->get(),
            'subjects' => Subject::where('is_active', true)->orderBy('code')->get(),
            'sections' => Section::where('is_active', true)->orderBy('name')->get(),
            'schoolYears' => SchoolYear::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.assignments-create', [
            'teachers' => User::where('role', 'teacher')->orderBy('lastname')->orderBy('firstname')->get(),
            'subjects' => Subject::where('is_active', true)->orderBy('code')->get(),
            'sections' => Section::where('is_active', true)->orderBy('name')->get(),
            'schoolYears' => SchoolYear::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAssignment($request);
        $data['status'] = $data['status'] ?? 'active';

        TeacherSubject::create($data);

        return redirect()->route('admin.assignments')->with('success', 'Teacher assigned successfully.');
    }

    public function edit(TeacherSubject $assignment)
    {
        return view('admin.assignments-edit', [
            'assignment' => $assignment,
            'teachers' => User::where('role', 'teacher')->orderBy('lastname')->orderBy('firstname')->get(),
            'subjects' => Subject::where('is_active', true)->orderBy('code')->get(),
            'sections' => Section::where('is_active', true)->orderBy('name')->get(),
            'schoolYears' => SchoolYear::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, TeacherSubject $assignment)
    {
        $validated = $this->validateAssignment($request, $assignment->id);

        $assignment->update($validated);

        return redirect()->route('admin.assignments')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(TeacherSubject $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.assignments')->with('success', 'Assignment removed successfully.');
    }

    protected function validateAssignment(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'school_year_id' => ['required', 'exists:school_years,id'],
            'semester' => ['required', 'in:First Semester,Second Semester,Summer'],
            'status' => ['nullable', 'in:active,archived'],
        ]);

        // Per spec §28: prevent duplicate assignment for the same combination.
        $exists = TeacherSubject::where('teacher_id', $data['teacher_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('section_id', $data['section_id'])
            ->where('school_year_id', $data['school_year_id'])
            ->where('semester', $data['semester'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'teacher_id' => 'This teacher is already assigned to this subject, section, school year, and semester.',
            ]);
        }

        $data['status'] = $data['status'] ?? 'active';
        return $data;
    }
}
