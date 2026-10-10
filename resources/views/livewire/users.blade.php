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

        {{-- Users Header with Icon --}}
        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a7.5 7.5 0 0115 0" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900">
                            Employee List
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Cards Grid -->
        <div x-data="{ userDeleteModalOpen: false, userId: null }" @userdeleted.window="userDeleteModalOpen = false"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-6">

            <!-- User Cards -->
            @foreach ($users as $user)
                @php
                    $initials =
                        strtoupper(substr($user->first_name ?? '', 0, 1)) .
                        strtoupper(substr($user->last_name ?? '', 0, 1));
                @endphp

                <div wire:key="{{ $user->id }}"
                    class="w-full bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-100/50 dark:bg-gray-900 dark:border-gray-800 dark:shadow-none hover:shadow-2xl transition-all duration-300 overflow-hidden group">

                    {{-- Click box to trigger modal --}}
                    <div wire:click="showUserDetails({{ $user->id }})"
                        class="flex flex-col items-center p-6 sm:p-8 cursor-pointer">

                        <!-- Profile Image or Initials -->
                        <div x-data="{ backgroundColor: generateRandomColor() }" :style="{ backgroundColor: backgroundColor }"
                            class="w-20 h-20 mb-4 rounded-2xl shadow-lg flex items-center justify-center text-white text-xl font-black tracking-wider transform group-hover:scale-105 transition-transform duration-300">
                            {{ $initials }}
                        </div>

                        <h5 class="mb-1 text-lg font-bold text-gray-900 dark:text-white text-center">
                            {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}
                        </h5>

                        {{-- Role Badge (Always showing Employee) --}}
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-gray-800 dark:text-gray-300 mb-6">
                            Employee
                        </span>
                    </div>

                    {{-- Edit & Delete Buttons (Visible only to Admin) --}}
                    @if (in_array(strtolower(trim(auth()->user()->role ?? '')), ['admin'], true))
                        <div
                            class="px-6 pb-6 pt-0 flex items-center justify-center gap-2 w-full border-t border-slate-100 dark:border-gray-800">
                            <!-- Edit Button -->
                            <button wire:click="$dispatch('edituser', { id: {{ $user->id }} })"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-xl transition-all shadow-md shadow-blue-500/20 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z" />
                                </svg>
                                Edit
                            </button>

                            <!-- Delete Button -->
                            {{-- @if (auth()->user()->id !== $user->id) --}}
                            @if (strtolower(trim(auth()->user()->role ?? '')) === 'admin')
                                <button @click="userDeleteModalOpen=true; userId={{ $user->id }}"
                                    class="flex-1 py-2.5 px-4 text-red-600 dark:text-red-400 inline-flex items-center justify-center hover:text-white border border-red-200 dark:border-red-900/50 hover:bg-red-600 hover:border-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                    <svg class="mr-1.5 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Delete
                                </button>
                            @endif
                        </div>
                    @endif

                </div>
            @endforeach

            <!-- User Info Details Modal -->
            @if ($userDetailsModalOpen && $selectedUser)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                    aria-modal="true">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                        wire:click="closeUserDetailsModal"></div>

                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div
                            class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-900 text-left shadow-2xl border border-slate-100 dark:border-gray-800 transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 sm:p-8">

                            {{-- Modal Header --}}
                            <div
                                class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-gray-800 mb-6">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Employee Info Details</h3>
                                <button wire:click="closeUserDetailsModal"
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="space-y-4">
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Name</span>
                                    <p class="text-base font-semibold text-slate-800 dark:text-white">
                                        {{ $selectedUser->first_name . ' ' . $selectedUser->last_name }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Role</span>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">
                                        {{ Str::ucfirst($selectedUser->role ?? 'employee') }}
                                    </p>
                                </div>

                                {{-- Category Name --}}
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Category
                                        Name</span>
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @forelse ($selectedUser->categories ?? [] as $category)
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <p class="text-xs text-slate-400 italic">No categories available</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Group Name --}}
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Group
                                        Name</span>
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @forelse ($selectedUser->groups ?? [] as $group)
                                            <span
                                                class="px-3 py-1 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                {{ $group->label ?? $group->label }}
                                            </span>
                                        @empty
                                            <p class="text-xs text-slate-400 italic">No groups assigned</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-gray-800 flex justify-end">
                                <button wire:click="closeUserDetailsModal"
                                    class="px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-gray-800 dark:text-gray-300 text-xs font-bold uppercase tracking-wider rounded-xl transition cursor-pointer">
                                    Close
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            <!-- Modern Delete Modal/Dialog -->
            <div x-show="userDeleteModalOpen" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="relative z-50" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">

                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                    @click="userDeleteModalOpen = false" inert></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                        <div x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-gray-900 text-left shadow-2xl border border-slate-100 dark:border-gray-800 transition-all sm:my-8 sm:w-full sm:max-w-md p-6 sm:p-8">

                            <div class="sm:flex sm:items-start gap-4">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 sm:mx-0 shadow-inner">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" inert>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-2 sm:mt-0 sm:text-left">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white"
                                        id="modal-title">
                                        Delete User</h3>
                                    <div class="mt-1">
                                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Are you sure you want to delete user? This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-100 dark:border-gray-800">
                                <button @click="userDeleteModalOpen = false" type="button"
                                    class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-slate-100 dark:bg-gray-800 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-gray-300 border border-slate-200 dark:border-gray-700 transition hover:bg-slate-200 dark:hover:bg-gray-700 cursor-pointer">
                                    Cancel
                                </button>

                                <button type="button" @click="$wire.delete(userId)" wire:loading.attr="disabled"
                                    class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-red-500/35 transition-all hover:bg-red-700 active:scale-95 disabled:opacity-60 cursor-pointer">
                                    <span wire:loading.remove>Delete User</span>
                                    <span wire:loading class="flex items-center gap-2">
                                        Deleting User...
                                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"
                                            fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                                            </path>
                                        </svg>
                                    </span>
                                </button>
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
        const min = 10;
        const max = 230;

        const r = Math.floor(Math.random() * (max - min + 1)) + min;
        const g = Math.floor(Math.random() * (max - min + 1)) + min;
        const b = Math.floor(Math.random() * (max - min + 1)) + min;

        return `rgb(${r}, ${g}, ${b})`;
    }
</script>
