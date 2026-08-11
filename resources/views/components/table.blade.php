{{--
  Table toolbar: search + filter + "Add" action, used above data tables.
  Usage:
  @include('components.table', [
    'tableId' => 'students-table',
    'searchPlaceholder' => 'Search students by name or ID…',
    'filterColumn' => 'status',
    'filterOptions' => ['Active', 'Inactive', 'Suspended'],
    'addLabel' => 'Add Student',
    'addModalId' => 'add-student-modal',
    'addIcon' => 'user-plus',
  ])
--}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
  <div class="relative flex-1">
    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color:var(--sg-text-muted)"></i>
    <input type="text" data-table-search="{{ $tableId }}" placeholder="{{ $searchPlaceholder ?? 'Search…' }}" class="sg-input pl-9">
  </div>

  @isset($filterOptions)
  <select data-table-filter="{{ $tableId }}" data-filter-column="{{ $filterColumn }}" class="sg-select sm:w-48">
    <option value="">All {{ ucfirst($filterColumn) }}</option>
    @foreach ($filterOptions as $opt)
      <option value="{{ $opt }}">{{ $opt }}</option>
    @endforeach
  </select>
  @endisset

  @isset($addLabel)
  <button data-modal-open="{{ $addModalId }}" class="sg-btn sg-btn-primary whitespace-nowrap">
    <i data-lucide="{{ $addIcon ?? 'plus' }}" class="w-4 h-4"></i> {{ $addLabel }}
  </button>
  @endisset
</div>
