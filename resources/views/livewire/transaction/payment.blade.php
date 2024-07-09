<?php

use App\Models\Transaction;
use Livewire\Volt\Component;

new class extends Component {
    public $transaction;
    public $paymentType;
    public $expiry_time;
    public $snapToken;

    public function mount(Transaction $transaction)
    {
        $this->transaction = Transaction::where("order_id", request()->route("orderId"))->first();
        if (!$this->transaction) {
            abort(404);
        }

        \Midtrans\Config::$serverKey = config("midtrans.server_key");
        $status = \Midtrans\Transaction::status($this->transaction->order_id);
        $this->expiry_time = strtotime($status->expiry_time) * 1000;

        // dd($this->expiry_time);
    }

    public function createMidtransSnapToken()
    {
        \Midtrans\Config::$serverKey = config("midtrans.server_key");
        \Midtrans\Config::$isProduction = config("midtrans.is_production");
        \Midtrans\Config::$isSanitized = config("midtrans.is_sanitized");
        \Midtrans\Config::$is3ds = config("midtrans.is_3ds");

        $transaction_details = [
            "order_id" => $this->transaction->order_id,
            "gross_amount" => $this->transaction->total_price,
        ];

        $item_details = [
            [
                "id" => $this->transaction->package->id,
                "price" => $this->transaction->package->price,
                "quantity" => $this->transaction->quantity,
                "name" => $this->transaction->package->event->title . " (" . $this->transaction->package->title . ")",
                "category" => $this->transaction->package->event->category->title,
            ],
        ];

        $customer_details = [
            "first_name" => $this->transaction->user->name,
            "email" => $this->transaction->user->email,
            "phone" => $this->transaction->user->phone,
        ];

        $expiry = [
            "duration" => 1,
            "unit" => "hours",
        ];

        $transaction_data = [
            "transaction_details" => $transaction_details,
            "item_details" => $item_details,
            "customer_details" => $customer_details,
            "expiry" => $expiry,
        ];

        if ($this->paymentType !== "other_payment") {
            $transaction_data["enabled_payments"] = [$this->paymentType];
        }

        try {
            if (!$this->transaction->snap_token) {
                $this->snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
                $this->transaction->update([
                    "snap_token" => $this->snapToken,
                ]);
                $this->dispatch("snapToken", $this->snapToken);
            } else {
                $this->dispatch("snapToken", $this->transaction->snap_token);
            }
        } catch (\Exception $e) {
            session()->flash("error", $e->getMessage());
        }
    }

    public function payment()
    {
        if ($this->paymentType == null) {
            session()->flash("error", "Payment method is required");
            return;
        }
        $this->createMidtransSnapToken();
    }
}; ?>

