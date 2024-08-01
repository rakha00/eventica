<x-app-layout>
    <livewire:layout.header />

    <div class="mx-auto flex justify-center px-4 py-10">
        <div class="w-full max-w-3xl rounded-lg bg-gray-100 shadow-lg dark:bg-gray-900">
            <div class="flex justify-between gap-4 rounded-t-md bg-gray-50 p-6 shadow-md dark:bg-gray-800">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Book Contact</h1>
            </div>
            <livewire:transaction.contact />
        </div>
    </div>
</x-app-layout>
