<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'rounded-lg bg-secondary px-8 py-2.5 font-medium text-white hover:bg-secondary/90 focus:outline-none focus:ring-0 dark:bg-primary dark:text-gray-950 dark:hover:bg-primary/90']) }}>
    {{ $slot }}
</button>
