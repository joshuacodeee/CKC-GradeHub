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
          <p class="text-sm mb-5" style="color:var(--sg-text-muted)">
            Used on printed reports and the school header throughout the system.
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
              @endif

              <div data-tab-panel id="tab-signature" class="{{ ($role ?? 'admin') === 'registrar' ? '' : 'hidden' }}">
                <div class="sg-card p-5">
                  <h3 class="font-display font-semibold mb-1" style="color:var(--sg-text)">Registrar Signature</h3>
                  <p class="text-sm mb-5" style="color:var(--sg-text-muted)">
                    Upload a clear signature on white or off-white paper. The background is removed in this browser before upload.
                  </p>

                  @if ($errors->any())
                    <div class="mb-4 rounded-xl p-3.5 border" role="alert"
                      style="background:var(--sg-danger-bg); border-color:var(--sg-danger) !important; color:var(--sg-danger)">
                      @foreach ($errors->all() as $err)
                        <p>{{ $err }}</p>
                      @endforeach
                    </div>
                  @endif

                  @if ($signatureUrl)
                    <div class="mb-5">
                      <p class="text-sm font-medium mb-2" style="color:var(--sg-text)">Current signature</p>
                      <div class="inline-flex items-center justify-center rounded-xl border p-4 bg-white"
                        style="border-color:var(--sg-border); min-width:240px; min-height:100px">
                        <img src="{{ $signatureUrl }}" alt="Current registrar signature" class="max-h-20 max-w-full object-contain">
                      </div>
                    </div>
                  @endif

                  <form id="signature-upload-form" method="POST" enctype="multipart/form-data"
                    action="{{ route(($role ?? 'admin') . '.signature.update') }}" class="space-y-4">
                    @csrf
                    <div>
                      <label for="signature-source" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">
                        Choose signature image
                      </label>
                      <input id="signature-source" type="file" accept="image/png,image/jpeg,image/webp" required
                        class="sg-input !h-auto !py-2">
                      <p class="text-xs mt-1" style="color:var(--sg-text-muted)">PNG, JPEG, or WebP; background removal is automatic.</p>
                    </div>

                    <input id="signature-processed" type="file" name="signature" required tabindex="-1" aria-hidden="true"
                      class="absolute w-px h-px opacity-0 pointer-events-none">
                    <p id="signature-status" class="text-sm hidden" role="status" aria-live="polite"></p>

                    <div id="signature-preview-wrap" class="hidden">
                      <p class="text-sm font-medium mb-2" style="color:var(--sg-text)">Transparent preview</p>
                      <div class="inline-flex items-center justify-center rounded-xl border p-4"
                        style="border-color:var(--sg-border); min-width:240px; min-height:100px; background-color:#fff; background-image:linear-gradient(45deg,#eee 25%,transparent 25%),linear-gradient(-45deg,#eee 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#eee 75%),linear-gradient(-45deg,transparent 75%,#eee 75%); background-size:16px 16px; background-position:0 0,0 8px,8px -8px,-8px 0">
                        <img id="signature-preview" alt="Signature with background removed" class="max-h-24 max-w-full object-contain">
                      </div>
                    </div>

                    <div class="flex justify-end pt-2">
                      <button id="signature-submit" type="submit" class="sg-btn sg-btn-primary" disabled>
                        <i data-lucide="upload" class="w-4 h-4"></i> Save Signature
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.school-info.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
              <label for="school_name" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">School
                Name</label>
              <input type="text" id="school_name" name="school_name" required maxlength="255"
                value="{{ old('school_name', $settings['school_name']) }}" class="sg-input">
            </div>

            <div>
              <label for="school_address" class="block text-sm font-medium mb-1.5"
                style="color:var(--sg-text)">Address</label>
              <input type="text" id="school_address" name="school_address" maxlength="500"
                value="{{ old('school_address', $settings['school_address']) }}" placeholder="Street, City, Province"
                class="sg-input">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="school_contact" class="block text-sm font-medium mb-1.5" style="color:var(--sg-text)">Contact
                  Number</label>
                <input type="text" id="school_contact" name="school_contact" maxlength="50"
                  value="{{ old('school_contact', $settings['school_contact']) }}" placeholder="e.g. (088) 123-4567"
                  class="sg-input">
              </div>
              <div>
                <label for="school_email" class="block text-sm font-medium mb-1.5"
                  style="color:var(--sg-text)">Email</label>
                <input type="email" id="school_email" name="school_email" maxlength="255"
                  value="{{ old('school_email', $settings['school_email']) }}" placeholder="info@ckcgingoog.edu.ph"
                  class="sg-input">
              </div>
            </div>

            <div class="flex justify-end pt-2">
              <button type="submit" class="sg-btn sg-btn-primary">
                <i data-lucide="check" class="w-4 h-4"></i> Save Changes
              </button>
            </div>
          </form>
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

        const form = document.getElementById('signature-upload-form');
        const source = document.getElementById('signature-source');
        const processed = document.getElementById('signature-processed');
        const preview = document.getElementById('signature-preview');
        const previewWrap = document.getElementById('signature-preview-wrap');
        const status = document.getElementById('signature-status');
        const submit = document.getElementById('signature-submit');
        let previewObjectUrl = null;

        const setSignatureStatus = (message, isError = false) => {
          status.textContent = message;
          status.classList.remove('hidden');
          status.style.color = isError ? 'var(--sg-danger)' : 'var(--sg-text-muted)';
        };

        source?.addEventListener('change', async () => {
          processed.value = '';
          submit.disabled = true;
          previewWrap.classList.add('hidden');
          status.classList.add('hidden');
          if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
          }
          const file = source.files?.[0];
          if (!file) return;

          if (!file.type.startsWith('image/')) {
            setSignatureStatus('Choose a valid image file.', true);
            return;
          }
          if (file.size > 10 * 1024 * 1024) {
            setSignatureStatus('The selected image must be 10 MB or smaller.', true);
            return;
          }

          try {
            const imageUrl = URL.createObjectURL(file);
            const image = new Image();
            image.src = imageUrl;
            try {
              await image.decode();
            } finally {
              URL.revokeObjectURL(imageUrl);
            }

            const scale = Math.min(1, 2400 / Math.max(image.naturalWidth, image.naturalHeight));
            const canvas = document.createElement('canvas');
            canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
            canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
            const context = canvas.getContext('2d', { willReadFrequently: true });
            if (!context) throw new Error('This browser cannot process the selected image.');
            context.drawImage(image, 0, 0, canvas.width, canvas.height);

            const pixels = context.getImageData(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < pixels.data.length; i += 4) {
              const luminance = 0.299 * pixels.data[i] + 0.587 * pixels.data[i + 1] + 0.114 * pixels.data[i + 2];
              const alpha = luminance >= 248 ? 0 : luminance <= 170 ? 255 : Math.round((248 - luminance) / 78 * 255);
              pixels.data[i] = 16;
              pixels.data[i + 1] = 36;
              pixels.data[i + 2] = 67;
              pixels.data[i + 3] = Math.min(pixels.data[i + 3], alpha);
            }
            context.putImageData(pixels, 0, 0);

            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
            if (!blob) throw new Error('Could not create the transparent PNG. Try another image.');
            if (blob.size > 5 * 1024 * 1024) throw new Error('The processed signature image is larger than 5 MB. Try a smaller image.');
            const cleanedFile = new File([blob], 'registrar-signature.png', { type: 'image/png' });
            const transfer = new DataTransfer();
            transfer.items.add(cleanedFile);
            processed.files = transfer.files;
            previewObjectUrl = URL.createObjectURL(blob);
            preview.src = previewObjectUrl;
            previewWrap.classList.remove('hidden');
            submit.disabled = false;
            setSignatureStatus('Background removed. Review the preview, then save.');
          } catch (error) {
            setSignatureStatus(error.message || 'Could not process the image. Try another file.', true);
          }
        });

        form?.addEventListener('submit', (event) => {
          if (!processed.files?.length) {
            event.preventDefault();
            setSignatureStatus('Select an image and wait for background removal before saving.', true);
          }
        });
      </script>
    @endpush
  @endonce
@endsection