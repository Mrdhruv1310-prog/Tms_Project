<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">
    <main class="scrollcontainer md:ml-16 px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16">

        <div class="mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/80 backdrop-blur-md border border-gray-200/80 text-sm font-semibold text-gray-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-sm transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        {{-- Executive Header --}}
        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-3xl border border-slate-200 bg-white/90 backdrop-blur-2xl shadow-xl shadow-slate-200/50 p-6 sm:p-8 relative">
            <div
                class="absolute -top-24 -left-24 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-br from-blue-500/15 via-indigo-500/10 to-sky-400/5 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute -bottom-24 -right-24 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-tl from-cyan-400/10 to-blue-600/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-2">
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Manage Users
                    </h1>

                    <p class="text-xs sm:text-sm font-medium text-slate-500">
                        View, edit and manage system user accounts seamlessly across all devices with enterprise
                        controls.
                    </p>
                </div>
            </div>
        </div>

        {{-- Users Cards Section --}}
        <div x-data="{ userDeleteModalOpen: false, userId: null }" @userdeleted.window="userDeleteModalOpen = false"
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white/90 backdrop-blur-2xl shadow-2xl shadow-slate-200/50">

            <div
                class="border-b border-slate-100 px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50/80 via-blue-50/30 to-transparent flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-pulse ring-4 ring-blue-100"></span>
                        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900">
                            Registered Users Directory
                        </h2>
                    </div>
                    <p class="text-xs font-medium text-slate-500">
                        Manage and control access permissions for all active platform profiles.
                    </p>
                </div>
                <div
                    class="text-xs font-semibold text-slate-700 bg-white px-4 py-2 rounded-xl border border-slate-200/80 shadow-2xs flex items-center gap-2 self-start sm:self-auto">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Total Profiles: <span class="font-bold text-blue-600 text-sm">{{ count($users) }}</span>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($users as $user)
                        @php
                            $initials =
                                strtoupper(substr($user->first_name, 0, 1)) .
                                strtoupper(substr($user->last_name, 0, 1));
                        @endphp

                        {{-- Highly Attractive Modern Card Design --}}
                        <div wire:key="{{ $user->id }}"
                            class="group relative bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1.5 flex flex-col items-center text-center overflow-hidden">

                            {{-- Subtle Background Glow on Hover --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-b from-blue-50/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                            </div>

                            <!-- Profile Initials Avatar with Ring Animation -->
                            <div x-data="{ backgroundColor: generateRandomColor() }" :style="{ backgroundColor: backgroundColor }"
                                class="relative w-20 h-20 sm:w-22 sm:h-22 mb-4 rounded-2xl shadow-md flex items-center justify-center text-white text-lg sm:text-xl font-bold border-2 border-white shrink-0 transition-transform duration-300 group-hover:scale-105">
                                {{ $initials }}
                                <span
                                    class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
                            </div>

                            {{-- Role Badge --}}
                            <div
                                class="relative z-10 mb-3 inline-flex items-center gap-1.5 rounded-full bg-slate-100/80 px-3.5 py-1 text-[10px] sm:text-xs font-bold uppercase tracking-widest text-slate-600 border border-slate-200/60 group-hover:bg-blue-50 group-hover:text-blue-600 group-hover:border-blue-100 transition-colors">
                                {{ Str::ucfirst($user->role) }}
                            </div>

                            {{-- User Full Name --}}
                            <h3 class="relative z-10 truncate w-full text-sm sm:text-base font-bold text-slate-800 mb-5 px-2 tracking-tight"
                                title="{{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}">
                                {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}
                            </h3>

                            {{-- Actions Footer --}}
                            <div
                                class="relative z-10 flex items-center justify-center gap-2.5 w-full mt-auto pt-4 border-t border-slate-100">
                                {{-- Edit Button --}}
                                <button wire:click="$dispatch('edituser', { id: {{ $user->id }} })"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 border border-slate-200/80 text-slate-700 text-xs font-bold uppercase tracking-wider transition-all duration-200 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:shadow-md cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z" />
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                {{-- Delete Button --}}
                                @if (auth()->user()->id !== $user->id)
                                    <button @click="userDeleteModalOpen=true; userId={{ $user->id }}"
                                        class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 border border-slate-200/80 text-rose-600 text-xs font-bold uppercase tracking-wider transition-all duration-200 hover:bg-rose-600 hover:text-white hover:border-rose-600 hover:shadow-md cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (session()->has('message'))
                    <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                @endif

                <!-- Delete Modal/Dialog -->
                <div x-show="userDeleteModalOpen" x-cloak x-transition class="relative z-50"
                    aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" inert></div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                            <div
                                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl border border-slate-200 transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 sm:p-8">
                                <div class="sm:flex sm:items-start gap-4">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 sm:mx-0 sm:h-12 sm:w-12 shadow-inner">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" inert>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-2 sm:mt-0 sm:text-left">
                                        <h3 class="text-base sm:text-lg font-bold text-slate-900" id="modal-title">
                                            Delete User Account</h3>
                                        <div class="mt-1">
                                            <p class="text-xs sm:text-sm font-medium text-slate-500">Are you sure you
                                                want to delete this user profile? This action cannot be undone.</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100">
                                    <button @click="userDeleteModalOpen = false" type="button"
                                        class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-slate-100 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 border border-slate-200 transition hover:bg-slate-200 cursor-pointer">
                                        Cancel
                                    </button>

                                    <form wire:submit.prevent="delete(userId)">
                                        <button wire:loading.attr="disabled" type="submit"
                                            class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-rose-500/35 transition-all hover:bg-rose-700 active:scale-95 disabled:opacity-60 cursor-pointer">
                                            <span wire:loading.remove>Delete User</span>
                                            <span wire:loading class="flex items-center gap-2">
                                                Deleting...
                                                <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"
                                                    fill="none">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v8H4z"></path>
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
