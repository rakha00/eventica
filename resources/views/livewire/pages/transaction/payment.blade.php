<?php

use App\Models\Transaction;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

new class extends Component implements HasForms, HasActions {
    use InteractsWithActions;
    use InteractsWithForms;
    public $orderId;

    #[Layout('layouts.app')]
    public function mount()
    {
        $this->orderId = request()->route('orderId');
    }

    public function openModal()
    {
        $this->dispatch('open-modal', id: 'delete-transaction');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', id: 'delete-transaction');
    }

    public function deleteTransaction()
    {
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        $status = \Midtrans\Transaction::status($this->orderId);

        if ($status->transaction_status == 'pending') {
            $transaction = Transaction::where('order_id', $this->orderId)->first();
            $transaction->eventPackage->increment('remaining', $transaction->quantity);
            $transaction->update(['status' => 'Cancelled']);
        }

        $transaction->delete();
        return redirect()->route('home');
    }
};
?>

<div>
    <livewire:layout.header />

    <div class="mx-auto flex justify-center px-4 py-10">
        <div class="w-full max-w-3xl rounded-lg bg-gray-100 shadow-lg dark:bg-gray-900">
            <div class="flex justify-between gap-4 rounded-t-md bg-gray-50 p-6 shadow-md dark:bg-gray-800">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Complete Payment</h1>
                <p class="flex items-center text-end text-red-600 hover:text-red-700 dark:text-red-600 dark:hover:text-red-800" wire:click="openModal">Cancel order</p>
            </div>
            <livewire:transaction.payment />
        </div>
    </div>

    <x-filament::modal id="delete-transaction" icon="heroicon-o-trash" icon-color="danger" alignment="center" :close-by-clicking-away="false" :close-button="false">
        <x-slot name="heading">
            Cancel Order
        </x-slot>

        <x-slot name="description">
            Are you sure wanna cancel this order?
        </x-slot>
        {{-- Modal content --}}
        <div class="flex justify-center gap-2">
            {{-- Modal footer actions --}}
            <x-filament::button wire:click="closeModal" color="gray">
                Close
            </x-filament::button>
            <x-filament::button wire:click="deleteTransaction" color="danger">
                Confirm
            </x-filament::button>
        </div>
    </x-filament::modal>

</div>
