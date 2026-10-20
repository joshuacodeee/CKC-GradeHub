<?php

it('renders role-specific sidebar navigation', function () {
    $studentSidebar = view('components.sidebar', [
        'role' => 'student',
        'active' => 'dashboard',
    ])->render();

    expect($studentSidebar)
        ->toContain('My Grades')
        ->toContain('Academic Records')
        ->not->toContain('Grade Reports');

    $registrarSidebar = view('components.sidebar', [
        'role' => 'registrar',
        'active' => 'students',
    ])->render();

    expect($registrarSidebar)
        ->toContain('Grade Reports')
        ->toContain('Registrar Menu');
});
