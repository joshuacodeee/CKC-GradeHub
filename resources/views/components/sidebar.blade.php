@php
  $navs = [
    'admin' => [
      ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard', 'key' => 'dashboard'],
      ['label' => 'Students', 'icon' => 'users', 'route' => 'admin.students', 'key' => 'students'],
      ['label' => 'Teachers', 'icon' => 'graduation-cap', 'route' => 'admin.teachers', 'key' => 'teachers'],
      ['label' => 'Subjects', 'icon' => 'book-open', 'route' => 'admin.subjects', 'key' => 'subjects'],
      ['label' => 'Sections', 'icon' => 'layers', 'route' => 'admin.sections', 'key' => 'sections'],
      ['label' => 'Curricula', 'icon' => 'list-tree', 'route' => 'admin.curricula', 'key' => 'curricula'],
      ['label' => 'School Years', 'icon' => 'calendar-range', 'route' => 'admin.settings', 'key' => 'school-years'],
      ['label' => 'Teacher Assignments', 'icon' => 'user-check', 'route' => 'admin.assignments', 'key' => 'assignments'],
      ['label' => 'Grade Reports', 'icon' => 'file-bar-chart-2', 'route' => 'admin.reports', 'key' => 'reports'],
      ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings', 'key' => 'settings'],
    ],

    'teacher' => [
      ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'teacher.dashboard', 'key' => 'dashboard'],
      ['label' => 'My Classes', 'icon' => 'presentation', 'route' => 'teacher.classes', 'key' => 'classes'],
      ['label' => 'My Subjects', 'icon' => 'book-open', 'route' => 'teacher.subjects', 'key' => 'subjects'],
      ['label' => 'My Sections', 'icon' => 'layers', 'route' => 'teacher.sections', 'key' => 'sections'],
      ['label' => 'Students', 'icon' => 'users', 'route' => 'teacher.students', 'key' => 'students'],
      ['label' => 'Enter Grades', 'icon' => 'square-pen', 'route' => 'teacher.grades', 'key' => 'grades'],
      ['label' => 'Grade Records', 'icon' => 'clipboard-list', 'route' => 'teacher.grades', 'key' => 'records'],
      ['label' => 'Reports', 'icon' => 'file-bar-chart-2', 'route' => 'teacher.reports', 'key' => 'reports'],
    ],

    'student' => [
      ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'student.dashboard', 'key' => 'dashboard'],
      ['label' => 'My Grades', 'icon' => 'square-check', 'route' => 'student.grades', 'key' => 'grades'],
      ['label' => 'Subjects', 'icon' => 'book-open', 'route' => 'student.subjects', 'key' => 'subjects'],
      ['label' => 'Academic Records', 'icon' => 'archive', 'route' => 'student.records', 'key' => 'records'],
    ],
  ];

  $items = $navs[$role] ?? $navs['admin'];

  $profileRoute = $role . '.profile';

  $roleLabel = [
    'admin' => 'Administrator',
    'teacher' => 'Teacher',
    'student' => 'Student'
  ][$role] ?? 'User';
@endphp


{{-- ============================================================
SIDEBAR
============================================================ --}}

<aside id="sg-sidebar" class="
        sg-sidebar
        fixed
        inset-y-0
        left-0
        z-50
        w-64
        -translate-x-full
        lg:translate-x-0
        flex
        flex-col
        text-white
        transition-transform
        duration-300
        ease-in-out
    ">

  {{-- ========================================================
  BRAND
  ========================================================= --}}

  <div class="
            flex
            items-center
            justify-between
            gap-3
            px-5
            h-16
            flex-shrink-0
            border-b
            border-white/10
        ">

    <a href="{{ route($role . '.dashboard') }}" class="flex items-center gap-3 min-w-0">

      {{-- CKC Logo --}}
      <div class="
                    w-10
                    h-10
                    flex-shrink-0
                    rounded-lg
                    overflow-hidden
                    bg-white
                    flex
                    items-center
                    justify-center
                ">

        @if(file_exists(public_path('images/ckc-logo.png')))

          <img src="{{ asset('images/ckc-logo.png') }}" alt="Christ the King College" class="w-8 h-8 object-contain">

        @else

          {{-- Fallback if logo does not exist --}}
          <div class="
                              w-full
                              h-full
                              flex
                              items-center
                              justify-center
                              bg-white
                          ">
            <span class="
                                  text-[10px]
                                  font-bold
                                  text-blue-900
                              ">
              CKC
            </span>
          </div>

        @endif

      </div>


      {{-- Brand Text --}}
      <div class="sg-brand-text min-w-0">

        <p class="
                        font-display
                        font-bold
                        text-sm
                        leading-tight
                        truncate
                    ">
          CKC GradeHub
        </p>

        <p class="
                        text-[11px]
                        text-white/50
                        leading-tight
                        truncate
                    ">
          Christ the King College
        </p>

      </div>

    </a>


    {{-- Mobile Close --}}
    <button type="button" data-sidebar-close class="
                lg:hidden
                text-white/70
                hover:text-white
                transition
            " aria-label="Close sidebar">

      <i data-lucide="x" class="w-5 h-5"></i>

    </button>

  </div>


  {{-- ========================================================
  NAVIGATION
  ========================================================= --}}

  <nav class="
            flex-1
            min-h-0
            overflow-y-auto
            py-4
            px-3
            space-y-1
            sg-scrollbar-thin
        ">

    <p class="
                sg-label
                px-3
                text-[11px]
                font-semibold
                uppercase
                tracking-wider
                text-white/35
                mb-2
            ">
      {{ $roleLabel }} Menu
    </p>


    @foreach ($items as $item)

      <a href="{{ route($item['route']) }}" class="
                      sg-nav-link
                      flex
                      items-center
                      gap-3
                      px-3
                      py-2.5
                      text-sm
                      font-medium
                      transition
                      {{ $active === $item['key'] ? 'active' : '' }}
                  ">

        <i data-lucide="{{ $item['icon'] }}" class="
                          sg-nav-icon
                          w-[18px]
                          h-[18px]
                          flex-shrink-0
                      "></i>

        <span class="sg-label truncate">
          {{ $item['label'] }}
        </span>

      </a>

    @endforeach

  </nav>


  {{-- ========================================================
  PROFILE + LOGOUT
  ========================================================= --}}

  <div class="
            border-t
            border-white/10
            p-3
            space-y-1
            flex-shrink-0
        ">

    {{-- Profile --}}
    <a href="{{ route($profileRoute) }}" class="
                sg-nav-link
                flex
                items-center
                gap-3
                px-3
                py-2.5
                text-sm
                font-medium
                {{ $active === 'profile' ? 'active' : '' }}
            ">

      <i data-lucide="user-circle" class="
                    sg-nav-icon
                    w-[18px]
                    h-[18px]
                    flex-shrink-0
                "></i>

      <span class="sg-label truncate">
        Profile
      </span>

    </a>


    {{-- Logout --}}
    <button type="button" data-logout class="
                sg-nav-link
                w-full
                flex
                items-center
                gap-3
                px-3
                py-2.5
                text-sm
                font-medium
                text-left
            " style="color:#f3b4b4">

      <i data-lucide="log-out" class="w-[18px] h-[18px] flex-shrink-0"></i>

      <span class="sg-label truncate">
        Logout
      </span>

    </button>

  </div>

</aside>