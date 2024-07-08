<?php
use App\Models\EventPackage;
use App\Models\Transaction;
use App\Models\Ticket;
use Livewire\Volt\Component;

new class extends Component {
    public $package;
    public $quantity;
    public $price;

    public function mount(EventPackage $package)
    {
        $this->package = $package->where("slug", request()->packageSlug)->first();
        if ($this->package) {
            $this->quantity = 1;
            $this->price = $this->package->price;
        } else {
            abort(404);
        }
    }

    public function increment(EventPackage $package)
    {
        if ($this->quantity < 5) {
            $this->quantity++;
            $this->price = $this->package->price * $this->quantity;
        }
    }

    public function decrement(EventPackage $package)
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->price = $this->package->price * $this->quantity;
        }
    }

    public function createTransaction()
    {
        $transaction = Transaction::create([
            "order_id" => strtoupper(uniqid()),
            "user_id" => auth()->user()->id,
            "package_id" => $this->package->id,
            "quantity" => $this->quantity,
            "discount_price" => 0,
            "total_price" => $this->price,
        ]);

        return $transaction;
    }

    public function createTicket(Transaction $transaction)
    {
        for ($i = 0; $i < $this->quantity; $i++) {
            $uniqueTicketNumber = "T-" . strtoupper($this->package->event->slug) . "-" . strtoupper($this->package->slug) . "-" . strtoupper(substr(uniqid(), -5));
            Ticket::create([
                "transaction_id" => $transaction->id,
                "ticket_number" => $uniqueTicketNumber,
                "status" => "Inactive",
            ]);
        }
    }

    public function bookTicket()
    {
        if ($this->package->remaining < $this->quantity) {
            session()->flash("error", "Sorry, this event package " . $this->package->remaining . " tickets left.");
            return;
        } else {
            $this->package->decrement("remaining", $this->quantity);
            $transaction = $this->createTransaction();
            $this->createTicket($transaction);
            $this->redirect(route("book-contact", $transaction->order_id));
        }
    }
};
?>

