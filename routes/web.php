<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminSectionController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminSubjectController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
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
        Route::get('/sections', [AdminSectionController::class, 'index'])->name('sections');
        Route::get('/sections/create', [AdminSectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [AdminSectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}/edit', [AdminSectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [AdminSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [AdminSectionController::class, 'destroy'])->name('sections.destroy');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/settings/school-years', [AdminSettingController::class, 'storeSchoolYear'])->name('school-years.store');
        Route::put('/settings/school-years/{schoolYear}/activate', [AdminSettingController::class, 'activateSchoolYear'])->name('school-years.activate');
        Route::get('/profile', [AdminProfileController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/classes', [TeacherController::class, 'classes'])->name('classes');
        Route::get('/subjects', [TeacherController::class, 'subjects'])->name('subjects');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::get('/grades', [TeacherController::class, 'grades'])->name('grades');
        Route::get('/grades/{teacherSubject}', [TeacherController::class, 'enterGrades'])->name('grades.enter');
        Route::post('/grades/{teacherSubject}/save', [TeacherController::class, 'saveGrades'])->name('grades.save');
        Route::get('/reports', [TeacherController::class, 'reports'])->name('reports');
        Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
    });

    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/grades', [StudentController::class, 'grades'])->name('grades');
        Route::get('/subjects', [StudentController::class, 'subjects'])->name('subjects');
        Route::get('/records', [StudentController::class, 'records'])->name('records');
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    });
});
