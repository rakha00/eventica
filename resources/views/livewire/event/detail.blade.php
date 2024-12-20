<?php

use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
    public $event;

    public function mount()
    {
        $this->event = Event::where('slug', request()->eventSlug)
            ->where('status', 'published')
            ->with('eventPackages')
            ->first();
        $this->event->lowest_price = $this->event->eventPackages->min('price');
    }
}; ?>

<div>
    <div class="flex justify-center">
        <img class="aspect-video w-full max-w-[1000px] rounded-lg" src="{{ asset('storage/' . $event->image) }}" alt="Event {{ $event->title }}">
    </div>
    <div class="mb-4 border-b border-primary dark:border-secondary">
        <!-- Nav Tabs -->
        <ul class="-mb-px flex overflow-x-scroll text-center text-sm font-medium sm:justify-center sm:overflow-auto" id="default-tab" data-tabs-toggle="#default-tab-content"
            data-tabs-active-classes="text-primary hover:text-primary dark:text-secondary dark:hover:text-secondary border-primary dark:border-secondary"
            data-tabs-inactive-classes="dark:border-transparent text-gray-800 hover:text-primary/80 dark:text-gray-200 border-transparent hover:border-primary dark:border-gray-700 dark:hover:text-secondary/90">
            <li class="me-2">
                <button class="inline-block rounded-t-lg border-b-2 p-4 sm:text-lg" id="summary-tab" data-tabs-target="#summary" type="button" role="tab" aria-controls="summary"
                    aria-selected="false">Summary</button>
            </li>
            <li class="me-2">
                <button class="inline-block rounded-t-lg border-b-2 p-4 hover:text-primary/80 dark:hover:text-secondary/90 sm:text-lg" id="package-tab" data-tabs-target="#package" type="button"
                    role="tab" aria-controls="package" aria-selected="false">Package</button>
            </li>
            <li class="me-2">
                <button class="inline-block rounded-t-lg border-b-2 p-4 hover:text-primary/80 dark:hover:text-secondary/90 sm:text-lg" id="location-tab" data-tabs-target="#location" type="button"
                    role="tab" aria-controls="location" aria-selected="false">Location</button>
            </li>
            <li>
                <button class="inline-block rounded-t-lg border-b-2 p-4 hover:text-primary/80 dark:hover:text-secondary/90 sm:text-lg" id="upcoming-tab" data-tabs-target="#upcoming" type="button"
                    role="tab" aria-controls="upcoming" aria-selected="false">Upcoming</button>
            </li>
        </ul>
    </div>
    <div id="default-tab-content">
        <!-- Summary -->
        <div class="hidden rounded-lg bg-gray-50 p-4 shadow-lg dark:bg-gray-800 dark:shadow-slate-500" id="summary" role="tabpanel" aria-labelledby="summary-tab">
            <div class="rounded-md bg-gray-100 p-6 shadow-inner shadow-slate-700 dark:bg-slate-700 dark:shadow-slate-500">
                <div class="mb-2 flex" x-data="{ showTime: false }">
                    <x-heroicon-o-map-pin class="size-4 sm:size-5 text-gray-800 dark:text-gray-200" />
                    <p class="ms-2 inline-flex items-center text-xs text-gray-800 dark:text-gray-200 sm:text-base">{{ $event->location }} |
                        <span class="ms-2 inline-flex cursor-pointer items-center text-primary dark:text-secondary"
                            x-on:click="showTime = !showTime">{{ \Carbon\Carbon::parse($event->start_event)->format('d M Y') }}
                            <span class="ms-2 text-xs text-primary dark:text-secondary sm:text-base" x-show="showTime" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform -translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0"
                                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-x-0"
                                x-transition:leave-end="opacity-0 transform -translate-x-10">{{ \Carbon\Carbon::parse($event->start_event)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($event->end_event)->format('d M Y H:i') }}</span>
                            <x-heroicon-s-chevron-double-right class="size-4 ms-2" x-show="!showTime" />
                            <x-heroicon-s-chevron-double-left class="size-4 ms-2" x-show="showTime" />
                        </span>
                    </p>
                </div>

                <div>
                    <h1 class="mb-2 line-clamp-1 text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $event->title }}</h1>
                </div>
                <div class="mb-2 flex flex-col justify-between gap-2 sm:flex-row">
                    <div class="basis-4/6 text-gray-800 dark:text-gray-200">
                        {!! $event->description !!}
                    </div>
                    <div class="basis-auto text-end">
                        <p class="text-gray-800 dark:text-gray-200">Starting From</p>
                        <p class="font-bold text-primary dark:text-secondary md:text-xl">{{ 'Rp ' . number_format($event->lowest_price, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="flex flex-col">
                    <h2 class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">Highlight</h2>
                    <div class="prose text-gray-800 dark:text-gray-200">
                        {!! $event->highlight !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- Packages -->
        <div class="hidden rounded-lg bg-gray-50 p-4 shadow-lg dark:bg-gray-800 dark:shadow-slate-500" id="package" role="tabpanel" aria-labelledby="package-tab">
            <div class="rounded-md bg-gray-100 p-6 shadow-inner shadow-slate-700 dark:bg-slate-700 dark:shadow-slate-500">
                <livewire:event.packages :event="$event" />
            </div>
        </div>
        <!-- Location -->
        <div class="hidden rounded-lg bg-gray-50 p-4 shadow-lg dark:bg-gray-800 dark:shadow-slate-500" id="location" role="tabpanel" aria-labelledby="location-tab">
            <div class="rounded-md bg-gray-100 p-6 shadow-inner shadow-slate-700 dark:bg-slate-700 dark:shadow-slate-500">
                <x-googlemaps />
                <a class="mt-4 flex items-center gap-2 rounded-md bg-primary py-2 text-white hover:bg-primary/80 dark:bg-secondary dark:text-gray-900 dark:hover:bg-secondary/80"
                    href="https://maps.app.goo.gl/v1x4481111111111EA" target="_blank">
                    <span class="ms-2">
                        <x-heroicon-s-map class="size-6" />
                    </span>
                    Directions
                </a>
            </div>
        </div>
        <!-- Upcoming -->
        <div class="hidden rounded-lg bg-gray-50 p-4 shadow-lg dark:bg-gray-800 dark:shadow-slate-500" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
            <div class="rounded-md bg-gray-100 p-6 shadow-inner shadow-slate-700 dark:bg-slate-700 dark:shadow-slate-500">
                <livewire:home.upcoming-events />
            </div>
        </div>
    </div>

</div>
