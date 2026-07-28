<nav class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 fixed left-0 right-0 top-0 z-40 transition-all duration-300 h-14 sm:h-16 flex items-center shadow-sm">
    <div class="flex items-center justify-between w-full max-w-[1600px] mx-auto px-4 sm:px-6">

        {{-- Left Side: Brand Logo --}}
        <div class="flex items-center shrink-0">
            <a href="/" class="flex items-center group transition-transform duration-200 active:scale-95">
                <img src="{{ asset('icons/tms.png') }}" class="h-7 sm:h-8 w-auto object-contain transition-opacity group-hover:opacity-90" alt="NS TMS Logo" />
            </a>
        </div>

        {{-- Right Side: Actions & Profile Dropdown --}}
        <div class="flex items-center space-x-2 sm:space-x-4">

            <div class="relative flex items-center">
                {{-- User Avatar Button --}}
                <button type="button" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="nav-user-dropdown"
                    class="flex items-center gap-2.5 p-1 rounded-full sm:rounded-xl transition-all duration-200 hover:bg-slate-50 hover:ring-2 hover:ring-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20">

                    <span class="sr-only">Open user menu</span>

                    {{-- Avatar Initials --}}
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-xs tracking-wider shadow-sm uppercase font-mono"
                        style="background-color: {{ $randomColor }};">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                    </div>

                    {{-- User Name (Responsive: Hidden on small mobile, visible on tablet/laptop/TV) --}}
                    <div class="hidden md:flex flex-col text-left pr-1">
                        <span class="text-xs font-bold text-slate-800 tracking-tight leading-tight">
                            {{ Str::ucfirst(auth()->user()->first_name) }}
                        </span>
                    </div>

                    {{-- Dropdown Chevron --}}
                    <svg class="hidden md:block w-3.5 h-3.5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- User Dropdown Menu --}}
                <div id="nav-user-dropdown"
                    class="hidden z-50 my-2 w-60 text-base list-none bg-white rounded-2xl divide-y divide-slate-100 shadow-xl border border-slate-200/70 ring-1 ring-slate-100/50 overflow-hidden">

                    {{-- User Details Section --}}
                    <div class="py-3 px-4 bg-slate-50/70">
                        <span class="block text-sm font-bold text-[#001b4d] tracking-tight truncate">
                            {{ Str::ucfirst(auth()->user()->first_name) }} {{ Str::ucfirst(auth()->user()->last_name) }}
                        </span>
                        <span class="block text-xs font-medium text-slate-400 truncate mt-0.5">
                            {{ auth()->user()->email }}
                        </span>
                    </div>

                    {{-- Actions Section --}}
                    <ul class="py-1 text-slate-700" aria-labelledby="nav-user-dropdown">
                        <li>
                            <a href="#" wire:click.prevent="logout"
                               class="flex justify-between items-center py-2.5 px-4 text-xs font-bold text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors group">

                                <span wire:loading.remove class="flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"></path>
                                    </svg>
                                    Sign Out
                                </span>

                                <span wire:loading class="flex items-center gap-2 text-slate-400">
                                    <svg class="animate-spin h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Signing Out...
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</nav>
