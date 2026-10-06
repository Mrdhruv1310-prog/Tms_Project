@props(['value'])

<label
    {{ $attributes->merge([
        'class' =>
            'block font-semibold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 transition-colors duration-150 ease-in-out cursor-pointer',
    ]) }}>
    {{ $value ?? $slot }}
</label>
