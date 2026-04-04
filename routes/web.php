<?php

use App\Http\Controllers\SuperAdmin\Auth\ForgotPasswordController;
use App\Http\Controllers\SuperAdmin\Auth\LoginController;
use App\Http\Controllers\SuperAdmin\Auth\ResetPasswordController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\SchoolAdminController;
use App\Http\Controllers\SuperAdmin\PupilController;
use App\Http\Controllers\SuperAdmin\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

        // Pupils Management
        Route::get('pupils/export-csv', [PupilController::class, 'exportCsv'])->name('pupils.export-csv');
        Route::get('pupils/print',      [PupilController::class, 'print'])->name('pupils.print');
        Route::resource('pupils', PupilController::class)->except(['show']);
        Route::patch('pupils/{pupil}/toggle-status', [PupilController::class, 'toggleStatus'])
             ->name('pupils.toggle-status');
    });
});
