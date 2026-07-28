<div class="min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50/50 to-blue-100/60 antialiased selection:bg-blue-600 selection:text-white text-slate-800">
    <main class="scrollcontainer px-3 sm:px-6 md:px-8 pb-20 pt-20 sm:pt-24 md:ml-16 max-w-[1750px] mx-auto transition-all duration-300">

        {{-- Executive Header / Background strictly solid white as requested --}}
        <div class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="absolute -top-24 -left-24 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-br from-blue-600/20 via-indigo-600/15 to-sky-400/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-tl from-cyan-500/15 to-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-medium uppercase tracking-widest text-slate-600 overflow-x-auto py-1">
                        <button type="button" onclick="window.history.back()" class="hover:text-blue-600 transition cursor-pointer flex items-center gap-1.5 shrink-0 bg-transparent border-none p-0 text-slate-600 font-semibold">
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <svg class="h-3 w-3 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        <span class="text-blue-700 font-bold bg-blue-100/80 px-3 py-1 rounded-lg border border-blue-300 shadow-xs shrink-0">User Directory</span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight text-slate-900 drop-shadow-xs">
                        Manage Users
                    </h1>

                    <p class="text-xs sm:text-sm font-medium text-slate-600">
                        View, edit and manage system user accounts seamlessly across all devices with enterprise controls.
                    </p>
                </div>

                {{-- Live System Status Badge --}}
                <div class="hidden sm:flex items-center gap-3.5 bg-gradient-to-r from-blue-50/80 to-indigo-50/80 backdrop-blur-xl px-4 sm:px-5 py-3 rounded-2xl border border-blue-200 shadow-md self-start lg:self-auto">
                    <div class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/40 shrink-0 ring-2 ring-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-blue-600">Users Module</p>
                        <p class="text-[11px] sm:text-xs font-semibold text-emerald-600 flex items-center gap-1.5 mt-0.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse shadow-sm shadow-emerald-500"></span> Live Control Panel
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users Cards Section --}}
        <div x-data="{ userDeleteModalOpen: false, userId: null }" @userdeleted.window="userDeleteModalOpen = false" class="overflow-hidden rounded-2xl sm:rounded-3xl border border-blue-200/80 bg-white/95 backdrop-blur-xl shadow-2xl shadow-blue-500/10 ring-2 ring-white">
            <div class="border-b border-slate-200 px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50 via-blue-50/60 to-indigo-50/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2.5">
                        <span class="h-3 w-3 rounded-full bg-blue-600 animate-pulse ring-4 ring-blue-200 shadow-md shadow-blue-500"></span>
                        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900">
                            Registered Users Directory
                        </h2>
                    </div>
                    <p class="text-xs font-medium text-slate-600">
                        Manage and control access permissions for all active platform profiles.
                    </p>
                </div>
                <div class="text-xs font-semibold text-slate-800 bg-white px-4 py-2 rounded-xl border border-blue-200 shadow-sm self-start sm:self-auto flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                    Total Profiles: <span class="font-extrabold text-blue-700 text-sm">{{ count($users) }}</span>
                </div>
            </div>

            <div class="p-4 sm:p-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6">
                    @foreach ($users as $user)
                        @php
                            $initials =
                                strtoupper(substr($user->first_name, 0, 1)) .
                                strtoupper(substr($user->last_name, 0, 1));
                        @endphp

                        {{-- Card color strictly preserved (bg-white with border-slate-200) + high-impact luminous corporate borders/shadows --}}
                        <div wire:key="{{$user->id}}"
                            class="w-full bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-md transition-all duration-300 hover:border-blue-500 hover:shadow-2xl hover:shadow-blue-600/20 hover:-translate-y-1.5 flex flex-col items-center text-center relative group">

                            <!-- Top active highlight accent bar -->
                            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 rounded-t-2xl opacity-75 group-hover:opacity-100 transition-opacity duration-300 shadow-sm"></div>

                            <!-- Profile Image or Initials -->
                            <div x-data="{ backgroundColor: generateRandomColor() }" :style="{ backgroundColor: backgroundColor }"
                                class="w-20 h-20 sm:w-24 sm:h-24 mb-4 rounded-2xl shadow-xl flex items-center justify-center text-white text-xl sm:text-2xl font-extrabold border-2 border-white shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-1 ring-4 ring-blue-50">
                                {{ $initials }}
                            </div>

                            <span class="mb-2.5 inline-flex rounded-full bg-blue-50 px-3.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-200 shadow-xs">
                                {{ Str::ucfirst($user->role) }}
                            </span>

                            <h3 class="truncate w-full text-sm sm:text-base font-bold text-slate-900 mb-4 px-2 tracking-tight">
                                {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}
                            </h3>

                            <div class="flex items-center justify-center gap-2.5 w-full mt-auto pt-3 border-t border-slate-100">
                                <button wire:click="$dispatch('edituser', { id: {{ $user->id }} })"
                                    class="group/btn relative flex-1 flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200 text-blue-700 text-xs font-bold uppercase tracking-wider shadow-xs transition-all duration-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:shadow-lg hover:scale-105 active:scale-95 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="relative z-10 w-4 h-4 transition-transform duration-300 group-hover/btn:rotate-12" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z" />
                                    </svg>
                                    <span class="relative z-10">Edit</span>
                                </button>

                                @if (auth()->user()->id !== $user->id)
                                    <button @click="userDeleteModalOpen=true;userId={{ $user->id }}"
                                        class="group/btn relative flex-1 flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200 text-rose-600 text-xs font-bold uppercase tracking-wider shadow-xs transition-all duration-300 hover:bg-rose-600 hover:text-white hover:border-rose-600 hover:shadow-lg hover:scale-105 active:scale-95 cursor-pointer">
                                        <svg class="relative z-10 w-4 h-4 transition-transform duration-300 group-hover/btn:rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="relative z-10">Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (session()->has('message'))
                    <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                @endif

                <!-- Modal/Dialog -->
                <div x-show="userDeleteModalOpen" x-cloak x-transition class="relative z-50"
                    aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" inert></div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl border border-slate-200 transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 sm:p-8">
                                <div class="sm:flex sm:items-start gap-4">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 sm:mx-0 sm:h-12 sm:w-12 shadow-inner ring-4 ring-rose-50">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor" inert>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-2 sm:mt-0 sm:text-left">
                                        <h3 class="text-base sm:text-lg font-bold text-slate-900" id="modal-title">Delete User Account</h3>
                                        <div class="mt-1">
                                            <p class="text-xs sm:text-sm font-medium text-slate-600">Are you sure you want to delete this user profile? This action cannot be undone.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button @click="userDeleteModalOpen = false" type="button"
                                        class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-slate-100 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 border border-slate-200 transition hover:bg-slate-200 cursor-pointer">
                                        Cancel
                                    </button>

                                    <form wire:submit.prevent="delete(userId)">
                                        <button wire:loading.attr="disabled" type="submit"
                                            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-rose-500/30 transition-all hover:bg-rose-700 active:scale-95 disabled:opacity-60 cursor-pointer">
                                            <span wire:loading.remove>Delete User</span>
                                            <span wire:loading class="flex items-center gap-2">
                                                Deleting...
                                                <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function generateRandomColor() {
        const min = 40;
        const max = 200;

        const r = Math.floor(Math.random() * (max - min + 1)) + min;
        const g = Math.floor(Math.random() * (max - min + 1)) + min;
        const b = Math.floor(Math.random() * (max - min + 1)) + min;

        return `rgb(${r}, ${g}, ${b})`;
    }
</script>
