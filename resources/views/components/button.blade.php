<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'rounded-lg bg-primary px-8 py-2.5 font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-0 dark:bg-secondary dark:text-gray-900 dark:hover:bg-secondary/90']) }}>
    {{ $slot }}
</button>
