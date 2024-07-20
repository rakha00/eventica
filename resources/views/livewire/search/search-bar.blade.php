<?php

use Livewire\Volt\Component;
use App\Models\EventCategory;

new class extends Component {
    public $categories;

    public function mount()
    {
        $this->categories = EventCategory::all();
    }
}; ?>

<form class="mx-auto max-w-7xl">
    <div class="flex">
        <label class="sr-only mb-2 text-sm font-medium text-gray-900 dark:text-white" for="search-dropdown">Category</label>
        <button
            class="z-10 hidden flex-shrink-0 items-center rounded-s-lg border border-primary bg-primary px-4 py-2.5 text-center text-sm font-medium text-white hover:bg-primary/90 dark:border-secondary dark:bg-secondary dark:text-black dark:hover:bg-secondary/90 sm:inline-flex"
            id="dropdown-button" data-dropdown-toggle="dropdown" type="button">
            All categories
            <x-heroicon-s-chevron-down class="ms-2 h-4 w-4" />
        </button>
        <div class="z-50 hidden w-44 divide-y divide-gray-100 rounded-lg border border-primary bg-white shadow dark:border-secondary dark:bg-gray-900" id="dropdown">
            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdown-button">
                @foreach ($categories as $category)
                    <li>
                        <button class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white" type="button">{{ $category->title }}</button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="relative w-full">
            <input
                class="z-20 block w-full rounded-e-lg rounded-s-lg border border-s-2 border-primary border-s-primary bg-gray-50 p-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 dark:border-secondary dark:border-s-secondary dark:bg-gray-900 dark:text-white dark:placeholder-gray-400 sm:rounded-s-none"
                id="search-dropdown" type="search" placeholder="Search Events, Categories, Locations..." required />
            <button
                class="absolute end-0 top-0 h-full rounded-e-lg border border-primary bg-primary p-2.5 text-sm font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-0 dark:border-secondary dark:bg-secondary dark:text-gray-950 dark:hover:bg-primary/80"
                type="submit">
                <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                <span class="sr-only">Search</span>
            </button>
        </div>
    </div>
</form>
