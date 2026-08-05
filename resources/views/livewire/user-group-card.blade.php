<div class="antialiased dark:bg-gray-900 min-h-screen">
    <!-- Perfectly aligned padding matching standard admin layouts, with a subtle and elegant top section highlight -->
    <main class="p-4 sm:p-6 md:p-8 md:ml-16 h-auto pt-20 sm:pt-24 md:pt-20 pb-16 max-w-7xl mx-auto">

        {{-- Executive Header / Background strictly solid white as requested --}}
        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div
                class="absolute -top-24 -left-24 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-br from-blue-600/20 via-indigo-600/15 to-sky-400/10 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute -bottom-24 -right-24 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-tl from-cyan-500/15 to-blue-600/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-2">
                    <div
                        class="flex items-center gap-2 text-[10px] sm:text-[11px] font-medium uppercase tracking-widest text-slate-600 overflow-x-auto py-1">
                        <button type="button" onclick="window.location.href='{{ route('dashboard') }}'"
                            class="hover:text-blue-600 transition cursor-pointer flex items-center gap-1.5 shrink-0 bg-transparent border-none p-0 text-slate-600 font-semibold">
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-blue-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight text-slate-900 drop-shadow-xs">
                        Manage Users Group
                    </h1>

                    <p class="text-xs sm:text-sm font-medium text-slate-600">
                        View, edit and manage system user accounts seamlessly across all devices with enterprise
                        controls.
                    </p>
                </div>
            </div>
        </div>

        <!-- Add New Group Section Container -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 p-6 sm:p-8 mb-6">
            <div class="mb-4">
                <h2 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-1">
                    ADD NEW GROUP
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    Create groups for organizing users.
                </p>
            </div>

            <hr class="border-slate-100 dark:border-gray-700 mb-6">

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 max-w-4xl">
                <!-- Input field -->
                <div class="relative flex-1">
                    <input wire:model="newGroup" type="text" id="floating_outlined"
                        class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer shadow-sm"
                        placeholder="Enter group name..." />
                    @error('newGroup')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Add Group Button -->
                <button wire:click="addGroup" wire:loading.attr="disabled" type="button"
                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-6 py-3.5 shadow-md transition-all duration-200 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center justify-center shrink-0">
                    <span wire:loading.remove wire:target="addGroup">+ ADD GROUP</span>
                    <span wire:loading wire:target="addGroup">Adding...</span>
                </button>
            </div>
        </div>

        <!-- Groups List Container -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-blue-600 inline-block"></span>
                        <h2 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                            GROUPS LIST
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        Manage all groups from here.
                    </p>
                </div>

                <!-- Total Groups Counter Badge -->
                <div
                    class="inline-flex items-center px-3.5 py-1.5 bg-blue-50/80 dark:bg-blue-900/30 border border-blue-200/60 dark:border-blue-700/50 rounded-full text-xs font-semibold text-blue-700 dark:text-blue-300 self-start sm:self-auto">
                    Total Groups: <span class="ml-1.5 font-bold">{{ count($groups) }}</span>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-gray-700 mb-6">

            <!-- Group Cards Grid with Colorful Avatar Badges -->
            <div x-data="{ userId: null }" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($groups as $index => $group)
                    @php
                        $words = explode(' ', $group['label'] ?? '');
                        $initials = '';
                        foreach ($words as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                        }
                        $initials = substr($initials, 0, 2);
                        if (empty($initials)) {
                            $initials = 'GP';
                        }

                        $colors = [
                            [
                                'bg' => 'bg-emerald-600 shadow-emerald-600/30',
                                'badge' =>
                                    'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
                            ],
                            [
                                'bg' => 'bg-blue-600 shadow-blue-600/30',
                                'badge' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                            ],
                            [
                                'bg' => 'bg-amber-600 shadow-amber-600/30',
                                'badge' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
                            ],
                            [
                                'bg' => 'bg-violet-600 shadow-violet-600/30',
                                'badge' => 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400',
                            ],
                            [
                                'bg' => 'bg-rose-600 shadow-rose-600/30',
                                'badge' => 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
                            ],
                        ];
                        $selectedColor = $colors[$index % count($colors)];
                    @endphp

                    <div wire:key="{{ $group['id'] }}"
                        class="relative w-full bg-white dark:bg-gray-900 border border-slate-200/90 dark:border-gray-700 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between p-5 group/card">

                        <!-- Top Colorful Avatar Block -->
                        <div class="flex flex-col items-center pt-2 pb-4">
                            <div
                                class="w-16 h-16 rounded-2xl {{ $selectedColor['bg'] }} text-white font-bold text-lg flex items-center justify-center shadow-lg mb-3 tracking-wider group-hover/card:scale-105 transition-transform duration-300">
                                {{ $initials }}
                            </div>

                            <!-- Small Category Tag Pill -->
                            <span
                                class="px-3 py-0.5 {{ $selectedColor['badge'] }} text-[10px] font-bold tracking-wider uppercase rounded-full mb-2">
                                GROUP
                            </span>

                            @if ($editingGroupId === $group['id'])
                                <input wire:model.defer="editingGroupName" type="text" placeholder="Group Name"
                                    class="block w-full px-3 py-2 text-sm text-center text-gray-900 bg-transparent rounded-xl border border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 mb-3" />
                                <div class="flex gap-2 w-full">
                                    <button wire:click="saveGroupName"
                                        class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium flex items-center justify-center gap-1 flex-1 transition-colors">
                                        <svg wire:loading wire:target="saveGroupName"
                                            class="w-3.5 h-3.5 animate-spin text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="saveGroupName">Save</span>
                                    </button>
                                    <button wire:click="cancelEditing"
                                        class="bg-red-600 hover:bg-red-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium flex-1 transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            @else
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white text-center truncate w-full px-2"
                                    title="{{ $group['label'] }}">
                                    {{ $group['label'] }}
                                </h3>
                            @endif
                        </div>

                        <!-- Action Buttons Bottom Row (Edit & View) -->
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-gray-800 grid grid-cols-2 gap-2">
                            <!-- Edit Button -->
                            <button wire:click="startEditing({{ $group['id'] }}, '{{ $group['label'] }}')"
                                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50/70 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-900/50 rounded-xl transition-colors border border-blue-200/50 dark:border-blue-800/40">
                                <svg class="w-3.5 h-3.5 mr-1.5" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.1213 2.70705C19.9497 1.53548 18.0503 1.53547 16.8787 2.70705L15.1989 4.38685L7.29289 12.2928C7.16473 12.421 7.07382 12.5816 7.02986 12.7574L6.02986 16.7574C5.94466 17.0982 6.04451 17.4587 6.29289 17.707C6.54127 17.9554 6.90176 18.0553 7.24254 17.9701L11.2425 16.9701C11.4184 16.9261 11.5789 16.8352 11.7071 16.707L19.5556 8.85857L21.2929 7.12126C22.4645 5.94969 22.4645 4.05019 21.2929 2.87862L21.1213 2.70705Z"
                                        fill="currentColor"></path>
                                </svg>
                                Edit
                            </button>

                            <!-- View Button -->
                            <button wire:click="$dispatch('openUserGroupModal',{ labelId: {{ $group['id'] }} })"
                                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50/70 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 rounded-xl transition-colors border border-rose-200/50 dark:border-rose-800/40">
                                <svg class="w-3.5 h-3.5 mr-1.5" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="3.5" stroke="currentColor"
                                        stroke-width="1.5" />
                                    <path
                                        d="M20.188 10.9343C20.5762 11.4056 20.7703 11.6412 20.7703 12C20.7703 12.3588 20.5762 12.5944 20.188 13.0657C18.7679 14.7899 15.6357 18 12 18C8.36427 18 5.23206 14.7899 3.81197 13.0657C3.42381 12.5944 3.22973 12.3588 3.22973 12C3.22973 11.6412 3.42381 11.4056 3.81197 10.9343C5.23206 9.21014 8.36427 6 12 6C15.6357 6 18.7679 9.21014 20.188 10.9343Z"
                                        stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                View
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
