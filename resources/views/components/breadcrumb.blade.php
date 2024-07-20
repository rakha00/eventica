<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a class="inline-flex items-center text-sm font-medium text-gray-800 hover:text-gray-600 dark:text-gray-200 dark:hover:text-gray-400" href="{{ route('home') }}" wire:navigate>
                <x-heroicon-s-home class="size-4 me-2.5 text-gray-800 dark:text-gray-200" />
                Home
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <x-heroicon-o-chevron-right class="size-4 me-2.5 text-gray-800 dark:text-gray-200" />
                <a class="ms-1 text-sm font-medium text-gray-800 hover:text-gray-600 dark:text-gray-200 dark:hover:text-gray-400 md:ms-2" href="#">Events</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <x-heroicon-o-chevron-right class="size-4 me-2.5 text-gray-800 dark:text-gray-200" />
                <span class="ms-1 text-sm font-bold text-primary dark:text-secondary md:ms-2">Detail Event</span>
            </div>
        </li>
    </ol>
</nav>
