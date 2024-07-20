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

        <div class="px-4">
            <section class="mx-auto mt-4 max-w-7xl">
                <livewire:search.search-events />
            </section>
        </div>
    </main>

    <livewire:layout.footer />
</div>
