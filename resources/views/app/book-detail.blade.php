<x-app-layout>

    <livewire:layout.header />

    <div class="container mx-auto my-8 flex justify-center p-4">
        <div class="w-full max-w-3xl rounded-lg border-2 border-tertiary bg-gray-900">
            <div class="flex justify-between gap-4 rounded-md bg-gray-800 p-6">
                <h1 class="text-xl font-bold text-white sm:text-2xl">Book Ticket</h1>
                <a class="flex items-center text-end text-red-500 hover:text-red-600" href="{{ route("event-detail", request()->eventSlug) }}">Cancel order</a>
            </div>
            <div class="p-6">
                <livewire:transaction.book-detail />
            </div>
        </div>
    </div>

</x-app-layout>
