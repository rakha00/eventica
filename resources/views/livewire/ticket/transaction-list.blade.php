<?php

use Livewire\Volt\Component;
use App\Models\Transaction;

new class extends Component {
    public $transactions;

    public function mount()
    {
        $this->transactions = Transaction::where('user_id', auth()->id())->get();
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
                    <li
                        class="relative flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <div class="flex flex-col">
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $transaction->eventPackage->event->title }}
                                ({{ $transaction->eventPackage->title }})
                            </h5>
                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ 'Order ID: ' . $transaction->order_id }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d F Y H:i') }}</p>
                            <p class="font-bold text-secondary dark:text-primary">{{ 'Rp ' . number_format($transaction->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex flex-col items-end space-y-6">
                            <span class="rounded-full bg-blue-500 px-2 py-1 text-xs font-semibold text-white">{{ $transaction->status }}</span>
                            <div>
                                <button class="rounded-lg bg-green-500 px-6 py-1 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">Pay</button>
                            </div>
                        </div>
                    </li>
                </div>
            @endforeach
        </ul>
    </div>
</div>
