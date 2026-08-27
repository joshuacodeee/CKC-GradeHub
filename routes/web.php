<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminSubjectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');
Route::get('/home', fn() => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [AdminUserController::class, 'students'])->name('students');
        Route::post('/students', [AdminUserController::class, 'storeStudent'])->name('students.store');
        Route::get('/teachers', [AdminUserController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [AdminUserController::class, 'storeTeacher'])->name('teachers.store');
        Route::get('/subjects', [AdminSubjectController::class, 'index'])->name('subjects');
        Route::post('/subjects', [AdminSubjectController::class, 'store'])->name('subjects.store');
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
