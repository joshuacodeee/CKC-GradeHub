<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SchoolYear;
use App\Support\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            'signature_path'       => Setting::get('registrar_signature_path'),
            'signatory_name'       => Setting::get('registrar_signatory_name', 'College Registrar'),
            'signatory_position'   => Setting::get('registrar_signatory_position', 'College Registrar'),
        ];

        return view('admin.settings', [
            'schoolYears'  => $schoolYears,
            'settings'     => $settings,
            'gradingScale' => GradingScale::table(),
            'role'         => auth()->user()->role,
            'signatureUrl' => $settings['signature_path']
                ? route('registrar-signature')
                : null,
            'signatureUpdatedAt' => Setting::get('registrar_signature_updated_at'),
            'signatureUpdatedBy' => Setting::get('registrar_signature_updated_by')
                ? \App\Models\User::find(Setting::get('registrar_signature_updated_by'))
                : null,
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

    public function updateSignature(Request $request)
    {
        $routePrefix = $request->user()->role;
        $validator = Validator::make($request->all(), [
            'signature' => ['required', 'file', 'mimes:png', 'max:5120'],
            'signatory_name' => ['nullable', 'string', 'max:255'],
            'signatory_position' => ['nullable', 'string', 'max:255'],
        ], [
            'signature.mimes' => 'Upload the processed PNG signature image.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route($routePrefix . '.settings', ['tab' => 'tab-signature'])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $newPath = $validated['signature']->store('signatures', 'local');
        if ($newPath === false) {
            throw new \RuntimeException('Unable to store the registrar signature on the local disk.');
        }
        $oldPath = Setting::get('registrar_signature_path');

        DB::transaction(function () use ($validated, $newPath, $request) {
            Setting::set('registrar_signature_path', $newPath);
            Setting::set('registrar_signatory_name', ($validated['signatory_name'] ?? null) ?: 'College Registrar');
            Setting::set('registrar_signatory_position', ($validated['signatory_position'] ?? null) ?: 'College Registrar');
            Setting::set('registrar_signature_updated_by', $request->user()->id);
            Setting::set('registrar_signature_updated_at', now()->toDateTimeString());
        });

        if ($oldPath && $oldPath !== $newPath) {
            if (Storage::disk('local')->exists($oldPath) && ! Storage::disk('local')->delete($oldPath)) {
                throw new \RuntimeException('The new signature was saved, but the previous signature file could not be removed.');
            }
        }

        return redirect()
            ->route($routePrefix . '.settings', ['tab' => 'tab-signature'])
            ->with('success', 'Registrar signature saved.');
    }

    public function removeSignature(Request $request)
    {
        $routePrefix = $request->user()->role;
        $oldPath = Setting::get('registrar_signature_path');

        DB::transaction(function () use ($request) {
            Setting::set('registrar_signature_path', null);
            Setting::set('registrar_signature_updated_by', $request->user()->id);
            Setting::set('registrar_signature_updated_at', now()->toDateTimeString());
        });

        if ($oldPath && Storage::disk('local')->exists($oldPath) && ! Storage::disk('local')->delete($oldPath)) {
            throw new \RuntimeException('The signature was removed from settings, but its stored file could not be deleted.');
        }

        return redirect()
            ->route($routePrefix . '.settings', ['tab' => 'tab-signature'])
            ->with('success', 'Registrar signature removed.');
    }

    public function signatureImage()
    {
        $path = Setting::get('registrar_signature_path');

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response(
            $path,
            'registrar-signature.png',
            ['Cache-Control' => 'private, no-store'],
        );
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
