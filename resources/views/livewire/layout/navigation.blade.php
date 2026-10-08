<nav x-data="{ open: false }" x-cloak
    class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700/80 shadow-xs transition-colors duration-150">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- LEFT SECTION -->
            <div class="flex items-center gap-3">

                <!-- Mobile Toggle Button -->
                <button @click="open = !open" type="button"
                    class="sm:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-hidden transition-colors duration-150">

                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2.5 group">

                    <x-application-logo
                        class="h-9 w-auto text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform duration-150" />

                    <span class="hidden sm:block text-lg font-bold text-gray-800 dark:text-white tracking-tight">
                        Task Manager
                    </span>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden sm:flex items-center gap-2 ml-6">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate
                        class="rounded-xl px-4 py-2 font-medium text-sm transition-all duration-150">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- RIGHT SECTION -->
            <div class="hidden sm:flex items-center gap-4">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button type="button"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/60 focus:outline-hidden transition-all duration-150 shadow-2xs">

                            <div
                                class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold uppercase shadow-inner">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>

                            <div class="text-left hidden md:block leading-tight">
                                <div class="text-xs font-bold text-gray-800 dark:text-white">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-150" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <button wire:click="logout" type="button" class="w-full text-left">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Container -->
    <div x-cloak x-show="open" @click.outside="open = false"
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
        class="sm:hidden border-t border-gray-200 dark:border-gray-700/80 bg-white/98 dark:bg-gray-900/98 backdrop-blur-md">

        <div class="px-4 py-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- User Information & Actions -->
        <div class="border-t border-gray-200 dark:border-gray-700/80 px-4 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm uppercase shadow-inner">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>

                <div class="leading-tight">
                    <div class="text-sm font-bold text-gray-800 dark:text-white">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->email }}
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button wire:click="logout" type="button" class="w-full text-left">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
