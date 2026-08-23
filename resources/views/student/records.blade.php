@extends('layouts.app', ['role' => 'student', 'active' => 'records', 'title' => 'Academic Records'])

@section('content')
<div class="space-y-5">
  <div>
    <h2 class="font-display font-semibold text-lg" style="color:var(--sg-text)">Academic Records</h2>
    <p class="text-sm" style="color:var(--sg-text-muted)">Completed semesters and grade history</p>
  </div>

  <div class="space-y-4" id="records-list"></div>
</div>
@endsection

@push('scripts')
<script>
  // Mock data placeholder
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    </div>
  `).join('');
</script>
@endpush
