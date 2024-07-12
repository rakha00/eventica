<?php

use function Livewire\Volt\layout;

layout('layouts.app');
?>

@push('scripts')
    @vite(['resources/js/swiper.js'])
@endpush

<div>
    <livewire:layout.header />

    <main>
        <div class="px-4">
            <section class="mt-6">
                <livewire:search.search-bar />
            </section>
        </div>

        <section class="mx-auto mt-4 max-w-7xl">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-6xl">Eventica</h1>
            <p class="font-mono italic text-gray-900 dark:text-white sm:text-xl">Where Events Begins</p>
            <x-button>
                All Events
            </x-button>
        </section>

        <section class="my-2">
            <livewire:home.carousel />
        </section>

        <section class="mx-auto mt-4 max-w-7xl">
            <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white md:text-4xl">Popular Events</h2>
            <livewire:home.upcoming-events />
        </section>

        <section class="mx-auto mb-6 mt-4 max-w-7xl">
            <div class="flex items-center justify-between">
                <h2 class="mb-4 text-2xl font-bold text-gray-900 dark:text-white md:text-4xl">Blogs</h2>
                <p class="text-xl text-gray-900 dark:text-white dark:hover:text-gray-200">See More</p>
            </div>
            <livewire:home.blogs />
        </section>
    </main>

    <livewire:layout.footer />
</div>