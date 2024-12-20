<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};
?>

<header class="relative z-10 border-b-2 border-gray-200 border-b-primary bg-white dark:border-b-secondary dark:bg-gray-800">
    <div class="mx-auto flex max-w-screen-xl flex-wrap items-center justify-between p-4">
        <a class="flex items-center space-x-3" href="{{ route('home') }}" wire:navigate>
            <img class="h-10 md:h-14" src="{{ asset('img/logo.webp') }}" alt="Logo" />
        </a>
        <div class="flex items-center space-x-3 md:order-2 md:space-x-0">
            <button class="flex rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-primary dark:focus:ring-secondary md:me-0" id="user-menu-button" data-dropdown-toggle="user-dropdown"
                data-dropdown-placement="bottom" type="button" aria-expanded="false">
                <span class="sr-only">Open user menu</span>
                <img class="h-10 w-10 rounded-full" src="{{ asset('storage/profile/default.webp') }}" alt="User">
            </button>
            <!-- Dropdown menu -->
            <div class="z-50 my-4 hidden list-none divide-y divide-gray-100 rounded-lg bg-white text-base shadow dark:divide-gray-600 dark:bg-gray-700" id="user-dropdown">
                @auth
                    <div class="px-4 py-3">
                        <span class="block text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="block truncate text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</span>
                    </div>
                @endauth
                <div class="flex items-center space-x-3">
                    <button class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" id="theme-toggle-light"><x-gmdi-light-mode-r
                            class="size-6" /></button>
                    <button class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" id="theme-toggle-dark"><x-gmdi-dark-mode-r
                            class="size-6" /></button>
                    <button class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" id="theme-toggle-system"><x-codicon-color-mode
                            class="size-6" /></button>
                </div>
                @guest
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        <li>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" href="{{ route('login') }}"
                                wire:navigate>Login</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" href="{{ route('register') }}"
                                wire:navigate>Register</a>
                        </li>
                    </ul>
                @endguest
                @auth
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        <li>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" href="{{ route('tickets') }}">My
                                Tickets</a>
                        </li>
                        <li>
                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" href="{{ route('profile') }}">Settings</a>
                        </li>
                        <li>
                            <a class="block cursor-pointer px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white" wire:click="logout">Log
                                Out</a>
                        </li>
                    </ul>
                @endauth
            </div>
            <button
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:ring-2 focus:ring-primary dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-secondary md:hidden"
                data-collapse-toggle="navbar-user" type="button" aria-controls="navbar-user" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
        <div class="hidden w-full items-center justify-between md:order-1 md:flex md:w-auto" id="navbar-user" x-data="{ activePage: '{{ Route::currentRouteName() }}' }">
            <ul
                class="mt-4 flex flex-col rounded-lg border-2 border-primary bg-gray-50 p-4 font-medium dark:border-secondary dark:bg-gray-800 md:mt-0 md:flex-row md:space-x-8 md:border-0 md:bg-white md:p-0 md:dark:bg-gray-800">
                <li>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700 md:p-0 md:hover:bg-transparent md:hover:text-primary md:dark:hover:bg-transparent md:dark:hover:text-secondary"
                        href="{{ route('home') }}" aria-current="page" :class="activePage === 'home' ? ' text-primary dark:text-secondary' : 'text-gray-900 dark:text-white'"
                        wire:navigate>Home</a>
                </li>
                <li>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700 md:p-0 md:hover:bg-transparent md:hover:text-primary md:dark:hover:bg-transparent md:dark:hover:text-secondary"
                        href="{{ route('search') }}" :class="activePage === 'search' ? 'text-primary dark:text-secondary' : 'text-gray-900 dark:text-white'" wire:navigate>All Events</a>
                </li>
                <li>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700 md:p-0 md:hover:bg-transparent md:hover:text-primary md:dark:hover:bg-transparent md:dark:hover:text-secondary"
                        href="{{ route('about') }}" :class="activePage === 'about' ? ' text-primary dark:text-secondary' : 'text-gray-900 dark:text-white'" wire:navigate>About Us</a>
                </li>
                <li>
                    <a class="block rounded px-3 py-2 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700 md:p-0 md:hover:bg-transparent md:hover:text-primary md:dark:hover:bg-transparent md:dark:hover:text-secondary"
                        href="{{ route('how-to-order') }}" :class="activePage === 'how-to-order' ? ' text-primary dark:text-secondary' : 'text-gray-900 dark:text-white'" wire:navigate>How to
                        Order</a>
                </li>
            </ul>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        document.getElementById('theme-toggle-light').addEventListener('click', function() {
            localStorage.setItem('color-theme', 'light');
            document.documentElement.classList.remove('dark');
        });

        document.getElementById('theme-toggle-dark').addEventListener('click', function() {
            localStorage.setItem('color-theme', 'dark');
            document.documentElement.classList.add('dark');
        });

        document.getElementById('theme-toggle-system').addEventListener('click', function() {
            localStorage.removeItem('color-theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
@endpush
