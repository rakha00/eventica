<?php

use App\Models\EventPackage;
use App\Models\Transaction;
use App\Models\Ticket;
use Filament\Notifications\Notification;
use App\Jobs\TransactionExpiredJob;
use Livewire\Volt\Component;

new class extends Component {
    public $package;
    public $quantity = 1;
    public $totalPrice;

    public function mount()
    {
        $this->package = EventPackage::where('slug', request()->packageSlug)->firstOrFail();

        $this->totalPrice = $this->package->price;
    }

    public function increment()
    {
        if ($this->quantity < 5) {
            $this->quantity++;
            $this->totalPrice = $this->package->price * $this->quantity;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->totalPrice = $this->package->price * $this->quantity;
        }
    }

    public function createTransaction()
    {
        $transaction = Transaction::create([
            'order_id' => strtoupper(uniqid()),
            'user_id' => auth()->user()->id,
            'event_package_id' => $this->package->id,
            'quantity' => $this->quantity,
            'total_price' => $this->totalPrice,
            'status' => 'Pending',
        ]);

        //Send notification to user
        Notification::make()->title('Transaction created successfully')->success()->body('You have 60 minutes to complete the payment.')->send();

        return $transaction;
    }

    public function createTicket($transaction)
    {
        for ($i = 0; $i < $this->quantity; $i++) {
            $uniqueTicketNumber = 'T-' . strtoupper($this->package->event->slug) . '-' . strtoupper($this->package->slug) . '-' . strtoupper(substr(uniqid(), -5));
            Ticket::create([
                'transaction_id' => $transaction->id,
                'ticket_id' => $uniqueTicketNumber,
                'status' => 'Inactive',
            ]);
        }
    }

    public function bookTicket()
    {
        if ($this->package->remaining < $this->quantity) {
            session()->flash('error', 'Sorry, this event package ' . $this->package->remaining . ' tickets left.');
            return;
        }
        $this->package->decrement('remaining', $this->quantity);
        $transaction = $this->createTransaction();
        $this->createTicket($transaction);

        TransactionExpiredJob::dispatch($transaction)->delay(now()->addSeconds(60));

        return redirect()->route('transaction-contact', ['eventSlug' => $this->package->event->slug, 'packageSlug' => $this->package->slug, 'orderId' => $transaction->order_id]);
    }
};
?>