<div>
    <div>
        <h2 class="mb-2 text-2xl font-semibold text-white">Payment Method</h2>
        <p class="mb-2 text-xs text-gray-400">Please select the payment method for your transaction. Once transaction is
            created, you can't change the payment method.</p>
        @if (!session()->has("expiry_time"))
            <div class="mt-4 rounded-md bg-gray-800 p-4">
                <div class="inline-flex w-full items-center justify-between">
                    <h3 class="text-xs font-semibold text-white sm:text-lg">Complete Payment In</h3>
                    <p class="rounded-md bg-gray-700 px-2 py-1 text-xs text-tertiary sm:text-base" id="countdown" id>
                    </p>
                </div>
            </div>
        @endif
        @if (session()->has("error"))
            <p class="w-fit rounded-full bg-red-500 px-2 py-1 text-xs text-white">{{ session()->get("error") }}</p>
        @endif
    </div>

    <!--  Select Payment -->
    <div class="mt-4 space-y-4 rounded-md bg-gray-800 p-4">
        <p class="w-28 rounded-full bg-green-500 px-2 py-1 text-xs text-white">Recommended</p>

        <div class="mb-4 flex items-center">
            <input class="h-4 w-4 border-gray-600 bg-gray-700 text-primary ring-offset-gray-800 focus:ring-2 focus:ring-tertiary" id="gopay" name="payment_type" type="radio" value="gopay"
                wire:model="paymentType">
            <label class="ms-2 text-sm font-medium text-gray-300" for="gopay">Gopay</label>
        </div>
        <div class="flex items-center">
            <input class="h-4 w-4 border-gray-600 bg-gray-700 text-primary ring-offset-gray-800 focus:ring-2 focus:ring-tertiary" id="bca" name="payment_type" type="radio" value="bca_va"
                wire:model="paymentType">
            <label class="ms-2 text-sm font-medium text-gray-300" for="bca">BCA</label>
        </div>
        <div class="flex items-center">
            <input class="h-4 w-4 border-gray-600 bg-gray-700 text-primary ring-offset-gray-800 focus:ring-2 focus:ring-tertiary" id="other_qris" name="payment_type" type="radio" value="other_qris"
                wire:model="paymentType">
            <label class="ms-2 text-sm font-medium text-gray-300" for="other_qris">Other QRIS</label>
        </div>
        <div class="flex items-center">
            <input class="h-4 w-4 border-gray-600 bg-gray-700 text-primary ring-offset-gray-800 focus:ring-2 focus:ring-tertiary" id="other_payment" name="payment_type" type="radio"
                value="other_payment" wire:model="paymentType">
            <label class="ms-2 text-sm font-medium text-gray-300" for="other_payment">Other Payment</label>
        </div>
    </div>
    <div class="mt-4 rounded-md bg-gray-800 p-4" x-data="{ open: false }">
        <!-- Total Payment -->
        <p class="mb-4 w-full rounded-full bg-yellow-200 px-2 py-1 text-center text-xs text-yellow-600">ORDER ID:
            {{ $this->transaction->order_id }}
        </p>
        <div class="flex items-center justify-between border-b-2 border-dashed pb-4">
            <div class="flex items-center gap-2">
                <div class="rounded-full bg-tertiary p-2">
                    <x-heroicon-c-ticket class="size-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <p class="text-sm font-bold text-white">{{ $this->transaction->package->event->title }}
                        ({{ $this->transaction->package->title }})</p>
                    <p class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($this->transaction->package->start_valid)->format("l, d F Y") }}
                    </p>
                </div>
            </div>
            <x-heroicon-s-chevron-down class="size-4 text-white" x-show="open" x-on:click="open = !open" />
            <x-heroicon-o-chevron-right class="size-4 text-white" x-show="!open" x-on:click="open = !open" />
        </div>
        <div class="my-2 rounded-md bg-gray-700 p-4" x-show="open">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-md w-full border-b-2 border-slate-400 pb-2 font-semibold text-white">Order Summary
                </h3>
            </div>
            <div class="mb-4 flex items-center">
                <img class="h-16 w-16 rounded-md" src="{{ $this->transaction->package->event->image }}" alt="Event Image">
                <div class="ml-4">
                    <h4 class="text-sm font-semibold text-white">{{ $this->transaction->package->event->title }}</h4>
                    <p class="text-xs text-gray-400">{{ $this->transaction->package->event->location }} |
                        {{ $this->transaction->package->event->start_event }}</p>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-xs font-semibold text-white">Ticket Type</p>
                <p class="text-xs text-gray-400">{{ "Pax (x" . $this->transaction->quantity . ")" }}</p>
            </div>
            <div class="mb-4">
                <p class="text-xs font-semibold text-white">Validity Date</p>
                <p class="text-xs text-gray-400">
                    @if (\Carbon\Carbon::parse($this->transaction->package->start_valid)->format("d M Y") == \Carbon\Carbon::parse($this->transaction->package->end_valid)->format("d M Y"))
                        {{ \Carbon\Carbon::parse($this->transaction->package->start_valid)->format("d M Y") }}
                    @else
                        {{ \Carbon\Carbon::parse($this->transaction->package->start_valid)->format("d M Y") . " - " . \Carbon\Carbon::parse($this->transaction->package->end_valid)->format("d M Y") }}
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold text-white">Contact Details</p>
                <p class="text-xs text-gray-400">Name: {{ $this->transaction->user->name }}</p>
                <p class="text-xs text-gray-400">Email: {{ $this->transaction->user->email }}</p>
                <p class="text-xs text-gray-400">Phone: {{ $this->transaction->user->phone }}</p>
                <p class="text-xs text-gray-400">ID Card: {{ $this->transaction->user->identity_card_number }}</p>
            </div>
        </div>
        <div class="my-2 inline-flex w-full items-center justify-between">
            <p class="text-sm text-white sm:text-base">Total Payment</p>
            <p class="text-xs font-bold text-tertiary sm:text-base">
                {{ "Rp " . number_format($this->transaction->package->price, 0, ",", ".") }}</p>
        </div>
        <p class="text-xs text-white">
            by proceeding with the payment process, you agree to the <span class="font-bold">Terms & Conditions</span>
            and <span class="font-bold">Privacy Policy</span> of eventica.com
        </p>
    </div>
    <form wire:submit="payment">
        <button class="mt-4 w-full rounded-md bg-tertiary px-4 py-2 font-bold text-black hover:bg-tertiary/80" id="pay-button">
            Pay Now
        </button>
    </form>
</div>
@push("scripts")
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config("midtrans.client_key") }}"></script>
    <script type="text/javascript">
        document.addEventListener('livewire:init', function() {
            Livewire.on('snapToken', function(snapToken) {
                snap.pay(snapToken[0], {
                    onSuccess: function(result) {
                        console.log('success');
                        console.log(result);
                    },
                    onPending: function(result) {
                        console.log('pending');
                        console.log(result);
                    },
                    onError: function(result) {
                        console.log('error');
                        console.log(result);
                    },
                    onClose: function() {
                        console.log('customer closed the popup without finishing the payment');
                    }
                });
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countdown/2.6.0/countdown.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var countDownDate = {{ $this->expiry_time }};


            countdown(
                new Date(countDownDate),
                function(ts) {
                    var countdownElement = document.getElementById("countdown");
                    if (countdownElement) {
                        countdownElement.innerHTML = ts.hours + "h " + ts.minutes + "m " + ts.seconds + "s ";
                    }
                },
                countdown.HOURS | countdown.MINUTES | countdown.SECONDS
            );
        });
    </script>
@endpush
