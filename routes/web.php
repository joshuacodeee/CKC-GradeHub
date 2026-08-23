<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/home', fn () => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
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

    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::view('/dashboard', 'teacher.dashboard')->name('dashboard');
        Route::view('/classes', 'teacher.classes')->name('classes');
        Route::view('/subjects', 'teacher.subjects')->name('subjects');
        Route::view('/students', 'teacher.students')->name('students');
        Route::view('/grades', 'teacher.grades')->name('grades');
        Route::view('/reports', 'teacher.reports')->name('reports');
        Route::view('/profile', 'teacher.profile')->name('profile');
    });

    Route::prefix('student')->name('student.')->group(function () {
        Route::view('/dashboard', 'student.dashboard')->name('dashboard');
        Route::view('/grades', 'student.grades')->name('grades');
        Route::view('/subjects', 'student.subjects')->name('subjects');
        Route::view('/records', 'student.records')->name('records');
        Route::view('/profile', 'student.profile')->name('profile');
    });
});
