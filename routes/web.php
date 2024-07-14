<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Volt::route('/', 'pages.app.index')->name('home');
Volt::route('/event/{eventSlug}', 'pages.app.event-detail')->name('event-detail');

Route::middleware(['auth'])->group(function () {
    Volt::route('/profile', 'pages.auth.profile')->name('profile');
    Volt::route('/tickets', 'pages.app.tickets')->name('tickets');


    Volt::route('/book/{eventSlug}/{packageSlug}', 'pages.transaction.detail')->name('transaction-detail');
    Volt::route('/book/{eventSlug}/{packageSlug}/{orderId}', 'pages.transaction.contact')->name('transaction-contact');
    Volt::route('/payment/{orderId}', 'pages.transaction.payment')->name('trasaction-payment');
});

require __DIR__ . '/auth.php';