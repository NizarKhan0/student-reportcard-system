<?php

use App\Http\Controllers\StudentDetailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassFormController;
use App\Http\Controllers\ExamController;

Route::view('/', 'welcome');

Route::get('classforms', [ClassFormController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('classforms');

Route::get('subjects', [SubjectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('subjects');

Route::get('streams', [StreamController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('streams');

Route::get('students', [StudentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('students');

Route::get('exams', [ExamController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('exams');

    Route::get('studentdetails', [StudentDetailController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('studentdetails');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';
