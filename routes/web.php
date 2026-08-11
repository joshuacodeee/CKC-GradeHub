<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — FRONTEND ONLY
|--------------------------------------------------------------------------
| Every route below simply returns a Blade view. There is no controller
| logic, no database access, and no authentication middleware, because
| this project is a static/mock-data UI prototype. Real auth/route
| guarding can be layered on later without changing any view markup.
*/

Route::redirect('/', '/login');

// Auth (simulated on the frontend via localStorage — see public/js/app.js)
Route::view('/login', 'auth.login')->name('login');

// Administrator
Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/students', 'admin.students')->name('students');
    Route::view('/teachers', 'admin.teachers')->name('teachers');
    Route::view('/subjects', 'admin.subjects')->name('subjects');
    Route::view('/sections', 'admin.sections')->name('sections');
    Route::view('/reports', 'admin.reports')->name('reports');
    Route::view('/settings', 'admin.settings')->name('settings');
    Route::view('/profile', 'admin.profile')->name('profile');
});

// Teacher
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::view('/dashboard', 'teacher.dashboard')->name('dashboard');
    Route::view('/classes', 'teacher.classes')->name('classes');
    Route::view('/subjects', 'teacher.subjects')->name('subjects');
    Route::view('/students', 'teacher.students')->name('students');
    Route::view('/grades', 'teacher.grades')->name('grades');
    Route::view('/reports', 'teacher.reports')->name('reports');
    Route::view('/profile', 'teacher.profile')->name('profile');
});

// Student
Route::prefix('student')->name('student.')->group(function () {
    Route::view('/dashboard', 'student.dashboard')->name('dashboard');
    Route::view('/grades', 'student.grades')->name('grades');
    Route::view('/subjects', 'student.subjects')->name('subjects');
    Route::view('/records', 'student.records')->name('records');
    Route::view('/profile', 'student.profile')->name('profile');
});
