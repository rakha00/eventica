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
        @livewireStyles
        @stack('styles')
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-b from-white to-slate-300 dark:bg-gradient-to-b dark:from-gray-950 dark:to-slate-800">
            {{ $slot }}
        </div>
        @livewireScripts
        @stack('scripts')
    </body>

</html>
