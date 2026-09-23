<div>
    <div
        class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">

        <!-- Decorative Glow Elements for Professional Depth -->
        <div
            class="absolute -top-32 -left-32 w-96 h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute -bottom-32 -right-32 w-96 h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
        </div>

        <main class="scrollcontainer md:ml-16 px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16">


            @php
                $titles = [
                    'my_tasks' => 'My Tasks',
                    'assigned_to_others' => 'Tasks Assigned to Others',
                    'tasks' => 'All Tasks',
                ];
                $title = $titles[$this->taskView] ?? 'Task Overview'; // Default title
            @endphp

            <h3 class="text-2xl font-semibold mb-2 text-center">{{ $title }}</h3>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                <div class="mb-5 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/80 backdrop-blur-md border border-gray-200/80 text-sm font-semibold text-gray-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-sm transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span>Back to Dashboard</span>
                    </a>
                </div>

                <section class="pt-4">
                    @if (session()->has('message'))
                        <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                    @endif
                    <!-- Loader -->
                    <div wire:loading.flex
                        wire:target.except="$dispatch,close-modal,callMountedTableAction, mountTableAction,nextPage,gotoPage,previousPage"
                        class="absolute inset-0 bg-white bg-opacity-75 flex justify-center items-center z-50">
                        <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="ml-2 text-gray-500">Loading Task...</span>
                    </div>

                    <!-- Table -->
                    {{ $this->table }}

                </section>
            </div>
        </main>
    </div>
</div>
