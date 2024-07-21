<?php

use Livewire\Volt\Component;
use App\Models\Event;
use App\Models\EventCategory;
use Livewire\Attributes\Url;

new class extends Component {
    public $events;
    public $categories;
    public $locations;
    public $search = '';
    #[Url]
    public $selectedCategory = '';
    public $selectedPrice = '';
    public $selectedLocation = '';
    public $selectedDate = '';
    public $selectedSort = 'latest';
    public $eventCount = 8;
    public $activeFilters = [];

    public function mount()
    {
        $this->selectedCategory;
        $this->events = Event::with('eventPackages')->where('status', 'published')->take(8)->get();
        $this->categories = EventCategory::all();
        $this->locations = Event::where('status', 'published')->pluck('location')->unique();
        $this->setLowestPrice();
        $this->updateActiveFilters();
    }

    public function updated($property, $value)
    {
        if (in_array($property, ['selectedCategory', 'selectedPrice', 'selectedLocation', 'selectedDate'])) {
            $this->filterEvents();
        }

        if ($property == 'search') {
            $this->resetFilters();
            $this->selectedSort = 'latest';
            $this->events = Event::with('eventPackages')
                ->where('status', 'published')
                ->where('title', 'like', $this->search . '%')
                ->orderBy('start_event', 'desc')
                ->take($this->eventCount)
                ->get();
            $this->setLowestPrice();
        }
    }

    public function filterEvents()
    {
        $query = Event::with('eventPackages')->where('status', 'published');

        $filters = [
            'event_category_id' => $this->selectedCategory,
            'location' => $this->selectedLocation,
        ];

        foreach ($filters as $field => $value) {
            if ($value) {
                $query->where($field, $value);
            }
        }

        if ($this->selectedPrice) {
            $priceRange = explode('-', $this->selectedPrice);
            $query->whereHas('eventPackages', function ($q) use ($priceRange) {
                $q->whereBetween('price', [$priceRange[0], $priceRange[1]]);
            });
        }

        if ($this->selectedDate) {
            $query->where('start_event', '>=', $this->selectedDate);
        }

        $this->applySorting($query);

        $this->events = $query->get();
        $this->setLowestPrice();
        $this->updateActiveFilters();
    }

    private function applySorting($query)
    {
        if ($this->selectedSort == 'latest') {
            $query->orderBy('start_event', 'desc');
        } elseif ($this->selectedSort == 'earliest') {
            $query->orderBy('start_event', 'asc');
        } elseif ($this->selectedSort == 'lowest') {
            $query->orderBy(function ($query) {
                $query->select('price')->from('event_packages')->whereColumn('event_packages.event_id', 'events.id')->orderBy('price', 'asc')->limit(1);
            });
        } elseif ($this->selectedSort == 'highest') {
            $query->orderByDesc(function ($query) {
                $query->selectRaw('MIN(price)')->from('event_packages')->whereColumn('event_packages.event_id', 'events.id');
            });
        }
    }

    public function sortBy($criteria)
    {
        $this->selectedSort = $criteria;
        $this->filterEvents();
    }

    public function updateActiveFilters()
    {
        $this->activeFilters = collect([
            'Category' => EventCategory::find($this->selectedCategory)->title ?? '',
            'Price' => $this->selectedPrice,
            'Location' => $this->selectedLocation,
            'Date' => $this->selectedDate,
        ])
            ->filter()
            ->all();
    }

    public function removeFilter($filterName)
    {
        $filterMap = [
            'Category' => 'selectedCategory',
            'Price' => 'selectedPrice',
            'Location' => 'selectedLocation',
            'Date' => 'selectedDate',
        ];

        if (isset($filterMap[$filterName])) {
            $this->{$filterMap[$filterName]} = '';
        }

        $this->updateActiveFilters();
        $this->filterEvents();
    }

    public function resetFilters()
    {
        $this->selectedCategory = '';
        $this->selectedPrice = '';
        $this->selectedLocation = '';
        $this->selectedDate = '';
        $this->filterEvents();
    }

    public function loadMore()
    {
        $this->eventCount += 8;
        $this->resetFilters();
        $this->events = Event::with('eventPackages')
            ->where('status', 'published')
            ->take($this->eventCount)
            ->get();
        $this->setLowestPrice();
    }

    private function setLowestPrice()
    {
        $this->events->each(function ($event) {
            $event->lowest_price = $event->eventPackages->min('price');
        });
    }
};
?>

