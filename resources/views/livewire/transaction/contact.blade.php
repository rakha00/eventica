<?php

use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public $transaction;
    public $tickets = [];
    public $ticketIds = [];
    public $isFilled = false;

    public function mount()
    {
        $this->transaction = Transaction::where('order_id', request()->route('orderId'))->where('status', 'Pending')->firstOrFail();

        $tickets = Ticket::where('transaction_id', $this->transaction->id)->get();
        foreach ($tickets as $index => $ticket) {
            $this->tickets[] = [
                'name' => $index == 0 ? $this->transaction->user->name : $ticket->name,
                'phone' => $index == 0 ? $this->transaction->user->phone : $ticket->phone,
                'email' => $index == 0 ? $this->transaction->user->email : $ticket->email,
                'identity_card_number' => $index == 0 ? $this->transaction->user->identity_card_number : $ticket->identity_card_number,
            ];
            $this->ticketIds[] = $ticket->id;
        }
    }

    public function updateUser()
    {
        User::where('id', $this->transaction->user_id)->update([
            'phone' => $this->tickets[0]['phone'],
            'identity_card_number' => $this->tickets[0]['identity_card_number'],
        ]);
    }

    public function updateTicket()
    {
        foreach ($this->tickets as $index => $ticket) {
            Ticket::where('id', $this->ticketIds[$index])->update($ticket);
        }
    }

    public function updateTransaction()
    {
        $this->transaction->update(['status' => 'On payment']);
    }

    public function fillAllInformation()
    {
        $this->isFilled = !$this->isFilled;
        foreach ($this->tickets as $index => $ticket) {
            if ($index != 0) {
                $this->tickets[$index]['phone'] = $this->isFilled ? $this->tickets[0]['phone'] : '';
                $this->tickets[$index]['email'] = $this->isFilled ? $this->tickets[0]['email'] : '';
            }
        }
    }

    public function bookContact()
    {
        $this->updateUser();
        $this->updateTicket();
        $this->updateTransaction();
        $this->redirect(route('trasaction-payment', ['orderId' => $this->transaction->order_id]));
    }
};
?>

<div class="p-6">
    @if (session()->has('error'))
        <div class="mt-4 rounded-md bg-red-500 p-4 text-white dark:bg-red-700 dark:text-white">
            {{ session('error') }}
        </div>
    @endif
    <h2 class="mb-2 text-2xl font-semibold text-gray-900 dark:text-white">Visitor Details</h2>
    <p class="text-xs text-gray-500 dark:text-gray-300">Make sure to fill in the visitor details correctly for a smooth experience.
        First ticket information will be used for the transaction details and we will update your user information.</p>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800" wire:ignore>
        <div class="inline-flex w-full items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-900 dark:text-white sm:text-lg">Complete Payment In</h3>
            <p class="shadow-slate-4000 rounded-md bg-gray-100 px-2 py-1 text-xs text-secondary shadow-inner dark:bg-gray-700 dark:text-primary dark:shadow-slate-500 sm:text-base" id="countdown">
            </p>
        </div>
    </div>
    <!-- Visitor -->
    @foreach ($tickets as $index => $ticket)
        <div class="mt-4 space-y-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white sm:text-lg">Ticket {{ $index + 1 }} (Pax)</h3>
            @if ($index == 0 && count($tickets) > 1)
                <label class="inline-flex cursor-pointer flex-col items-start sm:flex-row">
                    <input class="peer sr-only" type="checkbox">
                    <div class="peer relative mb-2 me-2 h-6 w-11 rounded-full border-gray-600 bg-gray-100 shadow-inner shadow-slate-400 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-secondary/80 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-0 peer-focus:ring-secondary rtl:peer-checked:after:-translate-x-full dark:bg-gray-700 dark:shadow-slate-500 dark:peer-checked:bg-primary/80 dark:peer-focus:ring-primary"
                        wire:click="fillAllInformation">
                    </div>
                    <span class="text-xs font-medium text-gray-900 dark:text-white sm:text-base">Fill all information with the same phone number and email</span>
                </label>
            @endif
            <input
                class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                type="text" wire:model="tickets.{{ $index }}.name" placeholder="Name" @if ($index == 0) readonly @endif>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                    <x-icon-flag-indonesia />
                </div>
                <input
                    class="block w-full rounded-md border-none bg-gray-100 p-2 ps-10 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                    type="text" wire:model="tickets.{{ $index }}.phone" placeholder="Phone Number" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="15" />
            </div>
            <input
                class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                type="email" wire:model="tickets.{{ $index }}.email" placeholder="Email" @if ($index == 0) readonly @endif>
            <input
                class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                type="text" wire:model="tickets.{{ $index }}.identity_card_number" placeholder="Identity Card Number" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                maxlength="16">
        </div>
    @endforeach

    <div class="mt-4 rounded-md bg-gray-50 p-4 dark:bg-gray-800">
        <!-- Total Payment -->
        <div class="mb-2 inline-flex w-full justify-between border-b-2 border-dashed border-gray-900 dark:border-gray-50">
            <h3 class="text-md mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">Total Payment</h3>
            <p class="text-md font-bold text-secondary dark:text-primary">
                {{ 'Rp ' . number_format($transaction->total_price, 0, ',', '.') }}
            </p>
        </div>
    </div>
    <form wire:submit="bookContact">
        <button class="mt-4 w-full rounded-md bg-secondary px-4 py-2 font-bold text-white hover:bg-secondary/80 dark:bg-primary dark:text-black dark:hover:bg-primary/80">
            Next
        </button>
    </form>
</div>

@script
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Set waktu transaksi dibuat
            var transactionCreatedAt = new Date("{{ $this->transaction->created_at }}");

            // Hitung waktu akhir transaksi (1 jam setelah transaksi dibuat)
            var transactionEndTime = new Date(transactionCreatedAt.getTime() + 60 * 60 * 1000);

            // Update hitungan mundur setiap detik
            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = transactionEndTime - now;

                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "EXPIRED";
                }
            }, 1000);
        })
    </script>
@endscript
