<?php

use function Livewire\Volt\layout;

layout('layouts.app');
?>
<div>
    <livewire:layout.header />
    <div class="container mx-auto min-h-screen px-4 py-8">
        <h1 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">About Us</h1>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
            Welcome to HIMTicket! We are dedicated to providing you with the best event ticketing experience. Our platform offers a wide range of events to choose from, ensuring that you find the
            perfect
            event to enjoy.
        </p>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
            Our mission is to make event discovery and ticket purchasing as seamless as possible. We work tirelessly to bring you the latest and most exciting events happening around you.
        </p>
        <p class="text-gray-700 dark:text-gray-300">
            Thank you for choosing HIMTicket. We hope you have a great time at your next event!
        </p>
    </div>
    <livewire:layout.footer />
</div>
