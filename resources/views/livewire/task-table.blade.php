<div>
    <div class="antialiased dark:bg-gray-900">
        <main class="scrollcontainer p-4 md:ml-16 h-auto pt-14 pb-16">
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
                <section class="pt-4">
                    @if (session()->has('message'))
                    <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                @endif
                    <!-- Loader -->
                    <div wire:loading.flex wire:target.except="$dispatch,close-modal,callMountedTableAction, mountTableAction,nextPage,gotoPage,previousPage" class="absolute inset-0 bg-white bg-opacity-75 flex justify-center items-center z-50">
                        <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
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
