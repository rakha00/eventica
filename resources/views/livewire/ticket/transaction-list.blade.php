<?php

use Livewire\Volt\Component;
use App\Models\Transaction;

new class extends Component {
    public $transactions;

    public function mount()
    {
        $this->transaction = Transaction::where('user_id', auth()->id())->get();
    }
}; ?>

<div>
    <div>
        <ul class="space-y-4">
            @foreach ($transactions as $transaction)
                <div>
                    <li
                        class="relative flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <div class="flex flex-col space-y-2">
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white">{{ $transaction->title }}</h5>
                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $transaction->date }}</p>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="rounded-full bg-blue-500 px-2 py-1 text-xs font-semibold text-white">{{ $transaction->status }}</span>
                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $transaction->amount }}</p>
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
