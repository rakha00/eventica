<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/event/{eventSlug}', [HomeController::class, 'eventDetail'])->name('event-detail');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/book/{eventSlug}/{packageSlug}', [TransactionController::class, 'bookDetail'])->name('book-detail');
    Route::get('/book/{eventSlug}/{packageSlug}/{orderId}', [TransactionController::class, 'bookContact'])->name('book-contact');
    Route::get('/payment/{orderId}', [TransactionController::class, 'bookPayment'])->name('book-payment');
});

require __DIR__ . '/auth.php';
