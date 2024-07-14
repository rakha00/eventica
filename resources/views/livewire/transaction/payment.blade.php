<?php

use App\Models\Transaction;
use Livewire\Volt\Component;

new class extends Component {
    public $transaction;
    public $paymentType;
    public $snapToken;

    public function mount()
    {
        $this->transaction = Transaction::where('order_id', request()->route('orderId'))->where('status', 'On payment')->firstOrFail();
    }

    public function createMidtransTransaction()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $transaction_details = [
            'order_id' => $this->transaction->order_id,
            'gross_amount' => $this->transaction->total_price,
        ];

        $item_details = [
            [
                'id' => $this->transaction->eventPackage->id,
                'price' => $this->transaction->eventPackage->price,
                'quantity' => $this->transaction->quantity,
                'name' => $this->transaction->eventPackage->event->title . ' (' . $this->transaction->eventPackage->title . ')',
                'category' => $this->transaction->eventPackage->event->eventCategory->title,
            ],
        ];

        $customer_details = [
            'first_name' => $this->transaction->user->name,
            'email' => $this->transaction->user->email,
            'phone' => $this->transaction->user->phone,
        ];

        $expiry = [
            'duration' => floor(60 - $this->transaction->created_at->diffInMinutes(now())),
            'unit' => 'minutes',
        ];

        $page_expiry = [
            'duration' => floor(60 - $this->transaction->created_at->diffInMinutes(now())),
            'unit' => 'minutes',
        ];

        if ($this->paymentType == 'gopay') {
            $paymentType = ['gopay'];
        }

        if ($this->paymentType == 'bca_va') {
            $paymentType = ['bca_va'];
        }

        if ($this->paymentType == 'alfamart') {
            $paymentType = ['alfamart'];
        }

        if ($this->paymentType == 'other_payment') {
            $paymentType = null;
        }

        $transaction_data = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
            'enabled_payments' => $paymentType,
            'expiry' => $expiry,
            'page_expiry' => $page_expiry,
        ];

        try {
            $this->snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
            $this->updateTransaction();
            $this->dispatch('snapJs', $this->snapToken);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updateTransaction()
    {
        $this->transaction->update([
            'snap_token' => $this->snapToken,
        ]);
    }

    public function payment()
    {
        if ($this->paymentType == null) {
            session()->flash('error', 'Payment method is required');
            return;
        }
        $this->createMidtransTransaction();
    }
}; ?>

