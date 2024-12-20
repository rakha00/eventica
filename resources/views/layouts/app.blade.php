@php
    $cwd = getcwd();
    $cssName = basename(glob($cwd . '/build/assets/*.css')[0], '.css');
    $jsName = basename(glob($cwd . '/build/assets/*.js')[0], '.js');
    $css = asset('build/assets/' . $cssName . '.css');
    $js = asset('build/assets/' . $jsName . '.js');
@endphp

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

        <!-- Styles -->
        @filamentStyles()
        @stack('styles')

        <!-- Vite -->
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
        <!-- Vite Build -->
        <link id="css" href="{{ $css }}" rel="stylesheet">
        <script src="{{ $js }}" id="js"></script>
    </head>

    <body class="font-sans antialiased">
        <div
            class="min-h-screen bg-gradient-to-t from-gray-50 via-sky-300 to-gray-50 dark:bg-gray-900 dark:bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] dark:bg-[size:24px_24px]">
            {{ $slot }}

            @livewire('notifications')
        </div>

        <!-- Scripts -->
        @filamentScripts()
        @stack('scripts')
    </body>

</html>
