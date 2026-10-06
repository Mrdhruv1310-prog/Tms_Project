@props(['status'])

@if ($status)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        {{ $attributes->merge(['class' => 'font-semibold text-sm text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 p-3 rounded-xl shadow-xs']) }}>
        {{ $status }}
    </div>
@endif