<div class="p-6">
    <div>
        <h2 class="mb-2 text-2xl font-semibold text-gray-900 dark:text-white">Payment Method</h2>
        <p class="mb-2 text-gray-500 dark:text-gray-400">
            Please select the payment method for your transaction.
            Note: when you have selected the payment method, you can't change it.
        </p>

        <!-- Countdown -->
        <div class="my-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800" wire:ignore>
            <div class="inline-flex w-full flex-col items-center justify-between gap-2 sm:flex-row sm:gap-0">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Complete Payment In</h3>
                <p class="shadow-slate-4000 w-fit rounded-md bg-gray-100 px-2 py-1 text-center text-secondary shadow-inner dark:bg-gray-700 dark:text-primary dark:shadow-slate-500" id="countdown">
                </p>
            </div>
        </div>

        @if (session()->has('error'))
            <p class="w-fit rounded-full bg-red-500 px-2 py-1 text-xs text-white" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 3000)" x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" wire:poll.5s>
                {{ session()->get('error') }}
            </p>
        @endif
    </div>

    <!--  Select Payment -->
    <div class="mt-4 space-y-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800">
        <p class="w-fit rounded-full bg-green-500 px-4 py-1 text-xs text-white">Recommended</p>

        <div class="mb-4 flex items-center">
            <input class="h-4 w-4 bg-gray-100 text-secondary focus:ring-0 dark:bg-gray-700 dark:text-primary" id="gopay" name="payment_type" type="radio" value="gopay" wire:model="paymentType">
            <label class="ms-2 font-medium text-gray-900 dark:text-gray-50" for="gopay">Gopay</label>
        </div>
        <div class="flex items-center">
            <input class="h-4 w-4 bg-gray-100 text-secondary focus:ring-0 dark:bg-gray-700 dark:text-primary" id="bca" name="payment_type" type="radio" value="bca_va" wire:model="paymentType">
            <label class="ms-2 font-medium text-gray-900 dark:text-gray-50" for="bca">BCA</label>
        </div>
        <div class="flex items-center">
            <input class="h-4 w-4 bg-gray-100 text-secondary focus:ring-0 dark:bg-gray-700 dark:text-primary" id="alfamart" name="payment_type" type="radio" value="alfamart"
                wire:model="paymentType">
            <label class="ms-2 font-medium text-gray-900 dark:text-gray-50" for="alfamart">Alfamart</label>
        </div>
        <div class="flex items-center">
            <input class="h-4 w-4 bg-gray-100 text-secondary focus:ring-0 dark:bg-gray-700 dark:text-primary" id="other_payment" name="payment_type" type="radio" value="other_payment"
                wire:model="paymentType">
            <label class="ms-2 font-medium text-gray-900 dark:text-gray-50" for="other_payment">Other Payment</label>
        </div>
    </div>
    <div class="mt-4 rounded-md bg-gray-50 p-4 shadow-lg dark:bg-gray-800" x-data="{ open: false }">
        <!-- Total Payment -->
        <p class="mb-4 w-full rounded-full bg-yellow-200 px-2 py-1 text-center text-sm text-yellow-600 sm:text-base">
            ORDER ID:
            {{ $this->transaction->order_id }}
        </p>
        <div class="flex items-center justify-between border-b-2 border-dashed pb-4" x-on:click="open = !open">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-secondary p-2 dark:bg-primary">
                    <x-heroicon-c-ticket class="size-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <p class="font-bold text-gray-900 dark:text-gray-50">
                        {{ $this->transaction->eventPackage->event->title }}
                        ({{ $this->transaction->eventPackage->title }})
                    </p>
                    <p class="text-gray-500 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($this->transaction->eventPackage->start_valid)->format('l, d F Y') }}
                    </p>
                </div>
            </div>
            <x-heroicon-s-chevron-down class="size-6 text-gray-900 dark:text-white" x-show="open" />
            <x-heroicon-o-chevron-right class="size-6 text-gray-900 dark:text-white" x-show="!open" />
        </div>
        <div class="my-2 rounded-md bg-gray-100 p-4 shadow-inner shadow-slate-400 dark:bg-gray-700 dark:shadow-slate-500" x-show="open">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="w-full border-b-2 border-slate-400 pb-2 font-semibold text-gray-900 dark:text-gray-50">Order
                    Summary
                </h3>
            </div>
            <div class="mb-4 flex items-center">
                <img class="h-16 w-16 rounded-md" src="{{ $this->transaction->eventPackage->event->image }}" alt="Event Image">
                <div class="ml-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50">
                        {{ $this->transaction->eventPackage->event->title }}
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-300">
                        {{ $this->transaction->eventPackage->event->location }} |
                        {{ $this->transaction->eventPackage->event->start_event }}
                    </p>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-900 dark:text-gray-50">Ticket Type</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">{{ 'Pax (x' . $this->transaction->quantity . ')' }}
                </p>
            </div>
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-900 dark:text-gray-50">Validity Date</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">
                    @if (\Carbon\Carbon::parse($this->transaction->eventPackage->start_valid)->format('d M Y') == \Carbon\Carbon::parse($this->transaction->eventPackage->end_valid)->format('d M Y'))
                        {{ \Carbon\Carbon::parse($this->transaction->eventPackage->start_valid)->format('d M Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($this->transaction->eventPackage->start_valid)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($this->transaction->eventPackage->end_valid)->format('d M Y') }}
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-900 dark:text-gray-50">Contact Details</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">Name: {{ $this->transaction->user->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">Email: {{ $this->transaction->user->email }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">Phone: {{ $this->transaction->user->phone }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-300">ID Card:
                    {{ $this->transaction->user->identity_card_number }}
                </p>
            </div>
        </div>
        <div class="my-2 inline-flex w-full items-center justify-between">
            <p class="text-gray-900 dark:text-gray-50">Total Payment</p>
            <p class="font-bold text-secondary dark:text-primary">
                {{ 'Rp ' . number_format($this->transaction->total_price, 0, ',', '.') }}
            </p>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-300">
            by proceeding with the payment process, you agree to the <span class="font-bold">Terms & Conditions</span>
            and <span class="font-bold">Privacy Policy</span> of eventica.com
        </p>
    </div>
    <button class="mt-4 w-full rounded-md bg-secondary px-4 py-2 font-bold text-white hover:bg-secondary/80 dark:bg-primary dark:text-gray-900 dark:hover:bg-primary/80" wire:click="payment">
        Pay Now
    </button>
</div>

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@script
    <script>
        document.addEventListener('snapJs', (event) => {
            snap.pay(event.detail[0], {
                // Optional
                onSuccess: function(result) {
                    /* You may add your own js here, this is just example */
                    alert("payment success!");
                    console.log(result);
                },
                // Optional
                onPending: function(result) {
                    /* You may add your own js here, this is just example */
                    alert("wating your payment!");
                    console.log(result);
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    alert("payment failed!");
                    console.log(result);
                },
                onClose: function() {
                    /* You may add your own implementation here */

                }
            });
        });
    </script>
@endscript

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
                        .title('Transaction has expired')
                        .danger()
                        .body('You will be redirected to the homepage.')
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
