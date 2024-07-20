<?php

use Livewire\Volt\Component;

new class extends Component {
    public $event;
    public $packages;
    public $dates;
    public $selectedDate;
    public $isAnyPackageBooked = false;

    public function mount()
    {
        $this->packages = $this->event
            ->eventPackages()
            ->with(['transactions' => fn($query) => $query->where('user_id', auth()->id())])
            ->orderBy('price', 'asc')
            ->get();

        $this->isAnyPackageBooked = $this->packages->contains(fn($package) => $package->transactions->isNotEmpty());

        $startDate = \Carbon\Carbon::parse($this->event->start_event);
        $endDate = \Carbon\Carbon::parse($this->event->end_event);

        while ($startDate->lte($endDate)) {
            $this->dates[] = $startDate->copy()->format('Y-m-d');
            $startDate->addDay();
        }
    }

    public function updatedSelectedDate()
    {
        $query = $this->event->eventPackages()->orderBy('price', 'asc');

        if ($this->selectedDate) {
            $query->whereDate('start_valid', '<=', $this->selectedDate)->whereDate('end_valid', '>=', $this->selectedDate);
        }

        $this->packages = $query->get();
    }
}; ?>

<div class="space-y-4">
    <select class="w-full rounded-lg border-none bg-gray-50 p-2 text-gray-900 shadow-lg focus:ring-0 dark:bg-gray-800 dark:text-white" id="package" name="package" wire:model.live="selectedDate">
        <option value="">Select Date</option>
        @foreach ($dates as $date)
            <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</option>
        @endforeach
    </select>
    @foreach ($packages as $package)
        <div class="w-full rounded-lg bg-gray-50 p-6 shadow-md dark:bg-gray-800">
            <div class="mb-2">
                <h5 class="mb-2 text-sm font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-xl">
                    {{ $package->title }}
                </h5>
                <span class="text-gray-600 dark:text-gray-200">{{ \Carbon\Carbon::parse($package->start_valid)->format('d M Y H:i') }}
                    -
                    {{ \Carbon\Carbon::parse($package->end_valid)->format('d M Y H:i') }}</span>
                <p class="text-gray-600 dark:text-gray-200">{{ $package->description }}</p>
            </div>
            <div class="flex flex-col items-center justify-between gap-2 sm:flex-row">
                <p class="w-full justify-start text-xl font-bold text-primary dark:text-secondary">
                    {{ 'Rp ' . number_format($package->price, 0, ',', '.') }}
                </p>
                @if ($isAnyPackageBooked)
                    <button
                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-3 py-2 text-center font-medium text-white dark:bg-secondary dark:text-gray-900 dark:hover:bg-secondary/80 sm:max-w-fit"
                        disabled>
                        Booked
                    </button>
                @else
                    <a class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-3 py-2 text-center font-medium text-white dark:bg-secondary dark:text-gray-900 dark:hover:bg-secondary/80 sm:max-w-fit"
                        href="{{ route('transaction-detail', [$event->slug, $package->slug]) }}
                    ">
                        Select Package
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
