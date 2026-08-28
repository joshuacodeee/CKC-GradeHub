<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::latest()->get();

        return view('admin.settings', [
            'schoolYears' => $schoolYears,
        ]);
    }

    public function storeSchoolYear(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_years,name'],
            'semester' => ['required', 'in:First Semester,Second Semester'],
        ]);

        if ($validated['name'] === 'current') {
            // Set all to non-current, then set this one
            SchoolYear::where('is_current', true)->update(['is_current' => false]);
            $validated['is_current'] = true;
        }

        SchoolYear::create($validated);

        return redirect()->route('admin.settings')->with('success', 'School year created successfully.');
    }

    public function activateSchoolYear(SchoolYear $schoolYear)
    {
        SchoolYear::where('is_current', true)->update(['is_current' => false]);
        $schoolYear->update(['is_current' => true, 'is_active' => true]);

        return back()->with('success', 'School year activated.');
    }
}
