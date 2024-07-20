<?php

use function Livewire\Volt\layout;

layout('layouts.app');
?>
<div>
    <livewire:layout.header />

    <main>
        <div class="mx-auto mb-10 mt-4 min-h-screen max-w-7xl space-y-2 px-6">
            <div class="mx-auto flex justify-center px-4 py-10">
                <div class="w-full max-w-6xl rounded-lg bg-gray-100 shadow-lg dark:bg-gray-900" x-data="{ tab: 'tickets' }">
                    <div class="sm:hidden">
                        <label class="sr-only" for="tabs">Select Tabs</label>
                        <select
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-secondary dark:focus:ring-secondary"
                            id="tabs" x-model="tab">
                            <option value="tickets">Tickets</option>
                            <option value="transactions">Transactions</option>
                        </select>
                    </div>
                    <ul class="hidden rounded-lg text-center text-sm font-medium text-gray-500 shadow dark:divide-gray-700 dark:text-gray-400 sm:flex">
                        <li class="w-full focus-within:z-10">
                            <button class="inline-block w-full rounded-s-lg border-r border-gray-200 p-4 focus:outline-none focus:ring-0 dark:border-gray-700" x-on:click="tab = 'tickets'"
                                :class="{ 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white': tab === 'tickets', 'bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': tab !== 'tickets' }">Tickets</button>
                        </li>
                        <li class="w-full focus-within:z-10">
                            <button class="inline-block w-full rounded-e-lg border-r border-gray-200 p-4 focus:outline-none focus:ring-0 dark:border-gray-700" x-on:click="tab = 'transactions'"
                                :class="{ 'bg-gray-100 text-gray-900 dark:bg-gray-700 dark:text-white': tab === 'transactions', 'bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': tab !== 'transactions' }">Transactions</button>
                        </li>
                    </ul>
                    <div class="p-4" x-show="tab === 'tickets'">
                        <!-- Tickets content goes here -->
                        <livewire:ticket.ticket-list>
                    </div>
                    <div class="p-4" x-show="tab === 'transactions'">
                        <livewire:ticket.transaction-list>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <livewire:layout.footer />
</div>
