<nav x-data="{ open: false }"
    class="sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-200 dark:border-gray-700 shadow-sm">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between h-16">

            <!-- LEFT -->
            <div class="flex items-center gap-3">

                <!-- Mobile Toggle -->
                <button
                    @click="open = !open"
                    class="sm:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">

                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            x-show="!open"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path
                            x-show="open"
                            x-cloak
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route('dashboard') }}"
                    wire:navigate
                    class="flex items-center gap-2">

                    <x-application-logo
                        class="h-9 w-auto text-indigo-600 dark:text-indigo-400" />

                    <span
                        class="hidden sm:block text-lg font-semibold text-gray-800 dark:text-white">
                        Task Manager
                    </span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden sm:flex items-center gap-2 ml-6">

                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                        wire:navigate
                        class="rounded-lg px-4 py-2">

                        {{ __('Dashboard') }}

                    </x-nav-link>

                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex items-center gap-4">

                <x-dropdown align="right" width="56">

                    <x-slot name="trigger">

                        <button
                            class="flex items-center gap-3 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                            <div
                                class="h-9 w-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold uppercase">

                                {{ substr(auth()->user()->name, 0, 1) }}

                            </div>

                            <div class="text-left hidden md:block">

                                <div
                                    class="text-sm font-semibold text-gray-800 dark:text-white">

                                    {{ auth()->user()->name }}

                                </div>

                                <div
                                    class="text-xs text-gray-500 dark:text-gray-400">

                                    {{ auth()->user()->email }}

                                </div>
                            </div>

                            <svg class="w-4 h-4 text-gray-500"
                                fill="currentColor"
                                viewBox="0 0 20 20">

                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />

                            </svg>

                        </button>

                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link
                            :href="route('profile')"
                            wire:navigate>

                            {{ __('Profile') }}

                        </x-dropdown-link>

                        <button wire:click="logout" class="w-full text-left">

                            <x-dropdown-link>

                                {{ __('Log Out') }}

                            </x-dropdown-link>

                        </button>

                    </x-slot>

                </x-dropdown>

            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div
        x-show="open"
        x-transition
        x-cloak
        class="sm:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">

        <div class="px-4 py-4 space-y-2">

            <x-responsive-nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
                wire:navigate>

                {{ __('Dashboard') }}

            </x-responsive-nav-link>

        </div>

        <!-- User Info -->
        <div
            class="border-t border-gray-200 dark:border-gray-700 px-4 py-4">

            <div class="flex items-center gap-3 mb-4">

                <div
                    class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold uppercase">

                    {{ substr(auth()->user()->name, 0, 1) }}

                </div>

                <div>

                    <div
                        class="text-sm font-semibold text-gray-800 dark:text-white">

                        {{ auth()->user()->name }}

                    </div>

                    <div
                        class="text-xs text-gray-500 dark:text-gray-400">

                        {{ auth()->user()->email }}

                    </div>

                </div>

            </div>

            <div class="space-y-2">

                <x-responsive-nav-link
                    :href="route('profile')"
                    wire:navigate>

                    {{ __('Profile') }}

                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-left">

                    <x-responsive-nav-link>

                        {{ __('Log Out') }}

                    </x-responsive-nav-link>

                </button>

            </div>
        </div>
    </div>
</nav>
