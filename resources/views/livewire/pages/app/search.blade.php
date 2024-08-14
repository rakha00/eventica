<?php

use function Livewire\Volt\layout;

layout('layouts.app');
?>

<div>
    <livewire:layout.header />

    <main>
        <livewire:search.search-events />
    </main>

    <livewire:layout.footer />
</div>
