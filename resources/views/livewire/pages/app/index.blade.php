<?php

use function Livewire\Volt\layout;

layout('layouts.app');
?>

<div>
    <livewire:layout.header />

    <main>
        <div class="px-4">
            <section class="mt-6">
                <livewire:search.search-bar />
            </section>
        </div>

        <div class="px-4">
            <section class="mx-auto mt-4 max-w-7xl">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-6xl">HIMTicket</h1>
                <p class="font-mono italic text-gray-900 dark:text-white sm:text-xl">Find your joy here</p>
                <x-button class="mt-4" href="{{ route('search') }}" wire:navigate>
                    All Events
                </x-button>
            </section>

            <section class="my-2">
                <livewire:home.carousel />
            </section>

            <section class="mx-auto mt-4 max-w-7xl">
                <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white md:text-4xl">Upcoming Events</h2>
                <livewire:home.upcoming-events />
            </section>
        </div>
    </main>

    <livewire:layout.footer />
</div>
