<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Grade Report · {{ $student->last_name_first }}</title>
  <style>
    :root { color-scheme: light; font-family: Arial, sans-serif; color: #10233e; }
    body { margin: 0; background: #f3f6fa; }
    .page { max-width: 1100px; margin: 24px auto; padding: 32px; background: #fff; }
    .topbar { display: flex; justify-content: space-between; gap: 16px; margin-bottom: 28px; }
    .actions { display: flex; gap: 8px; }
    button, .back { border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 13px; background: #fff; color: #10233e; text-decoration: none; cursor: pointer; }
    h1 { margin: 0; font-size: 24px; } h2 { margin: 0 0 5px; font-size: 18px; }
    .muted { color: #64748b; font-size: 13px; line-height: 1.6; }
    .school { border-bottom: 3px solid #c8952f; padding-bottom: 18px; margin-bottom: 22px; }
    .identity { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 20px 0; }
    .identity p { margin: 4px 0; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
    th { background: #102a4c; color: #fff; }
    .signature { width: 260px; margin: 52px 0 0 auto; text-align: center; font-size: 12px; }
    .signature img { display: block; max-width: 220px; max-height: 76px; margin: 0 auto; object-fit: contain; }
    .signature-line { border-bottom: 1px solid #1e293b; margin: 6px auto 0; padding-bottom: 5px; font-weight: 700; }
    @media print {
      body { background: #fff; }
      .page { max-width: none; margin: 0; padding: 12mm; }
      .no-print { display: none !important; }
      tr { page-break-inside: avoid; }
      th { color: #fff !important; print-color-adjust: exact; }
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="topbar no-print">
      <a class="back" href="{{ route('registrar.students.show', ['student' => $student, ...$filters]) }}">Back to student record</a>
      <div class="actions"><button type="button" onclick="window.print()">Print report</button></div>
    </div>

    <header class="school">
      <h1>{{ $schoolName }}</h1>
      <p class="muted">Official student grade report</p>
    </header>

    <h2>{{ $student->last_name_first_middle }}</h2>
    <div class="identity muted">
      <p><strong>Student ID:</strong> {{ $student->student_id ?? '—' }}</p>
      <p><strong>Course:</strong> {{ $student->course ?? '—' }}</p>
      <p><strong>Year level:</strong> {{ $student->year_level ?? '—' }}</p>
      <p><strong>Period:</strong>
        {{ $filters['school_year_id'] ? \App\Models\SchoolYear::find($filters['school_year_id'])?->name : 'All school years' }}
        · {{ $filters['semester'] ?? 'All semesters' }}
      </p>
    </div>

    <table>
      <thead>
        <tr><th>School year</th><th>Semester</th><th>Subject</th><th>Section</th><th>Teacher</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Final grade</th><th>Remarks</th></tr>
      </thead>
      <tbody>
        @forelse ($grades as $grade)
          @php
            $assignment = $grade->teacherAssignment;
            $academicYear = $assignment?->schoolYear ?? $grade->enrollment?->schoolYear;
            $subject = $assignment?->subject ?? $grade->subject;
            $teacher = $assignment?->teacher ?? $grade->teacher;
            $section = $assignment?->section ?? $grade->enrollment?->section;
          @endphp
          <tr>
            <td>{{ $academicYear?->name ?? '—' }}</td>
            <td>{{ $assignment?->semester ?? $academicYear?->semester ?? '—' }}</td>
            <td>{{ $subject?->code ?? '—' }} · {{ $subject?->name ?? 'Unknown subject' }}</td>
            <td>{{ $section?->name ?? '—' }}</td>
            <td>{{ $teacher?->full_name ?? '—' }}</td>
            <td>{{ $grade->prelim ?? '—' }}</td>
            <td>{{ $grade->midterm ?? '—' }}</td>
            <td>{{ $grade->final ?? '—' }}</td>
            <td>{{ $grade->final_grade ?? '—' }}</td>
            <td>{{ $grade->remarks ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="10" style="text-align:center">No submitted grades were found for this period.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="signature">
      @if ($signatureUrl)
        <img src="{{ $signatureUrl }}" alt="Official registrar signature">
      @else
        <div style="height: 76px"></div>
      @endif
      <div class="signature-line">{{ $signatoryName }}</div>
      <div>College Registrar</div>
    </div>
  </main>
</body>
</html>
