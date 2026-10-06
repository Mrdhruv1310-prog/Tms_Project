@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'w-full rounded-2xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-800 placeholder-slate-400 outline-none transition-all duration-150 ease-in-out focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500 dark:focus:border-blue-500 dark:focus:ring-blue-900/40 disabled:opacity-60 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:cursor-not-allowed shadow-xs',
]) !!}>
