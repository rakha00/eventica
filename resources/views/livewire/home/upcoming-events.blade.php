<?php

use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
    public $events;
    public $event;

    public function mount()
    {
        $this->events = Event::with('eventPackages')->get();
        foreach ($this->events as $event) {
            $this->event = $event;
            $event->lowest_price = $event->eventPackages->min('price');
        }
    }

    public function navigate($slug)
    {
        $this->redirect(route('event-detail', $slug), navigate: true);
    }
}; ?>

<div class="swiper h-max w-full">
    <div class="swiper-wrapper py-4">
        <!-- Slides -->
        @foreach ($events as $event)
            <div class="swiper-slide">
                <div class="max-w-sm rounded-lg bg-white shadow-lg dark:bg-gray-900">
                    <a wire:click="navigate('{{ $event->slug }}')">
                        <img class="rounded-t-lg" src="{{ asset($event->image) }}" alt="Card Image" />
                    </a>
                    <div class="p-5">
                        <p class="inline-flex w-full items-center text-gray-900 dark:text-white">
                            <x-heroicon-o-map-pin class="max-w-6 text-gray-900 dark:text-white" />
                            <span class="line-clamp-1 pe-2 ps-1">{{ \Illuminate\Support\Str::limit($event->location, 11) }}</span>
                            <span
                                class="line-clamp-1 border-s-2 border-black ps-2 font-bold text-secondary dark:border-white dark:text-primary">{{ \Carbon\Carbon::parse($event->start_event)->format('d M Y') }}</span>
                        </p>
                        <h5 class="mb-1 line-clamp-1 font-bold tracking-tight text-gray-900 dark:text-white">{{ $event->title }}</h5>
                        <p class="mb-3 line-clamp-3 h-[4.6rem] font-normal text-gray-700 dark:text-gray-200">{{ $event->description }}</p>
                        <p class="mb-2 text-gray-900 dark:text-white">
                            <span class="font-bold text-secondary dark:text-primary">{{ 'Rp ' . number_format($event->lowest_price, 0, ',', '.') }}</span>
                            / Person
                        </p>
                        <a class="inline-flex w-full justify-center rounded-lg bg-secondary px-3 py-2 text-sm font-medium text-white hover:bg-secondary/90 focus:outline-none focus:ring-4 focus:ring-secondary dark:bg-primary dark:text-gray-950 dark:hover:bg-primary/90"
                            wire:click="navigate('{{ $event->slug }}')">
                            Available
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
