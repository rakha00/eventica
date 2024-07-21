<?php

use Livewire\Volt\Component;
use App\Models\EventCategory;
use App\Models\Event;

new class extends Component {
    public $categories;
    public $events;
    public $search;

    public function mount()
    {
        $this->categories = EventCategory::all();
        $this->events = Event::take(5)->get();
    }

    public function updatedSearch()
    {
        $this->events = Event::where('title', 'like', $this->search . '%')
            ->where('status', 'published')
            ->take(5)
            ->get();
    }
}; ?>

<div class="mx-auto max-w-7xl">
    <div class="flex flex-wrap items-center">
        <label class="sr-only mb-2 text-sm font-medium text-gray-900 dark:text-white" for="search-dropdown">Category</label>
        <div class="relative flex items-center">
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
                            <a class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                href="{{ route('search', ['selectedCategory' => $category->id]) }}">{{ $category->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="relative mt-2 flex-grow sm:mt-0">
            <input
                class="z-20 block w-full rounded-lg border border-primary bg-gray-50 p-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 dark:border-secondary dark:bg-gray-900 dark:text-white dark:placeholder-gray-400 sm:rounded-s-none"
                id="search-dropdown" type="search" placeholder="Search Events..." wire:model.live.debounce.500ms="search" />
            @if (!empty($search))
                <ul class="absolute z-10 w-full border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                    @foreach ($events as $event)
                        <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            <a href="{{ route('event-detail', $event->slug) }}" wire:navigate>{{ $event->title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <button
                class="absolute end-0 top-0 h-full rounded-e-lg border border-primary bg-primary p-2.5 text-sm font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-0 dark:border-secondary dark:bg-secondary dark:text-gray-950 dark:hover:bg-primary/80"
                type="submit">
                <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                <span class="sr-only">Search</span>
            </button>
        </div>
    </div>
</div>
