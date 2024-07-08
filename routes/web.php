<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/profile', [HomeController::class, 'profile'])->middleware('auth')->name('profile');
Route::get('/event-detail/{eventSlug}', [HomeController::class, 'eventDetail'])->name('event-detail');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/auth.php';