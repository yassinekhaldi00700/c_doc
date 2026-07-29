<?php

use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Candidate\DashboardController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\SubjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('candidate')
    ->name('candidate.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        // Subject browsing is open to any authenticated role (professors and
        // admins need a way to see the same public subject listing/details a
        // candidate sees, e.g. to check other departments' open subjects).
        // Only the actual apply/profile/application flow below is
        // candidate-only.
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');

        Route::middleware('role:candidate')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/subjects/{subject}/apply', [ApplicationController::class, 'create'])
                ->middleware('profile.complete')->name('applications.create');
            Route::post('/subjects/{subject}/apply', [ApplicationController::class, 'store'])
                ->middleware('profile.complete')->name('applications.store');

            Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
            Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');

            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [ProfileController::class, 'overview'])->name('overview');
                Route::get('/personal', [ProfileController::class, 'editPersonal'])->name('personal.edit');
                Route::post('/personal', [ProfileController::class, 'updatePersonal'])->name('personal.update');
                Route::get('/academic', [ProfileController::class, 'editAcademic'])->name('academic.edit');
                Route::post('/academic', [ProfileController::class, 'updateAcademic'])->name('academic.update');
                Route::delete('/documents/{document}', [ProfileController::class, 'destroyDocument'])->name('documents.destroy');
                Route::get('/documents/{document}/preview', [ProfileController::class, 'previewDocument'])->name('documents.preview');
            });
        });
    });
