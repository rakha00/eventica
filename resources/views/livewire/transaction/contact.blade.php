<?php

use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;

new class extends Component {
    public $transaction;
    public $tickets = [];
    public $ticketIds = [];
    public $isFilled = false;

    public function mount()
    {
        $this->transaction = Transaction::where('order_id', request()->route('orderId'))
            ->where('user_id', auth()->user()->id)
            ->first();

        // Handle redirect failed
        if ($this->transaction->status === 'On payment') {
            $this->dispatch('redirect', $this->transaction->order_id);
        }

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

    public function rules()
    {
        return [
            'tickets.*.name' => 'required|alpha',
            'tickets.*.phone' => 'required|max_digits:12|min_digits:10|numeric',
            'tickets.*.email' => 'required|email',
            'tickets.*.identity_card_number' => 'required|digits:16|numeric',
        ];
    }

    public function messages()
    {
        return [
            'tickets.*.name.required' => 'Name must be filled.',
            'tickets.*.name.alpha' => 'Name must be alphabetic.',
            'tickets.*.phone.required' => 'Phone number must be filled.',
            'tickets.*.phone.min_digits' => 'Phone number is too short.',
            'tickets.*.phone.max_digits' => 'Phone number is too long.',
            'tickets.*.phone.numeric' => 'Phone number must be numeric.',
            'tickets.*.email.required' => 'Email must be filled.',
            'tickets.*.email.email' => 'Invalid email format.',
            'tickets.*.identity_card_number.required' => 'ID card number must be filled.',
            'tickets.*.identity_card_number.digits' => 'ID card number must consist of :digits digits.',
            'tickets.*.identity_card_number.numeric' => 'ID card number must be numeric.',
        ];
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
        $this->validate();
        $this->updateUser();
        $this->updateTicket();
        $this->updateTransaction();
        return redirect()->route('transaction-payment', ['orderId' => $this->transaction->order_id]);
    }
};
?>

@script
    <script>
        $wire.on('redirect', (orderId) => {
            window.location.href = '/payment/' + orderId;
        });
    </script>
@endscript

<div class="p-6">
    <h2 class="mb-2 text-2xl font-semibold text-gray-900 dark:text-white">Visitor Details</h2>
    <p class="text-xs text-gray-500 dark:text-gray-300">Make sure to fill in the visitor details correctly for a smooth
        experience.
        First ticket information will be used for the transaction details and we will update your user information.</p>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800" wire:ignore>
        <div class="inline-flex w-full items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-900 dark:text-white sm:text-lg">Complete Payment In</h3>
            <p class="shadow-slate-4000 rounded-md bg-gray-100 px-2 py-1 text-xs text-primary shadow-inner dark:bg-gray-700 dark:text-secondary dark:shadow-slate-500 sm:text-base" id="countdown">
            </p>
        </div>
    </div>
    <!-- Visitor -->
    @foreach ($tickets as $index => $ticket)
        <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white sm:text-lg">Ticket {{ $index + 1 }} (Pax)</h3>
            @if ($index == 0 && count($tickets) > 1)
                <label class="mt-4 inline-flex cursor-pointer flex-col items-start sm:flex-row">
                    <input class="peer sr-only" type="checkbox">
                    <div class="peer relative mb-2 me-2 h-6 w-11 rounded-full border-gray-600 bg-gray-100 shadow-inner shadow-slate-400 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary/80 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-0 peer-focus:ring-primary rtl:peer-checked:after:-translate-x-full dark:bg-gray-700 dark:shadow-slate-500 dark:peer-checked:bg-secondary/80 dark:peer-focus:ring-secondary"
                        wire:click="fillAllInformation">
                    </div>
                    <span class="text-xs font-medium text-gray-900 dark:text-white sm:text-base">Fill all information with the
                        same phone number and email</span>
                </label>
            @endif
            <div class="mt-4">
                @error('tickets.' . $index . '.name')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <input
                    class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                    type="text" x-on:click="showError = false" wire:model="tickets.{{ $index }}.name" placeholder="Name" @if ($index == 0) readonly @endif>
            </div>
            <div class="mt-4">
                @error('tickets.' . $index . '.phone')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <x-icon-flag-indonesia />
                    </div>
                    <input
                        class="block w-full rounded-md border-none bg-gray-100 p-2 ps-10 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                        type="text" wire:model="tickets.{{ $index }}.phone" placeholder="Phone Number" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="12" />
                </div>
            </div>
            <div class="mt-4">
                @error('tickets.' . $index . '.email')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <input
                    class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                    type="email" x-on:click="showError = false" wire:model="tickets.{{ $index }}.email" placeholder="Email" @if ($index == 0) readonly @endif>
            </div>
            <div class="mt-4">
                @error('tickets.' . $index . '.identity_card_number')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                <input
                    class="w-full rounded-md border-none bg-gray-100 p-2 text-gray-900 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-white dark:shadow-slate-500 dark:placeholder:text-gray-400"
                    type="text" x-on:click="showError = false" wire:model="tickets.{{ $index }}.identity_card_number" placeholder="Identity Card Number"
                    onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="16">
            </div>
        </div>
    @endforeach

    <div class="mt-4 rounded-md bg-gray-50 p-4 dark:bg-gray-800">
        <!-- Total Payment -->
        <div class="mb-2 inline-flex w-full justify-between border-b-2 border-dashed border-gray-900 dark:border-gray-50">
            <h3 class="text-md mb-2 font-semibold text-gray-900 dark:text-white sm:text-lg">Total Payment</h3>
            <p class="text-md font-bold text-primary dark:text-secondary">
                {{ 'Rp ' . number_format($transaction->total_price, 0, ',', '.') }}
            </p>
        </div>
    </div>
    <form wire:submit="bookContact">
        <button class="mt-4 w-full rounded-md bg-primary px-4 py-2 font-bold text-white hover:bg-primary/80 dark:bg-secondary dark:text-black dark:hover:bg-secondary/80">
            Next
        </button>
    </form>
</div>

@script
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Set transaction creation time
            var transactionCreatedAt = new Date("{{ $this->transaction->created_at }}");

            // Calculate transaction end time (1 hour after transaction creation)
            var transactionEndTime = new Date(transactionCreatedAt.getTime() + 60 * 60 * 1000);

            // Update countdown every second
            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = transactionEndTime - now;

                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    new FilamentNotification()
                        .title('Transaction expired')
                        .danger()
                        .body('Transaction has expired.')
                        .send();
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "EXPIRED";
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 3000);
                }
            }, 1000);
        })
    </script>
@endscript
