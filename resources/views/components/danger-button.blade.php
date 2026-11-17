<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' =>
            'inline-flex items-center justify-center px-3.5 py-2 sm:px-4 sm:py-2.5 text-[11px] xs:text-xs sm:text-sm bg-red-600 hover:bg-red-700 active:bg-red-800 focus:bg-red-700 text-white font-bold uppercase tracking-widest rounded-xl shadow-md shadow-red-500/20 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-150 ease-in-out cursor-pointer',
    ]) }}>
    {{ $slot }}
</button>
