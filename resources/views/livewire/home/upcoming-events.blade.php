<?php

use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
    public $events;
    public $event;

    public function mount()
    {
        $this->events = Event::with('eventPackages')->where('status', 'published')->orderBy('created_at', 'desc')->take(10)->get();
        foreach ($this->events as $event) {
            $this->event = $event;
            $event->lowest_price = $event->eventPackages->min('price');
            $event->remaining = $event->eventPackages->sum('remaining');
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
                <div class="max-w-sm rounded-lg bg-white shadow-lg dark:bg-gray-800">
                    <a wire:click="navigate('{{ $event->slug }}')">
                        <img class="rounded-t-lg" src="{{ asset('storage/' . $event->image) }}" alt="Cards" />
                    </a>
                    <div class="p-5">
                        <p class="inline-flex w-full items-center text-gray-900 dark:text-white">
                            <x-heroicon-o-map-pin class="max-w-6 text-gray-900 dark:text-white" />
                            <span class="line-clamp-1 pe-2 ps-1">{{ \Illuminate\Support\Str::limit($event->location, 11) }}</span>
                            <span
                                class="line-clamp-1 border-s-2 border-black ps-2 font-bold text-primary dark:border-white dark:text-secondary">{{ \Carbon\Carbon::parse($event->start_event)->format('d M Y') }}</span>
                        </p>
                        <h5 class="mb-1 line-clamp-1 font-bold tracking-tight text-gray-900 dark:text-white">{{ $event->title }}</h5>
                        <p class="mb-2 text-gray-900 dark:text-white">
                            {!! \Illuminate\Support\Str::limit($event->description, 100) !!}
                        </p>
                        <p class="mb-2 text-gray-900 dark:text-white">
                            <span class="font-bold text-primary dark:text-secondary">{{ 'Rp ' . number_format($event->lowest_price, 0, ',', '.') }}</span>
                            / Person
                        </p>
                        @if ($event->remaining > 0)
                            <a class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-primary/90 focus:ring-0 dark:bg-secondary dark:text-gray-950 dark:hover:bg-secondary/90"
                                wire:click="navigate('{{ $event->slug }}')">
                                Available
                            </a>
                        @else
                            <button
                                class="inline-flex w-full justify-center rounded-lg bg-red-500 px-3 py-2 text-sm font-medium text-white hover:bg-red-500/90 focus:ring-0 dark:bg-secondary dark:text-gray-950 dark:hover:bg-secondary/90"
                                disabled>
                                Sold Out
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
