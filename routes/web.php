<?php

use App\Http\Controllers\ClassFormController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('classforms', [ClassFormController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('classforms');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