<div>
    <h2 class="text-xl font-semibold text-white sm:text-2xl">{{ $package->event->title }}</h2>
    <div class="mt-4 rounded-md bg-gray-800 p-4"> <!-- Package -->
        <h3 class="text-md mb-2 line-clamp-1 font-semibold text-white sm:text-lg">{{ $package->title }}</h3>
        <p class="border-b-2 border-dashed pb-2 text-xs font-bold text-tertiary sm:text-base">{{ "Rp " . number_format($package->price, 0, ",", ".") }}</p>
    </div>
    <div class="mt-4 rounded-md bg-gray-800 p-4"> <!-- Date -->
        <h3 class="text-md mb-2 font-semibold text-white sm:text-lg">Date</h3>
        <div class="flex items-center justify-between gap-2 border-b-2 border-dashed pb-4">
            <div class="w-full">
                <div class="flex w-full flex-col rounded-md bg-gray-700 p-2 text-xs text-tertiary sm:text-base">
                    <p>
                        @if (\Carbon\Carbon::parse($package->start_valid)->format("Y-m-d") != \Carbon\Carbon::parse($package->end_valid)->format("Y-m-d"))
                            {{ \Carbon\Carbon::parse($package->start_valid)->format("l, d F Y") }} - {{ \Carbon\Carbon::parse($package->end_valid)->format("l, d F Y") }}
                        @else
                            {{ \Carbon\Carbon::parse($package->start_valid)->format("l, d F Y") }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 rounded-md bg-gray-800 p-4"> <!-- Total -->
        <h3 class="text-md mb-2 font-semibold text-white sm:text-lg">Total</h3>
        <div class="flex flex-col gap-2 sm:flex-row">
            <div class="inline-flex w-full basis-5/6 items-center justify-between rounded-md bg-gray-700 p-2">
                <p class="text-xs text-white sm:text-base">Event Package</p>
                <p class="text-xs font-bold text-tertiary sm:text-base">{{ "Rp " . number_format($price, 0, ",", ".") }}</p>
            </div>
            <div class="inline-flex w-full basis-1/6 items-center justify-between gap-2 rounded-md bg-gray-700 p-2">
                <button class="rounded-lg bg-gray-600 px-2 text-white hover:bg-gray-500" wire:click="decrement">-</button>
                <p class="text-md text-white">{{ $quantity }}</p>
                <button class="rounded-lg bg-gray-600 px-2 text-white hover:bg-gray-500" wire:click="increment">+</button>
            </div>
        </div>
        <div class="mt-4 border-b-2 border-dashed"></div>
    </div>
    <div class="mt-4 rounded-md bg-gray-800 p-4"> <!-- Discount -->
        <h3 class="text-md mb-2 font-semibold text-white sm:text-lg">Promo</h3>
        <div class="flex items-center gap-2 border-b-2 border-dashed pb-4">
            <input class="w-full rounded-md border border-gray-600 bg-gray-700 p-2 text-white" type="text" placeholder="Enter promo code">
            <button class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-500">Apply</button>
        </div>
    </div>
    @if (session()->has("error"))
        <div class="mt-4 rounded-md bg-red-500 p-4 text-white">
            {{ session("error") }}
        </div>
    @endif
    <div class="mt-4 rounded-md bg-gray-800 p-4"> <!-- Summary -->
        <h2 class="text-md mb-4 font-bold text-white sm:text-lg">Summary</h2>
        <div class="inline-flex w-full flex-col justify-between sm:flex-row">
            <h3 class="text-md mb-2 font-semibold text-white sm:text-lg">Date</h3>
            <p class="mb-2 text-xs font-bold text-tertiary sm:text-base">{{ \Carbon\Carbon::parse($package->start_valid)->format("l, d F Y") }}</p>
        </div>
        <div class="mb-2 border-b-2 border-dashed"></div>
        <div class="flex gap-2">
            <div class="flex w-full flex-col rounded-md">
                <p class="mb-2 text-xs text-white sm:text-base">Total (<span>{{ $quantity }}</span> Ticket)</p>
                <div x-data="{ open: false }">
                    <p class="mb-2 inline-flex items-center text-xs font-bold text-tertiary sm:text-base" @click="open = !open">{{ "Rp " . number_format($price, 0, ",", ".") }}
                        <x-heroicon-s-chevron-down class="ms-2 h-4 w-4" />
                    </p>
                    <div class="rounded-md bg-gray-700 p-4" x-show="open">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto text-white">
                                <thead>
                                    <tr>
                                        <th class="border px-2 py-2 text-xs">Qty</th>
                                        <th class="border px-4 py-2 text-xs">Item</th>
                                        <th class="border px-4 py-2 text-xs">Price</th>
                                        <th class="border px-4 py-2 text-xs">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border px-2 py-2 text-xs">{{ $quantity }}</td>
                                        <td class="border px-4 py-2 text-xs">
                                            {{ $package->event->title . " - " . $package->title . " - " . \Carbon\Carbon::parse($package->start_valid)->format("l, d F Y") }}</td>
                                        <td class="border px-4 py-2 text-xs">{{ "Rp " . number_format($package->price, 0, ",", ".") }}</td>
                                        <td class="border px-4 py-2 text-xs">{{ "Rp " . number_format($package->price * $quantity, 0, ",", ".") }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border px-2 py-2 text-xs"></td>
                                        <td class="border px-4 py-2 text-xs">Promo</td>
                                        <td class="border px-4 py-2 text-xs">Rp 0</td>
                                        <td class="border px-4 py-2 text-xs">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-r-0 px-2 py-2 text-xs font-bold">Total</td>
                                        <td class="border-b px-4 py-2 text-xs"></td>
                                        <td class="border-b px-4 py-2 text-xs"></td>
                                        <td class="border px-4 py-2 text-xs font-bold">{{ "Rp " . number_format($package->price * $quantity, 0, ",", ".") }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form wire:submit="bookTicket">
        <button class="mt-4 w-full rounded-md bg-tertiary px-4 py-2 font-bold text-black hover:bg-tertiary/80">Next</button>
    </form>
</div>
