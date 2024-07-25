<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">

        <!-- Theme Toggle -->
        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            document.addEventListener("DOMContentLoaded", function() {
                const themeToggle = document.getElementById('theme-toggle');
                if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                    if (themeToggle) {
                        themeToggle.checked = true;
                    }
                } else {
                    document.documentElement.classList.remove('dark');
                    if (themeToggle) {
                        themeToggle.checked = false;
                    }
                }
            });
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="build/assets/app-B25d2ddI.js"></script>
        <link href="build/assets/app-LAoRvosC.css" rel="stylesheet">
        <link href="build/assets/app-v2Sj8WLY.css" rel="stylesheet">
    </head>

    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 dark:bg-gray-900 sm:justify-center sm:pt-0">
            <div>
                <a href="/" wire:navigate>
                    <img class="h-28 w-auto fill-current text-gray-800 dark:text-gray-200" src="{{ asset('img/logo.webp') }}" alt="Logo">
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md dark:bg-gray-800 sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>

</html>
