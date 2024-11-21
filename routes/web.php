<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('devices', 'devices')
    ->middleware(['auth'])
    ->name('devices');

Route::view('customers', 'customers')
    ->middleware(['auth'])
    ->name('customers');

Route::view('test', 'test')
    ->name('test');

require __DIR__.'/auth.php';
