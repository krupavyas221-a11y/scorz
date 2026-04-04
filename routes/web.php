<?php

use App\Http\Controllers\Auth\ForgotPasswordController as SchoolForgotPasswordController;
use App\Http\Controllers\Auth\LoginController as SchoolLoginController;
use App\Http\Controllers\Auth\ResetPasswordController as SchoolResetPasswordController;
use App\Http\Controllers\Auth\ResetPinController;
use App\Http\Controllers\SchoolAdmin\DashboardController as SchoolAdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\SuperAdmin\Auth\ForgotPasswordController;
use App\Http\Controllers\SuperAdmin\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\Auth\ResetPasswordController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\SchoolAdminController;
use App\Http\Controllers\SuperAdmin\PupilController;
use App\Http\Controllers\SuperAdmin\ClassController;
use App\Http\Controllers\SuperAdmin\MasterData\SubjectController;
use App\Http\Controllers\SuperAdmin\MasterData\StrandController;
use App\Http\Controllers\SuperAdmin\MasterData\SkillCategoryController;
use App\Http\Controllers\SuperAdmin\MasterData\TestTypeController;
use App\Http\Controllers\SuperAdmin\MasterData\SeasonController;
use App\Http\Controllers\SuperAdmin\MasterData\TestLevelController;
use App\Http\Controllers\SuperAdmin\SchoolYearController;
use App\Http\Controllers\SuperAdmin\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| School Portal — Authentication (common for School Admin + Teacher)
|--------------------------------------------------------------------------
*/

// Guest-only: redirect authenticated school users to their dashboard
Route::middleware('guest:school')->group(function () {

    // Step 1 — Role selection
    Route::get('login',       [SchoolLoginController::class, 'showRoleSelect'])->name('login');
    Route::post('login/role', [SchoolLoginController::class, 'selectRole'])->name('login.role');

    // Step 2 — Credentials
    Route::get('login/credentials',  [SchoolLoginController::class, 'showCredentials'])->name('login.credentials');
    Route::post('login/credentials', [SchoolLoginController::class, 'submitCredentials'])->name('login.credentials');

    // Step 3 — PIN
    Route::get('login/pin',  [SchoolLoginController::class, 'showPin'])->name('login.pin');
    Route::post('login/pin', [SchoolLoginController::class, 'submitPin'])->name('login.pin');

    // Forgot / Reset Password
    Route::get('forgot-password',  [SchoolForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('forgot-password', [SchoolForgotPasswordController::class, 'send'])->name('password.email');

    Route::get('reset-password/{token}', [SchoolResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('reset-password',        [SchoolResetPasswordController::class, 'reset'])->name('password.update');

    // Request PIN reset (accessible without being logged in)
    Route::get('reset-pin',         [ResetPinController::class, 'showRequest'])->name('pin.reset.request');
    Route::post('reset-pin',        [ResetPinController::class, 'sendLink'])->name('pin.reset.send');
    Route::get('reset-pin/{token}', [ResetPinController::class, 'showForm'])->name('pin.reset.form');
    Route::post('reset-pin/update', [ResetPinController::class, 'update'])->name('pin.reset.update');
});

// Authenticated school routes
Route::middleware('auth:school')->group(function () {
    Route::post('logout', [SchoolLoginController::class, 'logout'])->name('logout');

    // School Admin dashboard
    Route::get('school/dashboard', [SchoolAdminDashboardController::class, 'index'])
         ->name('school-admin.dashboard');

    // Teacher dashboard
    Route::get('teacher/dashboard', [TeacherDashboardController::class, 'index'])
         ->name('teacher.dashboard');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    // Guest-only routes (redirect to dashboard if already logged in)
    Route::middleware('guest:superadmin')->group(function () {
        Route::get('login',           [LoginController::class, 'show'])->name('login');
        Route::post('login',          [LoginController::class, 'login']);

        Route::get('forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
        Route::post('forgot-password',[ForgotPasswordController::class, 'send'])->name('password.email');

        Route::get('reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
        Route::post('reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    // Authenticated routes
    Route::middleware('auth:superadmin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout',   [LoginController::class, 'logout'])->name('logout');

        // School Admin Management
        Route::resource('school-admins', SchoolAdminController::class)
             ->parameters(['school-admins' => 'schoolAdmin'])
             ->except(['destroy', 'edit']);
        Route::patch('school-admins/{schoolAdmin}/toggle-status', [SchoolAdminController::class, 'toggleStatus'])
             ->name('school-admins.toggle-status');

        // Teacher Management
        Route::resource('teachers', TeacherController::class)
             ->except(['show']);
        Route::patch('teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])
             ->name('teachers.toggle-status');

        // ── Master Data ──────────────────────────────────────────────────────
        foreach ([
            'subjects'         => SubjectController::class,
            'strands'          => StrandController::class,
            'skill-categories' => SkillCategoryController::class,
            'test-types'       => TestTypeController::class,
            'seasons'          => SeasonController::class,
            'test-levels'      => TestLevelController::class,
        ] as $uri => $ctrl) {
            $param = str_replace('-', '_', $uri); // e.g. skill_categories
            Route::get("{$uri}/export-csv", [$ctrl, 'exportCsv'])->name("{$uri}.export-csv");
            Route::get("{$uri}/export-pdf", [$ctrl, 'exportPdf'])->name("{$uri}.export-pdf");
            Route::resource($uri, $ctrl)
                 ->parameters([$uri => 'entry'])
                 ->except(['show', 'create', 'edit']);
            Route::patch("{$uri}/{entry}/toggle-status", [$ctrl, 'toggleStatus'])
                 ->name("{$uri}.toggle-status")
                 ->where('entry', '[0-9]+');
        }

        // Classes Management
        Route::resource('classes', ClassController::class)
             ->except(['show']);
        Route::patch('classes/{class}/toggle-status', [ClassController::class, 'toggleStatus'])
             ->name('classes.toggle-status');

        // School Years Management
        Route::resource('school-years', SchoolYearController::class)
             ->except(['show']);
        Route::patch('school-years/{schoolYear}/toggle-status', [SchoolYearController::class, 'toggleStatus'])
             ->name('school-years.toggle-status');
        Route::patch('school-years/{schoolYear}/assign-schools', [SchoolYearController::class, 'assignSchools'])
             ->name('school-years.assign-schools');

        // Pupils Management
        Route::get('pupils/export-csv', [PupilController::class, 'exportCsv'])->name('pupils.export-csv');
        Route::get('pupils/print',      [PupilController::class, 'print'])->name('pupils.print');
        Route::resource('pupils', PupilController::class)->except(['show']);
        Route::patch('pupils/{pupil}/toggle-status', [PupilController::class, 'toggleStatus'])
             ->name('pupils.toggle-status');
    });
});
