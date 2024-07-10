<?php

use Livewire\Volt\Component;

new class extends Component
{
    public $transaction;
    public $status;

    public function mount()
    {
        $this->status = \Midtrans\Transaction::status($this->transaction->order_id);
    }

    public function dehydrate()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        $this->status = \Midtrans\Transaction::status($this->transaction->order_id);
    }
    public function hydrate()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        $this->status = \Midtrans\Transaction::status($this->transaction->order_id);
        if ($this->status->transaction_status == 'settlement') {
            sleep(3);
            $this->redirect(route('home'), navigate: true);
        }
    }

    public function checkStatus()
    {
    }


    public function later()
    {
        $this->redirect(route('home'), navigate: true);
    }
}; ?>

<div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden backdrop-blur" id="static-modal" data-modal-backdrop="static" aria-hidden="true" tabindex="-1">
    <div class="relative max-h-full w-full max-w-md p-4">
        <!-- Modal content -->
        <div class="relative rounded-lg bg-white shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between rounded-t border-b p-1 dark:border-gray-600 md:p-2">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <img class="h-12" src="{{ asset('img/logoText.webp') }}" alt="Logo">
                </h3>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <p class="text-lg font-bold dark:text-white">
                    {{ 'Rp ' . number_format($transaction->total_price, 0, ',', '.') }}
                </p>
                <p class="dark:text-white">{{ 'Order ID: ' . $transaction->order_id }}</p>
                @if ($status->transaction_status == 'pending')
                <div class="rounded-md bg-gray-50 p-4 shadow-md dark:bg-gray-800">
                    <div class="inline-flex w-full flex-col items-center justify-between gap-2 sm:flex-row sm:gap-0">
                        <h3 class="text-xs font-semibold text-gray-900 dark:text-white">Complete Payment In</h3>
                        <p class="w-fit rounded-md bg-gray-100 px-2 py-1 text-center text-xs text-secondary shadow-inner shadow-slate-400 dark:bg-gray-700 dark:text-primary dark:shadow-slate-500" id="countdown" id>
                        </p>
                    </div>
                </div>
                @endif
                <div class="my-2 flex w-full flex-col justify-center">
                    @if ($status->transaction_status == 'pending')
                    <a class="mb-2 inline-flex w-full justify-center" href="https://simulator.sandbox.midtrans.com/qris/index" target="_blank">
                        <img class="w-64" src="https://api.sandbox.midtrans.com/v2/gopay/{{ $status->transaction_id }}/qr-code" alt="Gopay QR Code">
                    </a>
                    <div x-data="{ open: false }">
                        <p class="inline-flex cursor-pointer items-center text-secondary dark:text-primary" x-on:click="open = !open">
                            <x-heroicon-o-question-mark-circle class="size-5 me-2" />
                            How to pay (Click QR to simulate payment)
                        </p>
                        <p class="dark:text-white" x-show="open" x-transition:enter="transition-opacity ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            1. Open your Gojek, GoPay or other e-wallet app.<br>
                            2. Download or scan QRIS on your monitor.<br>
                            3. Confirm payment in the app.<br>
                            4. Payment completed.</p>
                    </div>
                    @else
                    <img class="p-10" src="{{ asset('img/checkPayment.webp') }}" alt="checkPayment">
                    <p class="text-center font-medium text-gray-900 dark:text-white">Payment completed. Redirecting...
                    </p>
                    @endif
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center justify-center rounded-b border-t border-gray-200 p-1 dark:border-gray-600 md:p-2">
                <button class="rounded-lg bg-secondary px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-secondary/80 focus:outline-none focus:ring-0 dark:bg-primary dark:hover:bg-primary/80" type="button" wire:click="checkStatus" x-on:click="redirecting = true">Check Status</button>
                <button class="ms-3 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700" type="button" wire:click="later">Later</button>
            </div>
        </div>
    </div>
</div>