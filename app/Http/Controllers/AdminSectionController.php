<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['adviser', 'schoolYear'])
            ->withCount(['enrollments' => function ($q) {
                $q->where('status', 'enrolled');
            }])
            ->latest()
            ->get();

        $advisers = User::where('role', 'teacher')->latest()->get();
        $schoolYears = SchoolYear::where('is_active', true)->latest()->get();

        return view('admin.sections', [
            'sections' => $sections,
            'advisers' => $advisers,
            'schoolYears' => $schoolYears,
        ]);
    }

    public function create()
    {
        $advisers = User::where('role', 'teacher')->latest()->get();
        $schoolYears = SchoolYear::where('is_active', true)->latest()->get();

        return view('admin.sections-create', [
            'advisers' => $advisers,
            'schoolYears' => $schoolYears,
        ]);
    }

    public function edit(Section $section)
    {
        $advisers = User::where('role', 'teacher')->latest()->get();
        $schoolYears = SchoolYear::where('is_active', true)->latest()->get();

        return view('admin.sections-edit', [
            'section' => $section,
            'advisers' => $advisers,
            'schoolYears' => $schoolYears,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'in:BSIT,BSSW,BSCRIM,BSED,BSA,BSBA,BSN,BSHM'],
            'year_level' => ['required', 'in:1st Year,2nd Year,3rd Year,4th Year'],
            'adviser_id' => ['nullable', 'exists:users,id'],
            'school_year_id' => ['nullable', 'exists:school_years,id'],
            'is_active' => ['boolean'],
        ]);

        Section::create($validated);

        return redirect()->route('admin.sections')->with('success', 'Section created successfully.');
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'in:BSIT,BSSW,BSCRIM,BSED,BSA,BSBA,BSN,BSHM'],
            'year_level' => ['required', 'in:1st Year,2nd Year,3rd Year,4th Year'],
            'adviser_id' => ['nullable', 'exists:users,id'],
            'school_year_id' => ['nullable', 'exists:school_years,id'],
            'is_active' => ['boolean'],
        ]);

        $section->update($validated);

        return redirect()->route('admin.sections')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('admin.sections')->with('success', 'Section deleted successfully.');
    }
}
