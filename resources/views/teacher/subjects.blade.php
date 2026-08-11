@extends('layouts.app', ['role' => 'teacher', 'active' => 'subjects', 'title' => 'My Subjects'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">My Subjects</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Subjects assigned to you this semester</p>
  </div>

  <div class="sg-card p-5">
    <div class="overflow-x-auto">
      <table class="sg-table">
        <thead><tr><th>Code</th><th>Subject Name</th><th>Units</th><th>Sections Handled</th><th>Total Students</th></tr></thead>
        <tbody id="teacher-subjects-tbody"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const bySubject = {};
  teacherClasses.forEach(c => {
    if (!bySubject[c.subjectCode]) bySubject[c.subjectCode] = { subject: c.subject, sections: [], students: 0 };
    bySubject[c.subjectCode].sections.push(c.section);
    bySubject[c.subjectCode].students += c.students;
  });

  document.getElementById('teacher-subjects-tbody').innerHTML = Object.entries(bySubject).map(([code, d]) => {
    const meta = subjects.find(s => s.code === code) || { units: 3 };
    return `
    <tr>
      <td class="font-medium" style="color:var(--sg-text)">${code}</td>
      <td style="color:var(--sg-text)">${d.subject}</td>
      <td style="color:var(--sg-text-muted)">${meta.units}</td>
      <td>${d.sections.map(s => `<span class="sg-badge sg-badge-info mr-1 mb-1 inline-block">${s}</span>`).join('')}</td>
      <td style="color:var(--sg-text)">${d.students}</td>
    </tr>`;
  }).join('');
  SG.renderIcons();
</script>
@endpush
