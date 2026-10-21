<div>
    <!-- Modal Container -->
    <div x-data="{ show: @entangle('isOpen'), isDropdownOpen: false }" x-cloak x-show="show"
        class="fixed inset-0 flex items-center justify-center z-50 p-2.5 xs:p-4 sm:p-6 lg:p-8 overflow-y-auto transform-gpu">

        {{-- Backdrop Overlay --}}
        <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" inert></div>

        {{-- Modal Window --}}
        <div x-show="show" x-transition:enter="transition ease-out duration-150 transform-gpu"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100 transform-gpu"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200/80 dark:border-gray-700 w-full max-w-[340px] xs:max-w-[380px] sm:max-w-xl lg:max-w-2xl xl:max-w-2xl 2xl:max-w-3xl 3xl:max-w-4xl overflow-hidden z-10 my-auto max-h-[92vh] flex flex-col transition-all duration-150">

            {{-- Modal Header --}}
            <div
                class="flex justify-between items-center px-4 xs:px-5 sm:px-6 py-3.5 sm:py-4 border-b border-slate-100 dark:border-gray-700/80 bg-slate-50/50 dark:bg-gray-800/50 shrink-0">
                <h3 class="text-sm xs:text-base sm:text-lg font-bold text-slate-800 dark:text-white">
                    Manage Group Members
                </h3>
                <button @click="$wire.isOpen = false"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-gray-300 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white flex items-center justify-center transition-all duration-150 cursor-pointer shrink-0">
                    <svg inert class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Body Content --}}
            <div class="p-4 xs:p-5 sm:p-6 space-y-4 sm:space-y-6 overflow-y-auto custom-scrollbar flex-1">

                {{-- Add Users Dropdown Section --}}
                <div class="space-y-1.5 sm:space-y-2" x-data="{ isDropdownOpen: false }">
                    <label
                        class="block text-[10px] xs:text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">
                        Add Employees to Group
                    </label>

                    <div class="relative">
                        <!-- Dropdown Toggle Button -->
                        <button @click="isDropdownOpen = !isDropdownOpen" @click.away="isDropdownOpen = false"
                            type="button"
                            class="flex justify-between items-center w-full px-3 py-2.5 xs:px-4 xs:py-3 text-xs sm:text-sm lg:text-base text-slate-700 dark:text-white bg-slate-50/60 dark:bg-gray-900/50 rounded-xl border border-slate-200 dark:border-gray-700 hover:border-blue-500/50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all cursor-pointer shadow-2xs">
                            <span class="font-medium truncate">Select an employee to add...</span>
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-150 shrink-0 ml-1.5"
                                :class="{ 'rotate-180': isDropdownOpen }" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="isDropdownOpen" x-transition:enter="transition ease-out duration-150 transform-gpu"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100 transform-gpu"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                            class="absolute left-0 right-0 mt-1.5 z-20 bg-white dark:bg-gray-900 rounded-xl sm:rounded-2xl shadow-xl border border-slate-200 dark:border-gray-700 overflow-hidden">

                            <ul
                                class="max-h-48 xs:max-h-56 sm:max-h-60 p-2 overflow-y-auto space-y-1 text-xs sm:text-sm text-slate-700 dark:text-gray-300 custom-scrollbar">
                                @if ($availableUsers && is_iterable($availableUsers) && count($availableUsers) > 0)
                                    @foreach ($availableUsers as $user)
                                        <li wire:key="avail-user-{{ $user['id'] }}"
                                            class="flex justify-between items-center py-2 px-2.5 xs:py-2.5 xs:px-3 rounded-lg sm:rounded-xl hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors">
                                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                                <div
                                                    class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center text-white font-bold text-[10px] sm:text-xs rounded-lg sm:rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-xs shrink-0">
                                                    {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <span
                                                        class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white block truncate">
                                                        {{ $user['name'] }}
                                                    </span>
                                                    @if (($user['role'] ?? '') === 'super-admin')
                                                        <span
                                                            class="text-[9px] xs:text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-1.5 py-0.5 rounded-md">Super
                                                            Admin</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <button type="button" @click="isDropdownOpen = false"
                                                wire:click="addUser({{ $user['id'] }})"
                                                wire:target="addUser({{ $user['id'] }})" wire:loading.attr="disabled"
                                                class="px-2.5 py-1 xs:px-3 xs:py-1.5 text-[10px] xs:text-xs font-bold uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 rounded-lg sm:rounded-xl flex items-center justify-center shadow-xs transition-all cursor-pointer disabled:opacity-50 shrink-0 ml-2 active:scale-95">
                                                <span wire:loading.remove
                                                    wire:target="addUser({{ $user['id'] }})">Add</span>
                                                <span wire:loading wire:target="addUser({{ $user['id'] }})">
                                                    <svg class="animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24"
                                                        fill="none">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8v8H4z"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="py-5 text-center text-xs text-slate-400 font-medium">
                                        No available users found.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-gray-700">

                {{-- Group Users List Section --}}
                <div class="space-y-2.5 sm:space-y-3">
                    <div class="flex items-center justify-between">
                        <h4
                            class="text-[10px] xs:text-xs font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">
                            Users Currently in Group
                        </h4>
                        <span
                            class="px-2 py-0.5 bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-gray-300 text-[10px] font-bold rounded-full">
                            {{ count($groupUsers ?? []) }}
                        </span>
                    </div>

                    @if (isset($groupUsers) && count($groupUsers) > 0)
                        <div class="max-h-48 xs:max-h-56 sm:max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                            @foreach ($groupUsers as $user)
                                <div wire:key="group-user-{{ $user['id'] }}"
                                    class="flex justify-between items-center p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-50/70 dark:bg-gray-900/40 border border-slate-200/60 dark:border-gray-700/60">
                                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                        <div
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-slate-200 dark:bg-gray-700 text-slate-700 dark:text-gray-300 font-bold text-[10px] sm:text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) }}
                                        </div>
                                        <span
                                            class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate">
                                            {{ $user['first_name'] }} {{ $user['last_name'] }}
                                        </span>
                                    </div>

                                    <button type="button" wire:click="deleteUser({{ $user['id'] }})"
                                        wire:target="deleteUser({{ $user['id'] }})" wire:loading.attr="disabled"
                                        class="px-2.5 py-1 xs:px-3 xs:py-1.5 text-[10px] xs:text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 border border-rose-200/60 dark:border-rose-800/40 rounded-lg sm:rounded-xl flex items-center justify-center transition-all cursor-pointer disabled:opacity-50 shrink-0 ml-2 active:scale-95">
                                        <span wire:loading.remove
                                            wire:target="deleteUser({{ $user['id'] }})">Remove</span>
                                        <span wire:loading wire:target="deleteUser({{ $user['id'] }})">
                                            <svg class="animate-spin h-3.5 w-3.5 text-rose-600" viewBox="0 0 24 24"
                                                fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="text-center py-6 sm:py-8 bg-slate-50/50 dark:bg-gray-900/30 rounded-xl sm:rounded-2xl border border-dashed border-slate-200 dark:border-gray-700">
                            <p class="text-xs text-slate-400 dark:text-gray-500 font-medium">No users assigned to this
                                group yet.</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Modal Footer --}}
            <div
                class="flex justify-end px-4 xs:px-5 sm:px-6 py-3 sm:py-4 bg-slate-50/50 dark:bg-gray-800/50 border-t border-slate-100 dark:border-gray-700/80 shrink-0">
                <button @click="$wire.isOpen = false" type="button"
                    class="w-full sm:w-auto px-4 py-2 xs:px-5 xs:py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-600 transition-all cursor-pointer shadow-2xs active:scale-95">
                    Done
                </button>
            </div>

        </div>
    </div>
</div>