<div class="p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{ $package->event->title }}</h2>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800">
        <!-- Package -->
        <h3 class="mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">{{ $package->title }}</h3>
        <p class="border-b-2 border-dashed border-gray-900 pb-2 font-bold text-secondary dark:border-gray-50 dark:text-primary sm:text-lg">
            {{ 'Rp ' . number_format($package->price, 0, ',', '.') }}
        </p>
    </div>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800""> <!-- Date -->
        <h3 class="mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">Date</h3>
        <div class="flex items-center justify-between gap-2 border-b-2 border-dashed border-gray-900 pb-4 dark:border-gray-50">
            <div class="w-full">
                <div class="flex w-full flex-col rounded-md bg-gray-100 p-2 text-xs text-secondary shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-primary dark:shadow-slate-500 sm:text-lg">
                    <p>
                        @if (\Carbon\Carbon::parse($package->start_valid)->format('Y-m-d') != \Carbon\Carbon::parse($package->end_valid)->format('Y-m-d'))
                            {{ \Carbon\Carbon::parse($package->start_valid)->format('l, d F Y') }} -
                            {{ \Carbon\Carbon::parse($package->end_valid)->format('l, d F Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($package->start_valid)->format('l, d F Y') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800""> <!-- Total -->
        <h3 class="mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">Total</h3>
        <div class="flex flex-col gap-2 sm:flex-row">
            <div class="inline-flex w-full basis-5/6 items-center justify-between rounded-md bg-gray-100 p-2 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:shadow-slate-500">
                <p class="text-gray-900 dark:text-white sm:text-lg">Event Package</p>
                <p class="font-bold text-secondary dark:text-primary sm:text-lg">
                    {{ 'Rp ' . number_format($totalPrice, 0, ',', '.') }}
                </p>
            </div>
            <div class="inline-flex w-full basis-1/6 items-center justify-between gap-2 rounded-md bg-gray-100 p-2 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:shadow-slate-500">
                <button class="rounded-lg bg-slate-50 px-2 text-gray-900 shadow-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500" wire:click="decrement">-</button>
                <p class="text-gray-900 dark:text-white">{{ $quantity }}</p>
                <button class="rounded-lg bg-slate-50 px-2 text-gray-900 shadow-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500" wire:click="increment">+</button>
            </div>
        </div>
        <div class="mt-4 border-b-2 border-dashed"></div>
    </div>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800""> <!-- Discount -->
        <h3 class="mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">Promo</h3>
        <div class="flex items-center gap-2 border-b-2 border-dashed border-gray-900 pb-4 dark:border-gray-50">
            <input class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 focus:ring-0 dark:bg-gray-700 dark:text-white dark:shadow-slate-500" type="text"
                placeholder="Enter promo code">
            <button class="rounded-lg bg-gray-50 px-4 py-2 text-gray-900 shadow-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">Apply</button>
        </div>
    </div>
    @if (session()->has('error'))
        <div class="mt-4 rounded-md bg-red-600 p-4 text-white dark:bg-red-500" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 3000)" x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" wire:poll.5s>
            {{ session('error') }}
        </div>
    @endif
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800">
        <!-- Summary -->
        <h2 class="mb-4 font-bold text-gray-900 dark:text-white sm:text-lg">Summary</h2>
        <div class="inline-flex w-full flex-col justify-between sm:flex-row">
            <h3 class="mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">Date</h3>
            <p class="mb-2 font-bold text-secondary dark:text-primary">
                {{ \Carbon\Carbon::parse($package->start_valid)->format('l, d F Y') }}
            </p>
        </div>
        <div class="mb-2 border-b-2 border-dashed border-gray-900 dark:border-gray-50"></div>
        <div class="flex gap-2">
            <div class="flex w-full flex-col rounded-md">
                <p class="mb-2 text-gray-900 dark:text-white">Total (<span>{{ $quantity }}</span> Ticket)</p>
                <div x-data="{ open: false }">
                    <p class="mb-2 inline-flex items-center font-bold text-secondary dark:text-primary" x-on:click="open = !open">
                        {{ 'Rp ' . number_format($totalPrice, 0, ',', '.') }}
                        <x-heroicon-s-chevron-down class="ms-2 h-4 w-4" />
                    </p>
                    <div class="rounded-md bg-gray-100 p-4 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:shadow-slate-500" x-show="open">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto text-gray-900 dark:text-white">
                                <thead>
                                    <tr>
                                        <th class="border border-gray-900 px-2 py-2 text-xs dark:border-gray-50">Qty
                                        </th>
                                        <th class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">Item
                                        </th>
                                        <th class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">Price
                                        </th>
                                        <th class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-900 px-2 py-2 text-xs dark:border-gray-50">
                                            {{ $quantity }}
                                        </td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">
                                            {{ $package->event->title . ' - ' . $package->title . ' - ' . \Carbon\Carbon::parse($package->start_valid)->format('l, d F Y') }}
                                        </td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">
                                            {{ 'Rp ' . number_format($package->price, 0, ',', '.') }}
                                        </td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">
                                            {{ 'Rp ' . number_format($package->price * $quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-900 px-2 py-2 text-xs dark:border-gray-50"></td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">Promo
                                        </td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">Rp 0
                                        </td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs dark:border-gray-50">Rp 0
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-r-0 border-gray-900 px-2 py-2 text-xs font-bold dark:border-gray-50">
                                            Total</td>
                                        <td class="border border-b border-gray-900 px-4 py-2 text-xs dark:border-gray-50">
                                        </td>
                                        <td class="border border-b border-gray-900 px-4 py-2 text-xs dark:border-gray-50">
                                        </td>
                                        <td class="border border-gray-900 px-4 py-2 text-xs font-bold dark:border-gray-50">
                                            {{ 'Rp ' . number_format($package->price * $quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="mt-4 w-full rounded-md bg-secondary px-4 py-2 font-bold text-white hover:bg-secondary/80 dark:bg-primary dark:text-gray-900 dark:hover:bg-primary/80"
        wire:click="bookTicket">Next</button>
</div>
