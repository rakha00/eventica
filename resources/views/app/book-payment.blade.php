<x-app-layout>
    <livewire:layout.header />

    <div class="container mx-auto my-8 flex justify-center p-4">
        <div class="w-full max-w-3xl rounded-lg border-2 border-tertiary bg-gray-900">
            <div class="flex justify-between gap-4 rounded-md bg-gray-800 p-6">
                <h1 class="text-2xl font-bold text-white">Complete Payment</h1>
                <a class="inline-flex items-center text-end text-red-500 hover:text-red-600" href="#">Cancel order</a>
            </div>
            <div class="p-6">
                <livewire:transaction.book-payment />
            </div>
        </div>
    </div>
</x-app-layout>
