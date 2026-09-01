<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SchoolYear;
use App\Support\GradingScale;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::latest()->get();

        $settings = [
            'school_name'    => Setting::get('school_name', 'Christ the King College'),
            'school_address' => Setting::get('school_address', 'Gingoog City, Misamis Oriental, Philippines'),
            'school_contact' => Setting::get('school_contact', ''),
            'school_email'   => Setting::get('school_email', 'info@ckcgingoog.edu.ph'),
            'grade_weight_prelim'  => Setting::get('grade_weight_prelim', '30'),
            'grade_weight_midterm' => Setting::get('grade_weight_midterm', '30'),
            'grade_weight_final'   => Setting::get('grade_weight_final', '40'),
            'passing_grade'        => Setting::get('passing_grade', '75'),
        ];

        return view('admin.settings', [
            'schoolYears'  => $schoolYears,
            'settings'     => $settings,
            'gradingScale' => GradingScale::table(),
        ]);
    }

    public function storeSchoolYear(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_years,name'],
            'semester' => ['required', 'in:First Semester,Second Semester'],
        ]);

        if ($validated['name'] === 'current') {
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

    public function updateSchoolInfo(Request $request)
    {
        $data = $request->validate([
            'school_name'    => ['required', 'string', 'max:255'],
            'school_address' => ['nullable', 'string', 'max:500'],
            'school_contact' => ['nullable', 'string', 'max:50'],
            'school_email'   => ['nullable', 'email', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings', ['tab' => 'tab-general'])
            ->with('success', 'School information saved.');
    }

    public function updateGradingScale(Request $request)
    {
        $data = $request->validate([
            'grade_weight_prelim'  => ['required', 'integer', 'min:0', 'max:100'],
            'grade_weight_midterm' => ['required', 'integer', 'min:0', 'max:100'],
            'grade_weight_final'   => ['required', 'integer', 'min:0', 'max:100'],
            'passing_grade'        => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'grade_weight_prelim.required'  => 'Prelim weight is required.',
            'grade_weight_midterm.required' => 'Midterm weight is required.',
            'grade_weight_final.required'   => 'Final weight is required.',
        ]);

        // Weights must total 100.
        $total = (int) $data['grade_weight_prelim']
               + (int) $data['grade_weight_midterm']
               + (int) $data['grade_weight_final'];
        if ($total !== 100) {
            return back()
                ->withErrors(['grade_weight_final' => "Weights must total 100. Currently: {$total}."])
                ->withInput();
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings', ['tab' => 'tab-grading'])
            ->with('success', 'Grading scale saved.');
    }
}
