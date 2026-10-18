@extends('layouts.app', ['role' => $role ?? 'admin', 'active' => 'reports', 'title' => 'Grade Reports'])

@section('content')
  <div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Grade Reports</h2>
        <p class="text-sm" style="color:var(--sg-text-muted)">
          School-wide grade submissions, {{ $schoolYear?->semester ?? 'First Semester' }}
          {{ $schoolYear?->name ?? '2026–2027' }}
        </p>
      </div>
      @if (($role ?? 'admin') !== 'registrar')
        <div class="flex gap-2">
          <button data-print class="sg-btn sg-btn-outline"><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
        </div>
      @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @include('components.stat-card', ['label' => 'Submitted', 'value' => $submittedCount, 'icon' => 'file-check-2', 'color' => 'success'])
      @include('components.stat-card', ['label' => 'Pending', 'value' => $pendingCount, 'icon' => 'clock', 'color' => 'warning'])
      @include('components.stat-card', ['label' => 'School-wide Average', 'value' => $schoolAverage, 'icon' => 'chart-line', 'color' => 'navy'])
    </div>

    <div class="sg-card p-5 print-area">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-display font-semibold" style="color:var(--sg-text)">Submission Log by Subject & Section</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="sg-table">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Section</th>
              <th>Teacher</th>
              <th>Students</th>
              <th>Class Average</th>
              <th>Status</th>
              <th class="text-right no-print">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($reportRows as $r)
              <tr>
                <td style="color:var(--sg-text)">{{ $r['subject'] }} <span class="text-xs"
                    style="color:var(--sg-text-muted)">({{ $r['code'] }})</span></td>
                <td style="color:var(--sg-text-muted)">{{ $r['section'] }}</td>
                <td style="color:var(--sg-text-muted)">{{ $r['teacher'] }}</td>
                <td style="color:var(--sg-text)">{{ $r['students'] }}</td>
                <td style="color:var(--sg-text)">
                  @if (is_numeric($r['avg']))
                    {{ $r['avg'] }}
                    <span class="text-xs font-semibold" style="color:var(--sg-navy-700)">
                      ({{ number_format($r['avg_equivalent'], 1) }})
                    </span>
                  @else
                    {{ $r['avg'] }}
                  @endif
                </td>
                <td>
                  @if($r['status'] === 'Submitted')
                    <span class="sg-badge sg-badge-success">{{ $r['status'] }}</span>
                  @elseif($r['status'] === 'Partial')
                    <span class="sg-badge sg-badge-warning">{{ $r['status'] }}</span>
                  @else
                    <span class="sg-badge sg-badge-info">{{ $r['status'] }}</span>
                  @endif
                </td>
                <td class="text-right no-print">
                  @if($r['status'] !== 'Pending')
                    <a href="{{ route(($role ?? 'admin') . '.reports.show', $r['assignment_id']) }}"
                      class="sg-btn sg-btn-ghost !p-2 inline-flex" title="View detailed grade report">
                      <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
                  @else
                    <span class="sg-badge sg-badge-info">Not yet submitted</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-10" style="color:var(--sg-text-muted)">No grade submissions found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($signatureUrl)
        <div class="print-only mt-8 ml-auto w-64 text-center text-xs">
          <img src="{{ $signatureUrl }}" alt="Registrar signature" class="mx-auto max-h-16 max-w-[220px] object-contain">
          <div class="border-t border-black pt-1 mt-1">College Registrar</div>
          <div>Christ the King College</div>
        </div>
      @endif
    </div>
  </div>
@endsection