<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden antialiased transform-gpu transition-all duration-150">
    <main
        class="scrollcontainer md:ml-16 px-2.5 xs:px-4 sm:px-6 lg:px-8 2xl:px-12 3xl:px-16 pt-14 sm:pt-20 pb-12 sm:pb-16 max-w-[2560px] mx-auto h-auto">

        {{-- Back Navigation --}}
        <div class="mb-3.5 sm:mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 xs:px-3 sm:px-4 sm:py-2 rounded-xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-md border border-gray-200/80 dark:border-gray-700 text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-[rgb(7,139,221)] dark:hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-xs transition-all duration-150 active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        {{-- Executive Header --}}
        <div
            class="mb-4 sm:mb-8 overflow-hidden rounded-2xl sm:rounded-3xl border border-blue-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-md sm:shadow-lg shadow-blue-900/5 dark:shadow-none p-4 sm:p-6 lg:p-8 relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
                <div class="flex items-start gap-3">
                    <div class="flex flex-col gap-0.5">
                        <h3
                            class="text-base xs:text-lg sm:text-xl lg:text-2xl 2xl:text-3xl font-bold text-slate-700 dark:text-white tracking-tight">
                            Manage Employee Group
                        </h3>
                        <p
                            class="text-[11px] xs:text-xs sm:text-sm lg:text-base font-medium text-slate-500 dark:text-gray-400">
                            View, edit and manage system user groups seamlessly.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add New Group Section Container --}}
        <div
            class="bg-white/95 dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 dark:border-gray-700 p-4 sm:p-6 lg:p-8 mb-4 sm:mb-8 transition-all duration-150">
            <div class="mb-3 sm:mb-4">
                <h2
                    class="text-xs xs:text-sm lg:text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-0.5">
                    Add New Group
                </h2>
                <p class="text-[11px] xs:text-xs sm:text-sm lg:text-base text-gray-500 dark:text-gray-400">
                    Create a new group category for organizing system users.
                </p>
            </div>

            <hr class="border-slate-100 dark:border-gray-700 mb-4 sm:mb-6">

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 max-w-4xl">
                <div class="relative flex-1">
                    <input wire:model="newGroup" type="text"
                        class="block w-full px-3 py-2.5 xs:px-3.5 sm:px-4 sm:py-3 text-xs sm:text-sm lg:text-base text-gray-900 bg-gray-50/50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all shadow-2xs"
                        placeholder="Enter group name..." />
                    @error('newGroup')
                        <span
                            class="text-rose-500 text-[10px] xs:text-[11px] sm:text-xs mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <button wire:click="addGroup" wire:loading.attr="disabled" type="button"
                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-xs sm:text-sm lg:text-base uppercase tracking-wider px-4 py-2.5 sm:px-6 sm:py-3.5 shadow-md shadow-blue-500/25 transition-all duration-150 flex items-center justify-center shrink-0 cursor-pointer disabled:opacity-50 active:scale-[0.98]">
                    <span wire:loading.remove wire:target="addGroup">+ Add Group</span>
                    <span wire:loading wire:target="addGroup" class="flex items-center gap-2">
                        Adding...
                        <svg class="animate-spin h-3.5 w-3.5 sm:h-4 sm:w-4 text-white" viewBox="0 0 24 24"
                            fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        {{-- Groups List Container --}}
        <div
            class="bg-white/95 dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200/80 dark:border-gray-700 p-4 sm:p-6 lg:p-8 transition-all duration-150">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span
                            class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-blue-600 inline-block ring-4 ring-blue-100 dark:ring-blue-900/30"></span>
                        <h2
                            class="text-xs xs:text-sm lg:text-base font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                            Groups Directory
                        </h2>
                    </div>
                    <p class="text-[11px] xs:text-xs sm:text-sm lg:text-base text-gray-500 dark:text-gray-400">
                        Overview and management of all active user groups.
                    </p>
                </div>

                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-blue-50/80 dark:bg-blue-900/30 border border-blue-200/60 dark:border-blue-700/50 rounded-xl text-xs sm:text-sm font-bold text-blue-700 dark:text-blue-300 self-start sm:self-auto shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Total Groups: <span class="text-xs sm:text-sm font-extrabold">{{ count($groups) }}</span>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-gray-700 mb-4 sm:mb-6">

            {{-- Group Cards Grid --}}
            @php
                $colorPalette = [
                    [
                        'bg' => 'bg-emerald-500 shadow-emerald-500/20',
                        'badge' =>
                            'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border-emerald-200/60',
                    ],
                    [
                        'bg' => 'bg-blue-500 shadow-blue-500/20',
                        'badge' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border-blue-200/60',
                    ],
                    [
                        'bg' => 'bg-amber-500 shadow-amber-500/20',
                        'badge' =>
                            'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border-amber-200/60',
                    ],
                    [
                        'bg' => 'bg-violet-500 shadow-violet-500/20',
                        'badge' =>
                            'bg-violet-50 dark:bg-violet-950/50 text-violet-600 dark:text-violet-400 border-violet-200/60',
                    ],
                    [
                        'bg' => 'bg-rose-500 shadow-rose-500/20',
                        'badge' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border-rose-200/60',
                    ],
                ];
            @endphp

            <div
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3.5 xs:gap-4 sm:gap-6">
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
                        $selectedColor = $colorPalette[$index % count($colorPalette)];
                    @endphp

                    <div wire:key="{{ $group['id'] }}"
                        class="group relative bg-gradient-to-b from-white via-white to-slate-50/50 dark:from-gray-900 dark:to-gray-900/80 border border-slate-200/80 dark:border-gray-700/80 rounded-2xl shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between p-3.5 xs:p-4 sm:p-5">

                        <div class="flex flex-col items-center pt-1 pb-2.5 sm:pb-3">
                            {{-- Initials Avatar Box --}}
                            <div
                                class="w-12 h-12 xs:w-14 xs:h-14 sm:w-16 sm:h-16 rounded-2xl {{ $selectedColor['bg'] }} text-white font-extrabold text-xs xs:text-sm sm:text-base flex items-center justify-center shadow-md mb-2 sm:mb-2.5 tracking-wider group-hover:scale-105 transition-transform duration-200 ring-4 ring-white dark:ring-gray-900 shrink-0">
                                {{ $initials }}
                            </div>

                            {{-- Category Badge --}}
                            <span
                                class="px-2 py-0.5 xs:px-2.5 {{ $selectedColor['badge'] }} border text-[9px] sm:text-[10px] font-bold tracking-wider uppercase rounded-full mb-2 sm:mb-2.5 shadow-2xs">
                                Group
                            </span>

                            {{-- Inline Editing Box or Title --}}
                            @if ($editingGroupId === $group['id'])
                                <div class="w-full space-y-2">
                                    <input wire:model="editingGroupName" type="text"
                                        placeholder="@error('editingGroupName'){{ $message }}@else Group Name @enderror"
                                        class="w-full rounded-xl bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-slate-800 dark:text-white outline-none shadow-2xs
                                        @error('editingGroupName')
                                            border border-rose-500 placeholder:text-rose-500 focus:border-rose-500 focus:ring-2 focus:ring-rose-100
                                        @else
                                            border border-slate-300 dark:border-gray-700 placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-100
                                        @enderror">
                                    <div class="flex gap-1.5 w-full">
                                        <button wire:click="saveGroupName"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl px-2 py-1.5 text-[10px] xs:text-[11px] font-bold uppercase tracking-wider flex items-center justify-center gap-1 flex-1 transition-colors cursor-pointer shadow-2xs">
                                            <span wire:loading.remove wire:target="saveGroupName">Save</span>
                                            <span wire:loading wire:target="saveGroupName">...</span>
                                        </button>
                                        <button wire:click="cancelEditing"
                                            class="bg-slate-200 hover:bg-slate-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 rounded-xl px-2 py-1.5 text-[10px] xs:text-[11px] font-bold uppercase tracking-wider flex-1 transition-colors cursor-pointer">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @else
                                <h3 class="text-xs xs:text-sm sm:text-base font-bold text-slate-900 dark:text-white text-center truncate w-full px-1"
                                    title="{{ $group['label'] }}">
                                    {{ $group['label'] }}
                                </h3>
                            @endif
                        </div>

                        {{-- Action Buttons Footer --}}
                        <div
                            class="mt-2.5 sm:mt-3 pt-2.5 border-t border-slate-100 dark:border-gray-800 grid grid-cols-2 gap-2">
                            <button wire:click="startEditing({{ $group['id'] }}, '{{ $group['label'] }}')"
                                class="inline-flex items-center justify-center px-2 py-1.5 xs:px-2.5 xs:py-2 text-[10px] xs:text-[11px] sm:text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50/80 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-900/50 rounded-xl transition-all border border-blue-200/60 dark:border-blue-800/40 cursor-pointer active:scale-95">
                                <svg class="w-3 h-3 xs:w-3.5 xs:h-3.5 mr-1 shrink-0" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z" />
                                </svg>
                                Edit
                            </button>

                            <button wire:click="$dispatch('openUserGroupModal',{ labelId: {{ $group['id'] }} })"
                                class="inline-flex items-center justify-center px-2 py-1.5 xs:px-2.5 xs:py-2 text-[10px] xs:text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-xl transition-all border border-slate-200 dark:border-gray-700 cursor-pointer active:scale-95">
                                <svg class="w-3 h-3 xs:w-3.5 xs:h-3.5 mr-1 shrink-0" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
