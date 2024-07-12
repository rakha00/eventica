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
        <div class="mx-auto mb-10 mt-4 max-w-7xl space-y-2 px-6">
            <x-breadcrumb />
            <livewire:event.detail />
        </div>
    </main>

    <livewire:layout.footer />
</div>
