<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.app.index')->name('home');
Volt::route('/search', 'pages.app.search')->name('search');
Volt::route('/about', 'pages.app.about')->name('about');
Volt::route('/event/{eventSlug}', 'pages.app.event-detail')->name('event-detail');
Volt::route('/how-to-order', 'pages.app.how-to-order')->name('how-to-order');

Route::middleware(['auth'])->group(function () {
    Volt::route('/profile', 'pages.auth.profile')->name('profile');
    Volt::route('/tickets', 'pages.app.tickets')->name('tickets');


    Volt::route('/book/{eventSlug}/{packageSlug}', 'pages.transaction.detail')->name('transaction-detail');
    Volt::route('/book/{eventSlug}/{packageSlug}/{orderId}', 'pages.transaction.contact')->name('transaction-contact');
    Volt::route('/payment/{orderId}', 'pages.transaction.payment')->name('transaction-payment');
});

require __DIR__ . '/auth.php';
