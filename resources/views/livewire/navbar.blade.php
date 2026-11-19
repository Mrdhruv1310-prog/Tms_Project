<nav x-data="{ userMenuOpen: false }"
    class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 fixed left-0 right-0 top-0 z-40 transition-all duration-300 h-14 sm:h-16 flex items-center shadow-sm">
    <div class="flex items-center justify-between w-full max-w-[1600px] mx-auto px-2.5 xs:px-3 sm:px-6 lg:px-8 gap-2">

        {{-- Left Side: Brand Logo --}}
        <div class="flex items-center shrink-0 min-w-0">
            <a href="/" wire:navigate
                class="flex items-center group transition-transform duration-200 active:scale-95">
                <img src="{{ asset('icons/tms.png') }}"
                    class="h-6 xs:h-7 sm:h-8 w-auto object-contain transition-opacity group-hover:opacity-90"
                    alt="NS TMS Logo" />
            </a>
        </div>

        {{-- Right Side: Actions & Profile Dropdown --}}
        <div class="flex items-center space-x-1.5 xs:space-x-2 sm:space-x-4 shrink-0">

            @auth
                <div class="relative flex items-center" @click.outside="userMenuOpen = false"
                    @keydown.escape.window="userMenuOpen = false">

                    {{-- User Avatar Button --}}
                    <button type="button" id="user-menu-button" @click="userMenuOpen = !userMenuOpen"
                        :aria-expanded="userMenuOpen"
                        class="flex items-center gap-1.5 sm:gap-2.5 p-1 rounded-full sm:rounded-xl transition-all duration-200 hover:bg-slate-50 hover:ring-2 hover:ring-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer">

                        <span class="sr-only">Open user menu</span>

                        {{-- Avatar Initials --}}
                        <div class="w-7 h-7 xs:w-8 xs:h-8 rounded-lg flex items-center justify-center text-white font-black text-[11px] xs:text-xs tracking-wider shadow-sm uppercase font-mono shrink-0"
                            style="background-color: {{ $randomColor ?? '#078bdd' }};">
                            {{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                        </div>

                        {{-- User Name (Hidden on mobile < 768px, visible on tablet & desktop) --}}
                        <div class="hidden md:flex flex-col text-left pr-0.5 max-w-[140px] lg:max-w-[200px]">
                            <span class="text-xs lg:text-sm font-bold text-slate-800 tracking-tight leading-tight truncate">
                                {{ Str::ucfirst(auth()->user()->first_name ?? '') }}
                            </span>
                        </div>

                        {{-- Dropdown Chevron --}}
                        <svg class="hidden md:block w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0"
                            :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    {{-- User Dropdown Menu --}}
                    <div id="nav-user-dropdown" x-show="userMenuOpen" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                        class="absolute right-0 top-full mt-2 z-50 w-56 xs:w-64 sm:w-72 max-w-[calc(100vw-1.25rem)] origin-top-right text-base list-none bg-white rounded-2xl divide-y divide-slate-100 shadow-xl border border-slate-200/70 ring-1 ring-slate-100/50 overflow-hidden">

                        {{-- User Details Section --}}
                        <div class="py-2.5 px-3.5 sm:py-3 sm:px-4 bg-slate-50/70">
                            <span class="block text-xs sm:text-sm font-bold text-[#001b4d] tracking-tight truncate">
                                {{ Str::ucfirst(auth()->user()->first_name ?? '') }}
                                {{ Str::ucfirst(auth()->user()->last_name ?? '') }}
                            </span>
                            <span class="block text-[11px] sm:text-xs font-medium text-slate-400 truncate mt-0.5">
                                {{ auth()->user()->email ?? '' }}
                            </span>
                        </div>

                        {{-- Actions Section --}}
                        <ul class="py-1 text-slate-700" aria-labelledby="user-menu-button">
                            <li>
                                <a href="#" wire:click.prevent="logout"
                                    class="flex justify-between items-center py-2.5 px-3.5 sm:px-4 text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors group">

                                    <span wire:loading.remove wire:target="logout" class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-red-500 transition-colors shrink-0"
                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75">
                                            </path>
                                        </svg>
                                        Sign Out
                                    </span>

                                    <span wire:loading wire:target="logout" class="flex items-center gap-2 text-slate-400">
                                        <svg class="animate-spin h-4 w-4 text-blue-600 shrink-0" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Signing Out...
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" wire:navigate
                    class="text-[11px] xs:text-xs sm:text-sm font-bold text-slate-600 hover:text-blue-600 hover:border-blue-200 transition-colors px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl border border-slate-200 whitespace-nowrap">
                    Sign In
                </a>
            @endauth

        </div>
    </div>
</nav>
