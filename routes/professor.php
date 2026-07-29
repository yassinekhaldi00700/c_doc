<?php

use App\Http\Controllers\Professor\ApplicationController;
use App\Http\Controllers\Professor\DashboardController;
use App\Http\Controllers\Professor\SubjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('professor')
    ->name('professor.')
    ->middleware(['auth', 'verified', 'role:professor'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('subjects', SubjectController::class)->except(['show', 'create', 'store']);
        Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
        Route::patch('/subjects/{subject}/toggle-open', [SubjectController::class, 'toggleOpen'])->name('subjects.toggle-open');

        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    });
