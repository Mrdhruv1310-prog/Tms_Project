@persist('navigate-spinner')
    <div id="loader" x-data="{ navigating: false }" x-init="document.addEventListener('livewire:navigating', () => navigating = true);
    document.addEventListener('livewire:navigated', () => navigating = false);" x-show="navigating"
        x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="z-index: 9999999; display: none;"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center pointer-events-none">

        <div
            class="flex flex-col items-center gap-3 p-4 rounded-2xl bg-white/80 dark:bg-gray-800/80 shadow-2xl border border-white/20">
            <div id="loader-center"
                class="w-12 h-12 border-4 border-blue-600/20 border-t-blue-600 rounded-full animate-spin"></div>
        </div>
    </div>
@endpersist