<section class="py-8 antialiased md:py-12">
    <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
        <!-- Heading & Filters -->
        <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
            <div>
                <!-- Breadcrumb -->
                <nav class="mb-4 flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a class="inline-flex items-center text-sm font-medium text-gray-800 hover:text-gray-600 dark:text-gray-200 dark:hover:text-gray-400" href="{{ route('home') }}"
                                wire:navigate>
                                <x-heroicon-s-home class="size-4 mb-1 me-2.5 text-gray-800 dark:text-gray-200" />
                                Home
                            </a>
                        </li>

                        <li aria-current="page">
                            <div class="flex items-center">
                                <x-heroicon-o-chevron-right class="size-4 me-2.5 text-gray-800 dark:text-gray-200" />
                                <span class="ms-1 text-sm font-bold text-primary dark:text-secondary md:ms-2">Events</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="w-96">
                    <input
                        class="block w-full rounded-lg border border-primary bg-gray-50 p-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 dark:border-secondary dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        id="search" type="search" placeholder="Search events..." wire:model.live.debounce.250ms="search">
                </div>
            </div>
            <div class="flex flex-col items-center sm:flex-row sm:space-x-4">
                <div class="mt-4 flex w-full items-center space-x-4 sm:mt-0">
                    <button
                        class="flex w-full items-center justify-center rounded-lg border border-primary bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary focus:z-10 focus:outline-none focus:ring-0 dark:border-secondary dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:w-auto"
                        data-modal-toggle="filterModal" data-modal-target="filterModal" type="button">
                        <x-gmdi-filter-alt-o class="me-2 h-5 w-5" />
                        Filters
                        <x-gmdi-keyboard-arrow-down-tt class="h-6 w-6" />
                    </button>
                    <button
                        class="flex w-full items-center justify-center rounded-lg border border-primary bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary focus:z-10 focus:outline-none focus:ring-0 dark:border-secondary dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:w-auto"
                        id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button">
                        <x-gmdi-sort-r class="me-2 h-5 w-5" />
                        Sort
                        <x-gmdi-keyboard-arrow-down-tt class="h-6 w-6" />
                    </button>
                </div>
                <div class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700" id="dropdownSort1" data-popper-placement="bottom">
                    <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400" aria-labelledby="sortDropdownButton">
                        <li>
                            <p class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                wire:click="sortBy('latest')">Latest</p>
                        </li>
                        <li>
                            <p class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                wire:click="sortBy('earliest')">Earliest</p>
                        </li>
                        <li>
                            <p class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                wire:click="sortBy('lowest')"> Lowest price </p>
                        </li>
                        <li>
                            <p class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                                wire:click="sortBy('highest')"> Highest price </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if (!empty($activeFilters))
            <div class="mb-4">
                @foreach ($activeFilters as $filterName => $filterValue)
                    <div class="inline-flex items-center space-x-4 rounded-full bg-primary px-3 py-1 text-sm text-white dark:bg-secondary dark:text-gray-900">
                        <p class="text-xs font-semibold">{{ $filterName . ': ' . $filterValue }}</p>
                        <button class="ml-2 text-xs text-white dark:text-gray-900" wire:click="removeFilter('{{ $filterName }}')">
                            <x-gmdi-close-r class="size-4" />
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Events -->
        <div class="mb-4 grid min-h-fit gap-4 min-[550px]:grid-cols-2 md:mb-8 md:grid-cols-3 xl:grid-cols-4">
            @forelse ($events as $event)
                <div class="max-w-sm rounded-lg bg-white shadow-lg dark:bg-gray-800">
                    <a href="{{ route('event-detail', $event->slug) }}" wire:navigate>
                        <img class="rounded-t-lg" src="{{ asset($event->image) }}" alt="Cards" />
                    </a>
                    <div class="p-5">
                        <p class="inline-flex w-full items-center text-gray-900 dark:text-white">
                            <x-heroicon-o-map-pin class="max-w-6 text-gray-900 dark:text-white" />
                            <span class="line-clamp-1 pe-2 ps-1">{{ \Illuminate\Support\Str::limit($event->location, 11) }}</span>
                            <span
                                class="line-clamp-1 border-s-2 border-black ps-2 font-bold text-primary dark:border-white dark:text-secondary">{{ \Carbon\Carbon::parse($event->start_event)->format('d M Y') }}</span>
                        </p>
                        <h5 class="mb-1 line-clamp-1 font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ $event->title }}
                        </h5>
                        <p class="mb-3 line-clamp-3 h-[4.6rem] font-normal text-gray-700 dark:text-gray-200">
                            {{ $event->description }}
                        </p>
                        <p class="mb-2 text-gray-900 dark:text-white">
                            <span class="font-bold text-primary dark:text-secondary">{{ 'Rp ' . number_format($event->lowest_price, 0, ',', '.') }}</span>
                            / Person
                        </p>
                        <a class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-primary/90 focus:ring-0 dark:bg-secondary dark:text-gray-950 dark:hover:bg-secondary/90"
                            href="{{ route('event-detail', $event->slug) }}" wire:navigate>
                            Available
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full min-h-screen text-start text-gray-500 dark:text-gray-400">
                    No event found.
                </div>
            @endforelse
        </div>
        @if ($events->isNotEmpty())
            <div class="grid w-full grid-cols-5 items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <p>Showing {{ $events->count() }} results</p>
                </div>
                <div class="col-start-3 flex justify-center">
                    @if ($events->count() == $eventCount)
                        <button
                            class="hover:text-primary-700 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                            type="button" wire:click="loadMore">Show more</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
    <!-- Filter modal -->
    <div class="fixed left-0 right-0 top-0 z-50 hidden h-modal w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0 md:h-full" id="filterModal" aria-hidden="true" tabindex="-1" method="get"
        wire:ignore>
        <div class="relative h-full w-full max-w-xl md:h-auto">
            <!-- Modal content -->
            <div class="relative rounded-lg bg-white shadow dark:bg-gray-800">
                <!-- Modal header -->
                <div class="flex items-start justify-between rounded-t p-4 md:p-5">
                    <h3 class="text-lg font-normal text-gray-500 dark:text-gray-400">Filters</h3>
                    <button
                        class="ml-auto inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="filterModal" type="button">
                        <x-gmdi-close-r class="size-6" />
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="px-4 md:px-5">
                    <div class="space-y-4" id="filters" role="tabpanel" aria-labelledby="filters-tab">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="category-filter">Category</label>
                            <select
                                class="focus:border-primary-500 focus:ring-primary-500 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                id="category-filter" name="category" wire:model.change="selectedCategory">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="price-filter">Price</label>
                            <select
                                class="focus:border-primary-500 focus:ring-primary-500 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                id="price-filter" name="price" wire:model.change="selectedPrice">
                                <option value="">Select Price</option>
                                <option value="0-50000">0 - 50,000</option>
                                <option value="50000-100000">50,000 - 100,000</option>
                                <option value="100000-500000">100,000 - 500,000</option>
                                <option value="500000-1000000">500,000 - 1,000,000</option>
                                <option value="1000000">1,000,000+</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="location-filter">Location</label>
                            <select
                                class="focus:border-primary-500 focus:ring-primary-500 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                id="location-filter" name="location" wire:model.change="selectedLocation">
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location }}">{{ $location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="date-filter">Date</label>
                            <input
                                class="focus:border-primary-500 focus:ring-primary-500 mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                id="date-filter" name="date" type="date" wire:model.change="selectedDate">
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center justify-end rounded-b p-4 dark:border-gray-600 md:p-5">
                    <button
                        class="hover:text-primary-700 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                        type="reset" wire:click="resetFilters">Reset</button>
                </div>
            </div>
        </div>
    </div>
</section>
