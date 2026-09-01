<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminSectionController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminSubjectController;
use App\Http\Controllers\AdminTeacherAssignmentController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/home', fn() => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/read', [NotificationController::class, 'markRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
    Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
});

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Students
        Route::get('/students', [AdminUserController::class, 'students'])->name('students');
        Route::post('/students', [AdminUserController::class, 'storeStudent'])->name('students.store');

        // Teachers
        Route::get('/teachers', [AdminUserController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [AdminUserController::class, 'storeTeacher'])->name('teachers.store');

        // Subjects — full CRUD
        Route::get('/subjects', [AdminSubjectController::class, 'index'])->name('subjects');
        Route::post('/subjects', [AdminSubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{subject}', [AdminSubjectController::class, 'show'])->name('subjects.show');
        Route::get('/subjects/{subject}/edit', [AdminSubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [AdminSubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [AdminSubjectController::class, 'destroy'])->name('subjects.destroy');
        Route::put('/subjects/{subject}/activate', [AdminSubjectController::class, 'activate'])->name('subjects.activate');

        // Sections
        Route::get('/sections', [AdminSectionController::class, 'index'])->name('sections');
        Route::get('/sections/create', [AdminSectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [AdminSectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}/edit', [AdminSectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [AdminSectionController::class, 'update'])->name('sections.update');
        Route::post('/sections/{section}/sync', [AdminSectionController::class, 'sync'])->name('sections.sync');
        Route::delete('/sections/{section}', [AdminSectionController::class, 'destroy'])->name('sections.destroy');

        // School years (under settings)
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');
        Route::post('/settings/school-years', [AdminSettingController::class, 'storeSchoolYear'])->name('school-years.store');
        Route::put('/settings/school-years/{schoolYear}/activate', [AdminSettingController::class, 'activateSchoolYear'])->name('school-years.activate');
        Route::put('/settings/school-info', [AdminSettingController::class, 'updateSchoolInfo'])->name('school-info.update');
        Route::put('/settings/grading-scale', [AdminSettingController::class, 'updateGradingScale'])->name('grading-scale.update');

        // Teacher Assignments (spec §11)
        Route::get('/assignments', [AdminTeacherAssignmentController::class, 'index'])->name('assignments');
        Route::get('/assignments/create', [AdminTeacherAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [AdminTeacherAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}/edit', [AdminTeacherAssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{assignment}', [AdminTeacherAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [AdminTeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

        // Reports & profile
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::get('/reports/{assignment}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::get('/profile', [AdminProfileController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/classes', [TeacherController::class, 'classes'])->name('classes');
        Route::get('/subjects', [TeacherController::class, 'subjects'])->name('subjects');
        Route::get('/sections', [TeacherController::class, 'sections'])->name('sections');
        Route::get('/students', [TeacherController::class, 'students'])->name('students');
        Route::post('/students', [TeacherController::class, 'storeStudent'])->name('students.store');
        Route::get('/grades', [TeacherController::class, 'grades'])->name('grades');
        Route::get('/grades/{teacherSubject}', [TeacherController::class, 'enterGrades'])->name('grades.enter');
        Route::post('/grades/{teacherSubject}/save', [TeacherController::class, 'saveGrades'])->name('grades.save');
        Route::get('/reports', [TeacherController::class, 'reports'])->name('reports');
        Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
        Route::put('/profile', [TeacherController::class, 'updateProfile'])->name('profile.update');
    });

    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/grades', [StudentController::class, 'grades'])->name('grades');
        Route::get('/subjects', [StudentController::class, 'subjects'])->name('subjects');
        Route::get('/records', [StudentController::class, 'records'])->name('records');
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
    });
});
