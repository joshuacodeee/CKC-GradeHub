<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $assignment->subject?->code ?? 'Grade Report' }} — {{ $assignment->section?->name ?? '' }} — CKC GradeHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="/css/tailwind.css">
  <link rel="stylesheet" href="/css/custom.css">
  <style>
    body {
      font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
      background: #fff;
      color: #000;
      margin: 0;
      padding: 24px;
    }

    .report-page {
      max-width: 1100px;
      margin: 0 auto;
    }

    .sg-table th, .sg-table td {
      padding: 6px 8px;
    }

    .sg-table { font-size: 11px; }

    .print-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
      padding: 10px 14px;
      border-radius: 10px;
      background: #f3f7fc;
      border: 1px solid #e7eff8;
    }

    .print-toolbar button {
      cursor: pointer;
      border: 1px solid #1d4d82;
      background: #1d4d82;
      color: #fff;
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .print-toolbar .back-link {
      color: #1d4d82;
      font-size: 13px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .institutional-header {
      text-align: center;
      padding: 8px 0 16px 0;
      border-bottom: 1px solid #e7eff8;
      margin-bottom: 16px;
    }

    .institutional-header h1 {
      font-family: 'Sora', 'Inter', ui-sans-serif, sans-serif;
      font-size: 22px;
      font-weight: 800;
      margin: 4px 0;
      color: #0b1a33;
    }

    .institutional-header .course {
      font-size: 13px;
      color: #1d4d82;
      font-weight: 600;
    }

    .institutional-header .meta {
      font-size: 11px;
      color: #4a5568;
      margin-top: 4px;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-bottom: 16px;
    }

    .stat {
      border: 1px solid #e7eff8;
      border-radius: 10px;
      padding: 10px 12px;
    }

    .stat .label {
      font-size: 10px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #4a5568;
    }

    .stat .value {
      font-family: 'Sora', 'Inter', sans-serif;
      font-size: 20px;
      font-weight: 700;
      color: #0b1a33;
      margin-top: 4px;
    }

    table { border-collapse: collapse; width: 100%; }
    thead th {
      background: #f3f7fc;
      text-align: left;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #1d4d82;
      border-bottom: 2px solid #1d4d82;
    }
    tbody td {
      border-bottom: 1px solid #e7eff8;
    }
    tbody tr:nth-child(even) { background: #fafbfd; }

    .signature-block {
      width: 260px;
      margin: 36px 0 0 auto;
      text-align: center;
      font-size: 11px;
    }

    .signature-block img {
      display: block;
      max-width: 220px;
      max-height: 78px;
      object-fit: contain;
      margin: 0 auto -4px;
    }

    .signature-line {
      border-top: 1px solid #333;
      padding-top: 5px;
    }

    .footer-note {
      margin-top: 16px;
      font-size: 10px;
      color: #4a5568;
      text-align: center;
    }

    @media print {
      body { padding: 0; }
      .print-toolbar { display: none !important; }
      .sg-card { box-shadow: none !important; }
      @page { margin: 1.5cm; }
    }
  </style>
</head>

<body>
  <div class="report-page">

    {{-- Toolbar with Print + Back --}}
    <div class="print-toolbar">
      <a href="{{ route('teacher.reports.show', $assignment) }}" class="back-link">
        ← Back to report
      </a>
      <button type="button" onclick="window.print()">
        <span>🖨</span> Print this report
      </button>
    </div>

    {{-- Institutional header --}}
    <div class="institutional-header">
      <p style="font-size:10px; letter-spacing:0.22em; text-transform:uppercase; color:#4a5568; margin:0;">
        Christ the King College · Gingoog City
      </p>
      <h1>
        {{ $assignment->subject?->name ?? 'Subject' }}
        <span style="color:#4a5568; font-weight:500;">—</span>
        {{ $assignment->subject?->code ?? '' }}
      </h1>
      <p class="course">
        {{ $assignment->section?->short_course_name ?? '—' }}
        @if($assignment->section?->year_level) · {{ $assignment->section->year_level }} @endif
        @if($assignment->section?->block) · Block {{ $assignment->section->block }} @endif
      </p>
      <p class="meta">
        {{ $assignment->semester ?? '—' }},
        S.Y. {{ $assignment->schoolYear?->name ?? '—' }}
        @if($assignment->class_code) · Class {{ $assignment->class_code }} @endif
        @if($assignment->subject?->units) · {{ $assignment->subject->units }} {{ \Illuminate\Support\Str::plural('unit', (int) $assignment->subject->units) }} @endif
        @if($assignment->teacher) · Teacher: {{ $assignment->teacher->name }} @endif
      </p>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
      <div class="stat">
        <div class="label">Enrolled</div>
        <div class="value">{{ $totalStudents }}</div>
      </div>
      <div class="stat">
        <div class="label">Recorded</div>
        <div class="value">{{ $gradedCount }}</div>
      </div>
      <div class="stat">
        <div class="label">Submitted</div>
        <div class="value">{{ $submittedCount }} / {{ $totalStudents }}</div>
      </div>
      <div class="stat">
        <div class="label">Class average</div>
        <div class="value">
          @if ($classAverage !== null)
            {{ number_format((float) $classAverage, 2) }}
            <span style="font-size:11px; color:#4a5568; font-weight:500;">
              · {{ number_format(\App\Support\GradingScale::equivalent((float) $classAverage), 1) }}
            </span>
          @else
            —
          @endif
        </div>
      </div>
    </div>

    {{-- Grade matrix --}}
    <table>
      <thead>
        <tr>
          <th style="width:32px;">#</th>
          <th>Student</th>
          <th>Student ID</th>
          <th style="text-align:center;">Course · Year · Block</th>
          <th style="text-align:center;">Units</th>
          <th style="text-align:center;">Prelim (Eq.)</th>
          <th style="text-align:center;">Midterm (Eq.)</th>
          <th style="text-align:center;">Final (Eq.)</th>
          <th>Remarks</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($gradeRows as $i => $row)
          @php
            $prelim  = $row['prelim']  !== null ? (float) $row['prelim']  : null;
            $midterm = $row['midterm'] !== null ? (float) $row['midterm'] : null;
            $final   = $row['final_grade'] !== null ? (float) $row['final_grade'] : null;
            $prelimEq  = $prelim  !== null ? \App\Support\GradingScale::equivalent($prelim)  : null;
            $midtermEq = $midterm !== null ? \App\Support\GradingScale::equivalent($midterm) : null;
            $finalEq   = $final   !== null ? \App\Support\GradingScale::equivalent($final)   : null;
            $remarks = $row['remarks'] ?? null;
            $status  = $row['status'] ?? null;
          @endphp
          <tr>
            <td style="color:#4a5568;">{{ $i + 1 }}</td>
            <td><strong>{{ $row['student_name'] }}</strong></td>
            <td style="color:#4a5568;">{{ $row['student_no'] }}</td>
            <td style="text-align:center; color:#4a5568;">
              {{ trim(($row['course'] ?? '') . ' · ' . ($row['year_level'] ?? '') . ($row['block'] ? ' · Block ' . $row['block'] : ''), ' ·') ?: '—' }}
            </td>
            <td style="text-align:center;">{{ $assignment->subject?->units ?? '—' }}</td>
            <td style="text-align:center; font-weight:600; color:#0b1a33;">
              {{ $prelimEq !== null ? number_format($prelimEq, 1) : '—' }}
            </td>
            <td style="text-align:center; font-weight:600; color:#0b1a33;">
              {{ $midtermEq !== null ? number_format($midtermEq, 1) : '—' }}
            </td>
            <td style="text-align:center; font-weight:600; color:#0b1a33;">
              {{ $finalEq !== null ? number_format($finalEq, 1) : '—' }}
            </td>
            <td>
              @if ($remarks === 'Passed')
                <span style="color:#1d6f42; font-weight:600;">Passed</span>
              @elseif ($remarks === 'Failed')
                <span style="color:#a02b2b; font-weight:600;">Failed</span>
              @else
                <span style="color:#4a5568;">—</span>
              @endif
            </td>
            <td>
              @if ($status === 'submitted')
                <span style="color:#1d6f42; font-weight:600;">Submitted</span>
              @elseif ($status === 'draft')
                <span style="color:#b56a05; font-weight:600;">Draft</span>
              @else
                <span style="color:#4a5568;">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" style="text-align:center; padding:24px; color:#4a5568;">
              No enrolled students in this class offering yet.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if (!empty($registrarSignatureUrl))
      <div class="signature-block">
        <img src="{{ $registrarSignatureUrl }}" alt="Registrar signature">
        <div class="signature-line">Registrar</div>
      </div>
    @endif

    <p class="footer-note">
      Generated by CKC GradeHub on {{ now()->format('F j, Y g:i A') }}
    </p>
  </div>
</body>

</html>
