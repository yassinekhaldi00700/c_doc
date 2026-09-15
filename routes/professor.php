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

        Route::get('/recruitment', [\App\Http\Controllers\Professor\RecruitmentReportController::class, 'index'])->name('recruitment.index');
        Route::get('/subjects/{subject}/recruitment', [\App\Http\Controllers\Professor\RecruitmentReportController::class, 'edit'])->name('recruitment.edit');
        Route::post('/subjects/{subject}/recruitment', [\App\Http\Controllers\Professor\RecruitmentReportController::class, 'update'])->name('recruitment.update');

        Route::resource('subjects', SubjectController::class)->except(['show', 'create', 'store']);
        Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');
        Route::patch('/subjects/{subject}/toggle-open', [SubjectController::class, 'toggleOpen'])->name('subjects.toggle-open');

        Route::get('/oral-exam-picks', [\App\Http\Controllers\Professor\OralExamPickController::class, 'index'])->name('oral-exam-picks.index');
        Route::get('/subjects/{subject}/oral-exam-picks', [\App\Http\Controllers\Professor\OralExamPickController::class, 'edit'])->name('oral-exam-picks.edit');
        Route::post('/subjects/{subject}/oral-exam-picks', [\App\Http\Controllers\Professor\OralExamPickController::class, 'update'])->name('oral-exam-picks.update');

        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    });
