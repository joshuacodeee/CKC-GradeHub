@extends('layouts.app', ['role' => 'admin', 'active' => 'settings', 'title' => 'Settings'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6" data-tabs>

  {{-- Tab nav --}}
  <div class="lg:col-span-1">
    <div class="sg-card p-2 flex lg:flex-col gap-1 overflow-x-auto">
      <button data-tab-target="#tab-school-years" class="sg-tab-active sg-btn !justify-start w-full !bg-transparent" style="color:var(--sg-text)"><i data-lucide="calendar-range" class="w-4 h-4"></i> School Years</button>
      <button data-tab-target="#tab-grading" class="sg-btn !justify-start w-full !bg-transparent" style="color:var(--sg-text)"><i data-lucide="percent" class="w-4 h-4"></i> Grading Scale</button>
      <button data-tab-target="#tab-general" class="sg-btn !justify-start w-full !bg-transparent" style="color:var(--sg-text)"><i data-lucide="school" class="w-4 h-4"></i> School Info</button>
    </div>
  </div>

  {{-- Panels --}}
  <div class="lg:col-span-3 space-y-5">

    <div data-tab-panel id="tab-school-years">
      <div class="sg-card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-display font-semibold" style="color:var(--sg-text)">School Years & Semesters</h3>
          <button class="sg-btn sg-btn-primary" data-toast="New school year form would open here (demo only)."><i data-lucide="plus" class="w-4 h-4"></i> Add School Year</button>
        </div>
        <div class="overflow-x-auto">
          <table class="sg-table">
            <thead><tr><th>School Year</th><th>Semester</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
              <tr>
                <td style="color:var(--sg-text)">2026–2027</td><td style="color:var(--sg-text-muted)">First Semester</td>
                <td><span class="sg-badge sg-badge-success">Current</span></td>
                <td class="text-right"><button class="sg-btn sg-btn-ghost !p-2" data-toast="Editing 2026–2027 First Semester (demo only)." data-toast-type="info"><i data-lucide="pencil" class="w-4 h-4"></i></button></td>
              </tr>
              <tr>
                <td style="color:var(--sg-text)">2025–2026</td><td style="color:var(--sg-text-muted)">Second Semester</td>
                <td><span class="sg-badge sg-badge-info">Archived</span></td>
                <td class="text-right"><button class="sg-btn sg-btn-ghost !p-2" data-toast="Editing 2025–2026 Second Semester (demo only)." data-toast-type="info"><i data-lucide="pencil" class="w-4 h-4"></i></button></td>
              </tr>
              <tr>
                <td style="color:var(--sg-text)">2025–2026</td><td style="color:var(--sg-text-muted)">First Semester</td>
                <td><span class="sg-badge sg-badge-info">Archived</span></td>
                <td class="text-right"><button class="sg-btn sg-btn-ghost !p-2" data-toast="Editing 2025–2026 First Semester (demo only)." data-toast-type="info"><i data-lucide="pencil" class="w-4 h-4"></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div data-tab-panel id="tab-grading" class="hidden">
      <div class="sg-card p-5">
        <h3 class="font-display font-semibold mb-4" style="color:var(--sg-text)">Grade Weighting</h3>
        <p class="text-sm mb-4" style="color:var(--sg-text-muted)">Applied automatically when teachers enter Prelim, Midterm, and Final grades.</p>
        <div class="grid grid-cols-3 gap-4 mb-4">
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Prelim</label><div class="relative"><input type="number" value="30" class="sg-input pr-8"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--sg-text-muted)">%</span></div></div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Midterm</label><div class="relative"><input type="number" value="30" class="sg-input pr-8"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--sg-text-muted)">%</span></div></div>
          <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Final</label><div class="relative"><input type="number" value="40" class="sg-input pr-8"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm" style="color:var(--sg-text-muted)">%</span></div></div>
        </div>
        <div class="flex items-center gap-3 mb-4">
          <label class="text-sm font-medium" style="color:var(--sg-text)">Passing grade</label>
          <input type="number" value="75" class="sg-input !w-24">
        </div>
        <button class="sg-btn sg-btn-primary" data-toast="Grading scale saved successfully."><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
      </div>
    </div>

    <div data-tab-panel id="tab-general" class="hidden">
      <div class="sg-card p-5 space-y-4">
        <h3 class="font-display font-semibold" style="color:var(--sg-text)">School Information</h3>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Name</label><input type="text" value="Northbridge Institute of Technology" class="sg-input"></div>
        <div><label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Address</label><input type="text" value="123 Rizal Avenue, Imus, Cavite, Philippines" class="sg-input"></div>
        <button class="sg-btn sg-btn-primary" data-toast="School information updated successfully."><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
      </div>
    </div>
  </div>
</div>
@endsection
