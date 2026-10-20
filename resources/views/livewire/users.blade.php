<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">
    <main class="scrollcontainer md:ml-16 px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pt-16 sm:pt-20 pb-12 sm:pb-16 max-w-full">

        <!-- Back to Dashboard -->
        <div class="mb-4 sm:mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-white/90 backdrop-blur-md border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-xs transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        {{-- Users Header with Icon & Import Employee Button --}}
        <div
            class="mb-5 sm:mb-8 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300/80 bg-white/95 backdrop-blur-xl shadow-lg shadow-slate-200/50 p-4 sm:p-6 lg:p-8 relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 relative z-10">

                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div
                        class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 shadow-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a7.5 7.5 0 0115 0" />
                        </svg>
                    </div>
                    <div class="space-y-0.5 sm:space-y-1 min-w-0">
                        <h1 class="text-base sm:text-xl font-bold tracking-tight text-slate-900 truncate">
                            Employee List
                        </h1>
                    </div>
                </div>

                <!-- Hidden Excel Input -->
                <input type="file" id="excelFileInput" wire:model="excelFile" accept=".xlsx,.xls,.csv"
                    class="hidden" />

                <!-- Import Actions Wrapper -->
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap w-full sm:w-auto">

                    <!-- Import Button -->
                    <button type="button" onclick="document.getElementById('excelFileInput').click()"
                        class="inline-flex items-center justify-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-bold text-white bg-[rgb(7,139,221)] hover:bg-[rgb(5,115,185)] rounded-xl sm:rounded-2xl transition-all shadow-md shadow-[rgb(7,139,221)]/20 active:scale-95 shrink-0 w-full sm:w-auto cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4 sm:w-4.5 sm:h-4.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        <span>Import Employee</span>
                    </button>

                    <!-- Selected File Name & Small Add Button -->
                    @if ($excelFile)
                        <div
                            class="inline-flex items-center gap-2 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 animate-fadeIn">
                            <span class="max-w-[120px] sm:max-w-[180px] truncate text-blue-600 font-medium">
                                {{ $excelFile->getClientOriginalName() }}
                            </span>

                            <!-- Remove File Cross Icon -->
                            <button type="button" wire:click="removeSelectedFile"
                                class="text-slate-400 hover:text-red-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>

                            <!-- Small Add Button -->
                            <button type="button" wire:click="saveUser" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg transition-all shadow-xs cursor-pointer">
                                <span wire:loading.remove wire:target="saveUser">Add</span>
                                <span wire:loading wire:target="saveUser" class="flex items-center gap-1">
                                    Adding...
                                </span>
                            </button>
                        </div>
                    @endif

                </div>

            </div>
        </div>

        <!-- User Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">

            <!-- User Cards -->
            @foreach ($users as $user)
                @php
                    $initials =
                        strtoupper(substr($user->first_name ?? '', 0, 1)) .
                        strtoupper(substr($user->last_name ?? '', 0, 1));
                    $isAuthUser = (int)$user->id === (int) auth()->id();
                    $userStatus = (int)$user->status;
                @endphp

                <div wire:key="user-card-{{ $user->id }}-{{$userStatus }}"
                    class="relative w-full bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">

                    {{-- Auth User "You" Badge --}}
                    @if ($isAuthUser)
                        <span
                            class="absolute top-3 right-3 z-10 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700 border border-blue-200 shadow-2xs">
                            You
                        </span>
                    @endif

                    {{-- Click box to trigger modal --}}
                    <div wire:click="showUserDetails({{ $user->id }})"
                        class="flex flex-col items-center p-4 sm:p-6 cursor-pointer">

                        <!-- Profile Image / Initials -->
                        <div x-data="{ backgroundColor: generateRandomColor() }" :style="{ backgroundColor: backgroundColor }"
                            class="w-16 h-16 sm:w-20 sm:h-20 mb-3 sm:mb-4 rounded-xl sm:rounded-2xl shadow-md flex items-center justify-center text-white text-lg sm:text-xl font-black tracking-wider transform group-hover:scale-105 transition-transform duration-300">
                            {{ $initials }}
                        </div>

                        <h5 class="mb-1 text-base sm:text-lg font-bold text-slate-900 text-center truncate w-full px-2">
                            {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}
                        </h5>

                        {{-- Role Badge --}}
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-slate-100 text-slate-600 mb-1">
                            Employee
                        </span>
                    </div>

                    {{-- Action Buttons --}}
                    <div
                        class="px-4 pb-4 sm:px-6 sm:pb-6 pt-3 flex items-center justify-center gap-2 w-full border-t border-slate-100">
                        <!-- Direct Dispatch Edit Button -->
                        <button type="button" wire:click="$dispatch('edituser', { id: {{$user->id }} })"
                            class="w-full inline-flex items-center justify-center px-3 py-2.5 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-xs cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1.5" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z" />
                            </svg>
                            Edit
                        </button>
                    </div>

                </div>
            @endforeach

            <!-- User Info Details Modal -->
            @if ($userDetailsModalOpen &&$selectedUser)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                    aria-modal="true">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                        wire:click="closeUserDetailsModal"></div>

                    <div class="flex min-h-full items-center justify-center p-3 text-center sm:p-0">
                        <div
                            class="relative transform overflow-hidden rounded-2xl sm:rounded-3xl bg-white text-left shadow-2xl border border-slate-100 transition-all sm:my-8 w-full max-w-lg p-4 sm:p-6 lg:p-8">

                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                <h3 class="text-base sm:text-lg font-bold text-slate-900">Employee Info Details</h3>
                                <button wire:click="closeUserDetailsModal"
                                    class="text-slate-400 hover:text-slate-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-3 sm:space-y-4">
                                <div>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Name</span>
                                    <p class="text-sm sm:text-base font-semibold text-slate-800">
                                        {{ $selectedUser->first_name . ' ' .$selectedUser->last_name }}
                                    </p>
                                </div>

                                <div>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Role</span>
                                    <p class="text-xs sm:text-sm font-semibold text-slate-800">
                                        {{ Str::ucfirst($selectedUser->role ?? 'employee') }}
                                    </p>
                                </div>

                                <div>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Category
                                        Name</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @forelse ($selectedUser->categories ?? [] as $category)
                                            <span
                                                class="px-2.5 py-0.5 text-xs font-medium rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <p class="text-xs text-slate-400 italic">No categories available</p>
                                        @endforelse
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Group
                                        Name</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @forelse ($selectedUser->groups ?? [] as $group)
                                            <span
                                                class="px-2.5 py-0.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                {{ $group->label ?? $group->label }}
                                            </span>
                                        @empty
                                            <p class="text-xs text-slate-400 italic">No groups assigned</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-3 border-t border-slate-100 flex justify-end">
                                <button wire:click="closeUserDetailsModal"
                                    class="w-full sm:w-auto px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold uppercase tracking-wider rounded-xl transition cursor-pointer">
                                    Close
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

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