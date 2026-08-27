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
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:subjects,code'],
            'name' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1', 'max:6'],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        Subject::create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()->route('admin.subjects')->with('success', 'Subject created successfully.');
    }
}
