<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\BackgroundJobController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (Auth::user()->hasRole(config('constants.role.admin'))) return (new BackgroundJobController)->index();
    else return (new AssignmentController)->index();
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
});

Route::middleware('admin')->group(function () {
    Route::post('/background-jobs/start/{backgroundJob}', [BackgroundJobController::class, 'startJob'])->name('background_jobs.start');
    Route::post('/background-jobs/cancel/{backgroundJob}', [BackgroundJobController::class, 'cancelJob'])->name('background_jobs.cancel');
    Route::post('/background-jobs/rerun/{backgroundJob}', [BackgroundJobController::class, 'reRunJob'])->name('background_jobs.rerun');
    Route::get('/background-jobs', [BackgroundJobController::class, 'index'])->name('background_jobs.index');
});

require __DIR__.'/auth.php';
