<nav
    class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 fixed left-0 right-0 top-0 z-40 transition-all duration-300 h-12 xs:h-14 sm:h-16 flex items-center shadow-xs">
    <div
        class="flex items-center justify-between w-full max-w-[1750px] 2xl:max-w-[2400px] 4k:max-w-[3400px] mx-auto px-2.5 xs:px-3 sm:px-6 lg:px-8">

        {{-- Left Side: Brand Logo --}}
        <div class="flex items-center shrink-0">
            <a href="/" class="flex items-center group transition-transform duration-200 active:scale-95 py-1">
                <img src="{{ asset('icons/tms.png') }}"
                    class="h-5 xs:h-6 sm:h-8 w-auto object-contain transition-opacity group-hover:opacity-90"
                    alt="NS TMS Logo" />
            </a>
        </div>

        {{-- Right Side: Actions & Profile Dropdown --}}
        <div class="flex items-center space-x-2 sm:space-x-4">

            @auth
                <div class="relative flex items-center" x-data="{ openDropdown: false }">
                    {{-- User Avatar Button --}}
                    <button type="button" id="user-menu-button" aria-expanded="false"
                        data-dropdown-toggle="nav-user-dropdown" @click="openDropdown = !openDropdown"
                        class="flex items-center gap-1.5 sm:gap-2 p-1 rounded-full sm:rounded-xl transition-all duration-200 hover:bg-slate-50 hover:ring-2 hover:ring-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer min-h-[40px] sm:min-h-0 items-center">

                        <span class="sr-only">Open user menu</span>

                        {{-- Avatar Initials --}}
                        <div class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-white font-black text-[10px] xs:text-[11px] sm:text-xs tracking-wider shadow-sm uppercase font-mono shrink-0"
                            style="background-color: {{ $randomColor ?? '#078bdd' }};">
                            {{ strtoupper(substr(auth()->user()->first_name ?? '', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                        </div>

                        {{-- User Name --}}
                        <div class="hidden md:flex flex-col text-left pr-1 min-w-0">
                            <span class="text-xs sm:text-sm font-bold text-slate-800 tracking-tight leading-tight truncate">
                                {{ Str::ucfirst(auth()->user()->first_name ?? '') }}
                            </span>
                        </div>

                        {{-- Dropdown Chevron --}}
                        <svg class="hidden md:block w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0"
                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    {{-- User Dropdown Menu --}}
                    <div id="nav-user-dropdown"
                        class="hidden md:block absolute right-0 top-full mt-2 z-50 w-56 xs:w-60 text-base list-none bg-white rounded-2xl divide-y divide-slate-100 shadow-xl border border-slate-200/70 ring-1 ring-slate-100/50 overflow-hidden"
                        style="display: none;" x-show="openDropdown" @click.outside="openDropdown = false">

                        {{-- User Details Section --}}
                        <div class="py-3 px-4 bg-slate-50/70">
                            <span class="block text-xs xs:text-sm font-bold text-[#001b4d] tracking-tight truncate">
                                {{ Str::ucfirst(auth()->user()->first_name ?? '') }}
                                {{ Str::ucfirst(auth()->user()->last_name ?? '') }}
                            </span>
                            <span class="block text-[11px] xs:text-xs font-medium text-slate-400 truncate mt-0.5">
                                {{ auth()->user()->email ?? '' }}
                            </span>
                        </div>

                        {{-- Actions Section --}}
                        <ul class="py-1 text-slate-700" aria-labelledby="nav-user-dropdown">
                            <li>
                                <a href="#" wire:click.prevent="logout"
                                    class="flex justify-between items-center py-2.5 px-4 text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors group">

                                    <span wire:loading.remove class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-red-500 transition-colors shrink-0"
                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75">
                                            </path>
                                        </svg>
                                        Sign Out
                                    </span>

                                    <span wire:loading class="flex items-center gap-2 text-slate-400">
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
                <a href="{{ route('login') }}"
                    class="text-[11px] xs:text-xs font-bold text-slate-600 hover:text-blue-600 transition-colors px-2.5 xs:px-3 py-1.5 rounded-lg border border-slate-200 min-h-[36px] flex items-center justify-center">
                    Sign In
                </a>
            @endauth

        </div>
    </div>
</nav>
