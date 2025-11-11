<?php

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplicationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('admin')->get('/admin', function () {
        return 'Admin only page';
    });

    Route::middleware('applicant')->get('/applicant', function () {
        return 'Applicant only page';
    });

    Route::post('/apply/{job_id}', [ApplicationController::class, 'store'])->name('apply')->middleware('applicant');

    Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve'])->name('approve')->middleware('admin');
    Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject'])->name('reject')->middleware('admin');
    Route::get('/export/applications', [ApplicationController::class, 'export'])->name('export.applications')->middleware('admin');

    Route::resource('jobs', \App\Http\Controllers\JobController::class)->middleware('admin');
    Route::get('/jobs/template', [\App\Http\Controllers\JobController::class, 'template'])->name('jobs.template')->middleware('admin');
    Route::post('/jobs/import', [\App\Http\Controllers\JobController::class, 'import'])->name('jobs.import')->middleware('admin');
});
