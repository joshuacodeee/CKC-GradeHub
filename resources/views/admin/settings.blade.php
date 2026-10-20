@extends('layouts.app', ['role' => $role ?? 'admin', 'active' => 'settings', 'title' => 'Settings'])

@section('content')
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" data-tabs>

    {{-- Tab nav --}}
    <div class="lg:col-span-1">
      <div class="sg-card p-2 flex lg:flex-col gap-1 overflow-x-auto">
        @if (($role ?? 'admin') === 'admin')
          <button type="button" data-tab-target="#tab-school-years" class="sg-tab-active sg-btn !justify-start w-full">
            <i data-lucide="calendar-range" class="w-4 h-4"></i> School Years
          </button>
          <button type="button" data-tab-target="#tab-grading" class="sg-btn !justify-start w-full">
            <i data-lucide="percent" class="w-4 h-4"></i> Grading Scale
          </button>
          <button type="button" data-tab-target="#tab-general" class="sg-btn !justify-start w-full">
            <i data-lucide="school" class="w-4 h-4"></i> School Info
          </button>
        @endif
        <button type="button" data-tab-target="#tab-signature"
          class="sg-btn !justify-start w-full {{ ($role ?? 'admin') === 'registrar' ? 'sg-tab-active' : '' }}">
          <i data-lucide="signature" class="w-4 h-4"></i> Registrar Signature
        </button>
      </div>
    </div>

    {{-- Panels --}}
    <div class="lg:col-span-3 space-y-5">

      {{-- ───── School Years ───── --}}
      @if (($role ?? 'admin') === 'admin')
      <div data-tab-panel id="tab-school-years">
        <div class="sg-card p-5">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-display font-semibold" style="color:var(--sg-text)">School Years & Semesters</h3>
              <p class="text-xs mt-0.5" style="color:var(--sg-text-muted)">
                The current school year scopes all grade submissions and reports.
              </p>
            </div>
            <button type="button" data-modal-open="add-sy-modal" class="sg-btn sg-btn-primary flex-shrink-0">
              <i data-lucide="plus" class="w-4 h-4"></i> Add School Year
            </button>
          </div>
          <div class="overflow-x-auto no-print">
            <table class="sg-table">
              <thead>
                <tr>
                  <th>School Year</th>
                  <th>Semester</th>
                  <th>Status</th>
                  <th class="text-right no-print">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($schoolYears as $sy)
                  <tr>
                    <td>{{ $sy->name }}</td>
                    <td style="color:var(--sg-text-muted)">{{ $sy->semester }}</td>
                    <td>
                      @if($sy->is_current)
                        <span class="sg-badge sg-badge-success">Current</span>
                      @elseif($sy->is_active)
                        <span class="sg-badge sg-badge-info">Active</span>
                      @else
                        <span class="sg-badge sg-badge-brass">Archived</span>
                      @endif
                    </td>
                    <td class="text-right no-print">
                      @if(!$sy->is_current)
                        <form method="POST" action="{{ route('admin.school-years.activate', $sy) }}">
                          @csrf
                          @method('PUT')
                          <button type="submit" class="sg-btn sg-btn-ghost !p-2" data-toast="Set as current school year."
                            title="Set as current">
                            <i data-lucide="check" class="w-4 h-4"></i>
                          </button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-10" style="color:var(--sg-text-muted)">No school years defined.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- ───── Grading Scale ───── --}}
      <div data-tab-panel id="tab-grading" class="hidden">
        <div class="sg-card p-5">
          <h3 class="font-display font-semibold mb-1" style="color:var(--sg-text)">Grade Weighting</h3>
          <p class="text-sm mb-5" style="color:var(--sg-text-muted)">
            How the system computes the final grade from prelim, midterm, and final
            components. Weights must total 100.
          </p>

          @if ($errors->any())
            <div class="mb-4 rounded-xl p-3.5 flex items-start gap-3 border"
              style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important;">
              <i data-lucide="circle-x" class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:var(--sg-danger)"></i>
              <div class="text-sm">
                <p class="font-semibold" style="color:var(--sg-text)">Please fix the following:</p>
                <ul class="mt-1 list-disc list-inside text-xs space-y-0.5" style="color:var(--sg-text-muted)">
                  @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.grading-scale.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
              <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Prelim weight</label>
                <div class="relative">
                  <input type="number" name="grade_weight_prelim" min="0" max="100" required
                    value="{{ old('grade_weight_prelim', $settings['grade_weight_prelim']) }}" class="sg-input pr-9"
                    data-grading-weight>
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"
                    style="color:var(--sg-text-muted)">%</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Midterm weight</label>
                <div class="relative">
                  <input type="number" name="grade_weight_midterm" min="0" max="100" required
                    value="{{ old('grade_weight_midterm', $settings['grade_weight_midterm']) }}" class="sg-input pr-9"
                    data-grading-weight>
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"
                    style="color:var(--sg-text-muted)">%</span>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Final weight</label>
                <div class="relative">
                  <input type="number" name="grade_weight_final" min="0" max="100" required
                    value="{{ old('grade_weight_final', $settings['grade_weight_final']) }}" class="sg-input pr-9"
                    data-grading-weight>
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"
                    style="color:var(--sg-text-muted)">%</span>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between gap-4 mb-5 p-4 rounded-xl" style="background:var(--sg-navy-50)">
              <div>
                <p class="text-xs uppercase tracking-wider" style="color:var(--sg-text-muted)">Total</p>
                <p class="font-display text-2xl font-bold" style="color:var(--sg-text)" data-grading-total>100%</p>
              </div>
              <div class="flex items-center gap-3">
                <label class="text-sm font-medium whitespace-nowrap" style="color:var(--sg-text)">Passing grade</label>
                <div class="relative">
                  <input type="number" name="passing_grade" min="0" max="100" step="0.01" required
                    value="{{ old('passing_grade', $settings['passing_grade']) }}" class="sg-input !w-28 pr-9">
                  <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm"
                    style="color:var(--sg-text-muted)">%</span>
                </div>
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="sg-btn sg-btn-primary">
                <i data-lucide="check" class="w-4 h-4"></i> Save Changes
              </button>
            </div>
          </form>
        </div>

        {{-- ───── Official CKC Grading Equivalent Table ───── --}}
        <div class="sg-card p-5 mt-5">
          <div class="flex items-center gap-3 mb-1">
            <span class="w-6 h-px" style="background:var(--sg-brass-500)"></span>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em]" style="color:var(--sg-brass-600)">CKC
              official scale</p>
          </div>
          <h3 class="font-display font-semibold text-lg mb-1" style="color:var(--sg-text)">Grading Equivalent</h3>
          <p class="text-sm mb-4" style="color:var(--sg-text-muted)">
            The official Christ the King College grading equivalent table. Used by the
            system to convert final percentage grades to the 1.0–5.0 scale and to assign
            the Passed / Failed remark.
          </p>

          <div class="overflow-x-auto no-print">
            <table class="sg-table">
              <thead>
                <tr>
                  <th class="text-right">Percentage</th>
                  <th class="text-right">Grade Equivalent</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($gradingScale as $row)
                  <tr>
                    <td class="text-right font-semibold" style="color:var(--sg-text)">{{ $row['percentage'] }}</td>
                    <td class="text-right font-display font-semibold" style="color:var(--sg-navy-700)">
                      {{ $row['equivalent'] }}</td>
                    <td>
                      <span class="sg-badge sg-badge-{{ $row['tone'] }}">{{ $row['status'] }}</span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- ───── School Info ───── --}}
      <div data-tab-panel id="tab-general" class="hidden">
        <div class="sg-card p-5">
          <h3 class="font-display font-semibold mb-1" style="color:var(--sg-text)">School Information</h3>
          <p class="text-sm mb-5" style="color:var(--sg-text-muted)">Used on printed reports and the school header throughout the system.</p>

          @if ($errors->any() && request()->query('tab') === 'tab-general')
            <div class="mb-4 rounded-xl p-3.5 border" role="alert"
              style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important; color:var(--sg-danger)">
              @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('admin.school-info.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
              <label for="school_name" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Name</label>
              <input type="text" id="school_name" name="school_name" required maxlength="255"
                value="{{ old('school_name', $settings['school_name']) }}" class="sg-input">
            </div>
            <div>
              <label for="school_address" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Address</label>
              <input type="text" id="school_address" name="school_address" maxlength="500"
                value="{{ old('school_address', $settings['school_address']) }}" class="sg-input">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="school_contact" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Contact Number</label>
                <input type="text" id="school_contact" name="school_contact" maxlength="50"
                  value="{{ old('school_contact', $settings['school_contact']) }}" class="sg-input">
              </div>
              <div>
                <label for="school_email" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Email</label>
                <input type="email" id="school_email" name="school_email" maxlength="255"
                  value="{{ old('school_email', $settings['school_email']) }}" class="sg-input">
              </div>
            </div>
            <div class="flex justify-end pt-2">
              <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save Changes</button>
            </div>
          </form>
        </div>
      </div>
      @endif

      {{-- ───── Signature Settings ───── --}}
      <div data-tab-panel id="tab-signature" class="{{ ($role ?? 'admin') === 'registrar' ? '' : 'hidden' }}">
        <div class="sg-card p-5 space-y-6" data-signature-settings>
          <div>
            <h3 class="font-display font-semibold mb-1" style="color:var(--sg-text)">Official Signature Settings</h3>
            <p class="text-sm" style="color:var(--sg-text-muted)">
              Upload a JPG, JPEG, or PNG and remove its background locally in this browser, or draw a signature below.
              The original image is not sent to an image-processing service.
            </p>
          </div>

          @if ($errors->any() && request()->query('tab') === 'tab-signature')
            <div class="rounded-xl p-3.5 border" role="alert"
              style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important; color:var(--sg-danger)">
              @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
            </div>
          @endif

          @if ($signatureUrl)
            <div>
              <p class="text-sm font-medium mb-2" style="color:var(--sg-text)">Current saved signature</p>
              <div class="inline-flex items-center justify-center rounded-xl border p-4 bg-white"
                style="border-color:var(--sg-border); min-width:240px; min-height:100px">
                <img src="{{ $signatureUrl }}" alt="Current official signature" class="max-h-20 max-w-full object-contain">
              </div>
              <form method="POST" action="{{ route(($role ?? 'admin') . '.signature.remove') }}" class="mt-3"
                onsubmit="return confirm('Remove the saved official signature?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="sg-btn sg-btn-outline" style="color:var(--sg-danger)">Remove saved signature</button>
              </form>
            </div>
          @endif

          <form id="signature-upload-form" method="POST" enctype="multipart/form-data"
            action="{{ route(($role ?? 'admin') . '.signature.update') }}" class="space-y-4">
            @csrf
            <h4 class="font-semibold" style="color:var(--sg-text)">Option A · Upload and remove background</h4>
            <div>
              <label for="signature-source" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Signature image</label>
              <input id="signature-source" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                class="sg-input !h-auto !py-2">
              <p class="text-xs mt-1" style="color:var(--sg-text-muted)">JPG/JPEG or PNG, up to 10 MB. Image segmentation runs on this device; the processed transparent PNG is uploaded only after you save.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="upload-signatory-name" class="block text-sm font-medium mb-1.5">Signatory name</label>
                <input id="upload-signatory-name" name="signatory_name" type="text" maxlength="255" required class="sg-input"
                  value="{{ old('signatory_name', $settings['signatory_name']) }}">
              </div>
              <div>
                <label for="upload-signatory-position" class="block text-sm font-medium mb-1.5">Official position</label>
                <input id="upload-signatory-position" name="signatory_position" type="text" maxlength="255" required class="sg-input"
                  value="{{ old('signatory_position', $settings['signatory_position']) }}">
              </div>
            </div>
            <input id="signature-processed" type="file" name="signature" tabindex="-1" aria-hidden="true"
              class="absolute w-px h-px opacity-0 pointer-events-none">
            <p id="signature-status" class="text-sm hidden" role="status" aria-live="polite"></p>
            <div id="signature-preview-wrap" class="hidden">
              <p class="text-sm font-medium mb-2" style="color:var(--sg-text)">Processed transparent PNG preview</p>
              <div class="inline-flex items-center justify-center rounded-xl border p-4"
                style="border-color:var(--sg-border); min-width:240px; min-height:100px; background-color:#fff; background-image:linear-gradient(45deg,#eee 25%,transparent 25%),linear-gradient(-45deg,#eee 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#eee 75%),linear-gradient(-45deg,transparent 75%,#eee 75%); background-size:16px 16px; background-position:0 0,0 8px,8px -8px,-8px 0">
                <img id="signature-preview" alt="Signature with background removed" class="max-h-24 max-w-full object-contain">
              </div>
            </div>
            <div class="flex justify-end">
              <button id="signature-submit" type="submit" class="sg-btn sg-btn-primary" disabled>
                <i data-lucide="upload" class="w-4 h-4"></i> Save uploaded signature
              </button>
            </div>
          </form>

          <form id="draw-signature-form" method="POST" enctype="multipart/form-data"
            action="{{ route(($role ?? 'admin') . '.signature.update') }}" class="space-y-4 border-t pt-5"
            style="border-color:var(--sg-border)">
            @csrf
            <h4 class="font-semibold" style="color:var(--sg-text)">Option B · Draw or write a signature</h4>
            <p class="text-xs" style="color:var(--sg-text-muted)">Use a mouse, touchscreen, or stylus. The saved PNG has a transparent background.</p>
            <canvas id="signature-pad" class="w-full rounded-xl border bg-white touch-none"
              style="height:180px; border-color:var(--sg-border); touch-action:none" aria-label="Draw your signature"></canvas>
            <div class="flex gap-2">
              <button id="signature-clear" type="button" class="sg-btn sg-btn-outline">Clear and redraw</button>
            </div>
            <div id="draw-preview-wrap" class="hidden">
              <p class="text-sm font-medium mb-2">Drawing preview</p>
              <div class="inline-flex rounded-xl border p-3 bg-white" style="border-color:var(--sg-border)">
                <img id="draw-signature-preview" alt="Signature drawing preview" class="max-h-20 max-w-full object-contain">
              </div>
            </div>
            <input id="draw-signature-file" type="file" name="signature" tabindex="-1" aria-hidden="true"
              class="absolute w-px h-px opacity-0 pointer-events-none">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="draw-signatory-name" class="block text-sm font-medium mb-1.5">Signatory name</label>
                <input id="draw-signatory-name" name="signatory_name" type="text" maxlength="255" required class="sg-input"
                  value="{{ old('signatory_name', $settings['signatory_name']) }}">
              </div>
              <div>
                <label for="draw-signatory-position" class="block text-sm font-medium mb-1.5">Official position</label>
                <input id="draw-signatory-position" name="signatory_position" type="text" maxlength="255" required class="sg-input"
                  value="{{ old('signatory_position', $settings['signatory_position']) }}">
              </div>
            </div>
            <p id="draw-signature-status" class="text-sm hidden" role="status" aria-live="polite"></p>
            <div class="flex justify-end">
              <button id="draw-signature-submit" type="submit" class="sg-btn sg-btn-primary" disabled>
                <i data-lucide="save" class="w-4 h-4"></i> Save drawn signature
              </button>
            </div>
          </form>

          @if ($signatureUpdatedAt)
            <p class="text-xs" style="color:var(--sg-text-muted)">
              Last updated {{ \Illuminate\Support\Carbon::parse($signatureUpdatedAt)->format('M j, Y g:i A') }}
              @if ($signatureUpdatedBy) by {{ $signatureUpdatedBy->full_name }} @endif
            </p>
          @endif
        </div>
      </div>
    </div>
  </div>

  @if (($role ?? 'admin') === 'admin')
  {{-- Add School Year Modal --}}
  <div id="add-sy-modal" class="hidden fixed inset-0 z-[70] sg-modal-backdrop flex items-center justify-center p-4">
    <div class="sg-modal sg-surface rounded-2xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--sg-border)">
        <h3 class="font-display font-semibold text-base" style="color:var(--sg-text)">Add School Year</h3>
        <button type="button" data-modal-close="add-sy-modal" class="sg-btn sg-btn-ghost !p-2">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <form method="POST" action="{{ route('admin.school-years.store') }}" class="p-6 space-y-4">
        @csrf
        <div>
          <label for="sy_name" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School Year
            Name</label>
          <input type="text" id="sy_name" name="name" required placeholder="e.g. 2027–2028" class="sg-input">
        </div>
        <div>
          <label for="sy_semester" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Semester</label>
          <select id="sy_semester" name="semester" class="sg-select" required>
            <option value="First Semester">First Semester</option>
            <option value="Second Semester">Second Semester</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" data-modal-close="add-sy-modal" class="sg-btn sg-btn-outline">Cancel</button>
          <button type="submit" class="sg-btn sg-btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  @once
    @push('scripts')
      <script>
        (function () {
          // Switch the active tab when the URL has ?tab=tab-xxx (used after save).
          const params = new URLSearchParams(window.location.search);
          const wantedTab = params.get('tab');
          if (wantedTab) {
            const root = document.querySelector('[data-tabs]');
            if (root) {
              const target = root.querySelector(wantedTab);
              if (target) {
                root.querySelectorAll('[data-tab-panel]').forEach((p) => p.classList.add('hidden'));
                target.classList.remove('hidden');
                root.querySelectorAll('[data-tab-target]').forEach((b) => b.classList.remove('sg-tab-active'));
                const activeBtn = root.querySelector(`[data-tab-target="${wantedTab}"]`);
                if (activeBtn) activeBtn.classList.add('sg-tab-active');
              }
            }
          }

          // Live total of grading weights.
          const weightInputs = document.querySelectorAll('[data-grading-weight]');
          const totalEl = document.querySelector('[data-grading-total]');
          const recompute = () => {
            if (!totalEl) return;
            let total = 0;
            weightInputs.forEach((i) => {
              const n = parseInt(i.value, 10);
              if (!isNaN(n)) total += n;
            });
            totalEl.textContent = total + '%';
            totalEl.style.color = total === 100
              ? 'var(--sg-success)'
              : (total > 100 ? 'var(--sg-danger)' : 'var(--sg-text)');
          };
          weightInputs.forEach((i) => i.addEventListener('input', recompute));
          recompute();
        })();

      </script>
      @vite('resources/js/signature-settings.js')
    @endpush
  @endonce
@endsection