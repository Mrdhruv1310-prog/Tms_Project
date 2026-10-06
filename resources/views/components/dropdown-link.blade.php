<a
    {{ $attributes->merge([
        'class' =>
            'group flex w-full items-center px-4 py-2 text-start text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition-colors duration-150 ease-in-out cursor-pointer',
    ]) }}>
    {{ $slot }}
</a>
