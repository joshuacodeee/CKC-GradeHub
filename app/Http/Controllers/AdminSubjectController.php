<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class AdminSubjectController extends Controller
{
    public function index()
    {
        return view('admin.subjects', [
            'subjects' => Subject::query()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateSubject($request);

        Subject::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        $subject->load(['teacherAssignments.teacher', 'teacherAssignments.section', 'teacherAssignments.schoolYear']);
        return view('admin.subjects-show', ['subject' => $subject]);
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects-edit', ['subject' => $subject]);
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $this->validateSubject($request, $subject->id);

        $subject->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $subject->update(['is_active' => false]);
        return redirect()->route('admin.subjects')->with('success', 'Subject deactivated successfully.');
    }

    public function activate(Subject $subject)
    {
        $subject->update(['is_active' => true]);
        return redirect()->route('admin.subjects')->with('success', 'Subject activated successfully.');
    }

    protected function validateSubject(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = ['required', 'string', 'max:50'];
        $codeRule[] = 'unique:subjects,code' . ($ignoreId ? ',' . $ignoreId : '');

        return $request->validate([
            'code' => $codeRule,
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'units' => ['required', 'integer', 'min:1', 'max:6'],
        ]);
    }
}
