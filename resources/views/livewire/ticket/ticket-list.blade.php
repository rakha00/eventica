<?php

use Livewire\Volt\Component;
use App\Models\Ticket;
use App\Models\Transaction;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;

new class extends Component {
    public $transactions;
    public $ticketId;
    public $qrCode;

    public function mount()
    {
        $this->transactions = Transaction::with(['tickets', 'eventPackage.event'])
            ->where('user_id', auth()->id())
            ->where('status', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function allTickets()
    {
        $this->transactions = Transaction::with(['tickets', 'eventPackage.event'])
            ->where('user_id', auth()->id())
            ->where('status', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function upcomingEvents()
    {
        $this->transactions = Transaction::with([
            'tickets',
            'eventPackage' => function ($query) {
                $query->whereDate('end_valid', '>', now());
            },
            'eventPackage.event',
        ])
            ->where('user_id', auth()->id())
            ->where('status', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->transactions = $this->transactions->filter(function ($transaction) {
            return $transaction->eventPackage !== null;
        });
    }

    public function pastEvents()
    {
        $this->transactions = Transaction::with([
            'tickets',
            'eventPackage' => function ($query) {
                $query->whereDate('end_valid', '<', now());
            },
            'eventPackage.event',
        ])
            ->where('user_id', auth()->id())
            ->where('status', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->transactions = $this->transactions->filter(function ($transaction) {
            return $transaction->eventPackage !== null;
        });
    }

    public function viewQRCode($ticketId)
    {
        $this->ticketId = $ticketId;
        $this->qrCode = (new QRCode())->render($ticketId);
        $this->dispatch('open-modal', id: 'view-qr-code');
    }
}; ?>

<div>
    <div class="md: flex flex-col space-y-2 sm:flex-row sm:space-x-4 sm:space-y-0" x-data="{ activeTab: 'allTickets' }">
        <button class="rounded-full bg-white px-2 py-1 text-xs shadow dark:bg-gray-700"
            :class="{ 'dark:bg-secondary text-white bg-primary dark:text-gray-900': activeTab === 'allTickets', 'bg-white text-gray-900 dark:text-white': activeTab !== 'allTickets' }"
            x-on:click="activeTab = 'allTickets'" wire:click="allTickets">
            All Tickets
        </button>
        <button class="rounded-full bg-white px-2 py-1 text-xs shadow dark:bg-gray-700 dark:text-white"
            :class="{ 'dark:bg-secondary text-white bg-primary dark:text-gray-900': activeTab === 'upcomingEvents', 'bg-white text-gray-900 dark:text-white': activeTab !== 'upcomingEvents' }"
            x-on:click="activeTab = 'upcomingEvents'" wire:click="upcomingEvents">
            Upcoming Events
        </button>
        <button class="rounded-full bg-white px-2 py-1 text-xs shadow dark:bg-gray-700 dark:text-white"
            :class="{ 'dark:bg-secondary text-white bg-primary dark:text-gray-900': activeTab === 'pastEvents', 'bg-white text-gray-900 dark:text-white': activeTab !== 'pastEvents' }"
            x-on:click="activeTab = 'pastEvents'" wire:click="pastEvents">
            Past Events
        </button>
    </div>

    <x-filament::modal id="view-qr-code" alignment="center">
        <x-slot name="heading">
            Scan this QR code at venue
        </x-slot>
        {{-- Modal content --}}
        <img class="w-full" src="{{ $qrCode }}" alt="QR Code">
        <p class="text-gray-900 dark:text-white">{{ 'Ticket ID: ' . $ticketId }}</p>
    </x-filament::modal>

    <div x-data="{ open: null }">
        @if ($transactions->isEmpty())
            <p class="text-lg text-gray-700 dark:text-gray-400">You don't have any tickets</p>
        @endif
        @foreach ($transactions as $transaction)
            <div wire:key="{{ $transaction->id }}">
                <div class="mb-4">
                    <button
                        class="flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                        x-on:click="open === {{ $transaction->id }} ? open = null : open = {{ $transaction->id }}">
                        <div class="flex items-center">
                            <img class="h-12 w-12 rounded-full object-cover" src="{{ $transaction->eventPackage->event->image }}" alt="Event">
                            <div class="ml-4 text-left">
                                <h5 class="text-lg font-bold text-gray-900 dark:text-white">{{ $transaction->eventPackage->event->title }}
                                    ({{ $transaction->eventPackage->title }})
                                </h5>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction->eventPackage->start_valid)->format('d F Y H:i') }} -
                                    {{ \Carbon\Carbon::parse($transaction->eventPackage->end_valid)->format('d F Y H:i') }}</p>
                            </div>
                        </div>
                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="mt-2 grid grid-cols-1 gap-2 md:grid-cols-2" x-show="open === {{ $transaction->id }}">
                        @foreach ($transaction->tickets as $ticket)
                            <div wire:key="{{ $ticket->id }}">
                                <a
                                    class="flex flex-col items-center rounded-lg border border-gray-200 bg-white shadow hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 md:max-w-xl md:flex-row">
                                    <div class="flex w-full flex-col justify-between p-4 leading-normal lg:flex-row lg:items-center lg:justify-between">
                                        <div>
                                            <p class="mb-1 font-normal text-gray-700 dark:text-gray-400">{{ 'Ticket ID: ' . $ticket->ticket_id }}</p>
                                            <p class="mb-1 font-normal text-gray-700 dark:text-gray-400">{{ $ticket->name }}</p>
                                        </div>
                                        <button class="rounded-lg bg-blue-500 px-2 py-1 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75"
                                            wire:click="viewQRCode('{{ $ticket->ticket_id }}')">
                                            View QR Code
                                        </button>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
