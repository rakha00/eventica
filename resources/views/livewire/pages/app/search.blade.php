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
        <livewire:search.search-events />
    </main>

    <livewire:layout.footer />
</div>
