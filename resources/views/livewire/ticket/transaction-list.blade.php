<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use Filament\Notifications\Notification;

new class extends Component {
    public $transactions;

    public function mount()
    {
        $this->transactions = Transaction::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
    }

    public function onSuccess()
    {
        Notification::make()->title('Yeay! Your payment has been successful')->success()->body('You can see your ticket here')->send();
        return redirect()->route('tickets');
    }

    public function payment($transaction)
    {
        if ($transaction['status'] == 'Pending') {
            Notification::make()->title('Whoops! it seems the snap token has not been created')->warning()->body('Please complete your transaction here.')->send();
            return $this->redirect(route('transaction-contact', ['eventSlug' => $transaction['event_package']['event']['slug'], 'packageSlug' => $transaction['event_package']['slug'], 'orderId' => $transaction['order_id']]));
        }
        if ($transaction['status'] == 'On payment') {
            if ($transaction['snap_token'] == null) {
                Notification::make()->title('Whoops! it seems the snap token has not been created')->warning()->body('Please complete your transaction here.')->send();
                return $this->redirect(route('transaction-payment', ['orderId' => $transaction['order_id']]));
            } else {
                $this->dispatch('snapJs', $transaction['snap_token']);
            }
        }
    }
}; ?>

<div>
    <div>
        <ul class="space-y-4">
            @if ($transactions->isEmpty())
                <p>You don't have any transactions.</p>
            @endif
            @foreach ($transactions as $transaction)
                <div>
                    <li class="relative flex flex-col items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 md:flex-row">
                        <div class="flex w-full flex-col">
                            <div class="flex justify-between gap-2">
                                <h5 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $transaction->eventPackage->event->title }}
                                    ({{ $transaction->eventPackage->title }})
                                </h5>
                                <p
                                    class="{{ $transaction->status == 'Expired'
                                        ? 'bg-red-500'
                                        : ($transaction->status == 'Pending' || $transaction->status == 'On payment'
                                            ? 'bg-yellow-300'
                                            : ($transaction->status == 'Completed'
                                                ? 'bg-green-500'
                                                : '')) }} max-h-6 rounded-full px-2 py-1 text-xs font-semibold text-white">
                                    {{ $transaction->status }}
                                </p>
                            </div>
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ 'Order ID: ' . $transaction->order_id }}</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y H:i') }}</p>
                                    <p class="font-bold text-secondary dark:text-primary">{{ 'Rp ' . number_format($transaction->total_price, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-end">
                                    @if ($transaction->status == 'Pending' || $transaction->status == 'On payment')
                                        <div>
                                            <button class="rounded-lg bg-green-500 px-6 py-1 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75"
                                                wire:click="payment({{ $transaction }})">Pay</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                </div>
            @endforeach
        </ul>
    </div>
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
                    $wire.$call('onSuccess');
                },
                // Optional
                onError: function(result) {
                    /* You may add your own js here, this is just example */
                    alert("payment failed!");
                    console.log(result);
                },
                onClose: function() {

                }
            });
        });
    </script>
@endscript
