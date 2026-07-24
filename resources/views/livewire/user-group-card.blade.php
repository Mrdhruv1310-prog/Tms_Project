<div class="antialiased dark:bg-gray-900">
    <main class="p-4 md:ml-16 h-auto pt-20 pb-16 bg-red">
        <div class="relative flex items-center mb-6">
            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="window.history.back()">
                <svg class="w-5 h-5 transform rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                </svg>
                <span class="sr-only">Go Back</span>
            </button>

            <h3 class="absolute left-1/2 -translate-x-1/2 text-2xl font-semibold text-center">
                Manage User Groups
            </h3>
        </div>

        <div class="relative mb-6 flex flex-col items-center sm:flex-row sm:justify-center space-x-4">
            <!-- Input field -->
            <div class="relative mb-4 sm:mb-0">
                <input wire:model="newGroup" type="text" id="floating_outlined"
                    class="block px-2.5 pb-2.5 pt-4 text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="floating_outlined"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Group Name</label>
                @error('newGroup')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Add Category Button -->
            <button wire:click="addGroup" wire:loading.attr="disabled" type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mt-0 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <span wire:loading.remove wire:target="addGroup">Add Group</span>
                <span wire:loading wire:target="addGroup">Adding...</span>
            </button>
        </div>
        <!-- Group Cards Grid -->
        <div x-data="{ userId: null }" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($groups as $group)
                @php
                    // Extracting initials properly from the group label string
                    $words = explode(' ', $group['label'] ?? '');
                    $initials = '';
                    foreach ($words as $w) {
                        $initials .= strtoupper(substr($w, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
                @endphp

                <div wire:key="{{ $group['id'] }}" class="relative w-full bg-white/95 backdrop-blur-md border border-slate-200/60 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                    <div class="flex flex-col items-center px-6 py-8 text-center">

                        <!-- Premium Initial Badge Placeholder with Consistent Seed-Based Colors -->
                        <div x-data="{ backgroundColor: generateRandomColor('{{ $group['label'] }}') }" :style="{ backgroundColor: backgroundColor }"
                            class="w-20 h-20 mb-4 rounded-full shadow-inner border-4 border-white flex items-center justify-center text-white text-xl font-black tracking-wider uppercase transition-transform duration-300 hover:scale-105">
                            {{ $initials ?: 'GP' }}
                        </div>

                    <!-- Group Label or Input -->
                    <div>
                        @if ($editingGroupId === $group['id'])
                            <input
                                wire:model.defer="editingGroupName"
                                type="text" placeholder="Group Name"
                                class="block px-2.5 pb-2.5 pt-4 text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            />
                            <div class="flex gap-4">
                                <button
                                    wire:click="saveGroupName"
                                        class="bg-green-500 text-white rounded px-2 py-1 mt-2 flex items-center gap-2"
                                    >
                                    <!-- Loader shown when saving -->
                                    <svg wire:loading wire:target="saveGroupName" class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <!-- Text -->
                                    <span wire:loading.remove wire:target="saveGroupName">Save</span>
                                </button>
                                <button
                                    wire:click="cancelEditing"
                                    class="bg-red-500 text-white rounded px-2 py-1 mt-2">
                                    <svg wire:loading wire:target="cancelEditing" class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="cancelEditing">Cancel</span>
                                </button>
                            </div>
                        @else
                            <h5 class="mb-2 text-lg font-semibold text-gray-900">
                                {{ $group['label'] }}
                            </h5>
                        @endif
                    </div>

                        <!-- Buttons -->
                    <div class="flex gap-4 mt-4">
                        <!-- View Button -->
                        <button
                            wire:click="$dispatch('openUserGroupModal',{ labelId: {{ $group['id'] }} })"
                            x-data="{ isLoading: false }"
                            x-on:click="isLoading = true"
                            x-on:viewusergroupmodalopened.window="console.log('Modal opened'); isLoading = false"
                            x-bind:class="{ 'opacity-50 cursor-not-allowed': isLoading }"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">

                            <!-- Icon or Loader -->
                            <svg x-show="!isLoading" class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3.5" stroke="#222222"/>
                                <path d="M20.188 10.9343C20.5762 11.4056 20.7703 11.6412 20.7703 12C20.7703 12.3588 20.5762 12.5944 20.188 13.0657C18.7679 14.7899 15.6357 18 12 18C8.36427 18 5.23206 14.7899 3.81197 13.0657C3.42381 12.5944 3.22973 12.3588 3.22973 12C3.22973 11.6412 3.42381 11.4056 3.81197 10.9343C5.23206 9.21014 8.36427 6 12 6C15.6357 6 18.7679 9.21014 20.188 10.9343Z" stroke="#222222"/>
                            </svg>

                            <svg x-show="isLoading" class="w-5 h-5 mr-2 animate-spin text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>

                            <span>View</span>
                        </button>

                            <!-- Edit Button -->
                            <button
                                wire:click="startEditing({{ $group['id'] }}, '{{ $group['label'] }}')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700 gap-2"
                            >
                                <!-- Loader shown when editing -->
                                <svg
                                    wire:loading
                                    wire:target="startEditing({{ $group['id'] }}, '{{ $group['label'] }}')"
                                    class="w-5 h-5 animate-spin text-blue-700"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>

                                <!-- Edit Icon and Text -->
                                <span wire:loading.remove wire:target="startEditing({{ $group['id'] }}, '{{ $group['label'] }}')">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M21.1213 2.70705C19.9497 1.53548 18.0503 1.53547 16.8787 2.70705L15.1989 4.38685L7.29289 12.2928C7.16473 12.421 7.07382 12.5816 7.02986 12.7574L6.02986 16.7574C5.94466 17.0982 6.04451 17.4587 6.29289 17.707C6.54127 17.9554 6.90176 18.0553 7.24254 17.9701L11.2425 16.9701C11.4184 16.9261 11.5789 16.8352 11.7071 16.707L19.5556 8.85857L21.2929 7.12126C22.4645 5.94969 22.4645 4.05019 21.2929 2.87862L21.1213 2.70705ZM18.2929 4.12126C18.6834 3.73074 19.3166 3.73074 19.7071 4.12126L19.8787 4.29283C20.2692 4.68336 20.2692 5.31653 19.8787 5.70705L18.8622 6.72357L17.3068 5.10738L18.2929 4.12126ZM15.8923 6.52185L17.4477 8.13804L10.4888 15.097L8.37437 15.6256L8.90296 13.5112L15.8923 6.52185ZM4 7.99994C4 7.44766 4.44772 6.99994 5 6.99994H10C10.5523 6.99994 11 6.55223 11 5.99994C11 5.44766 10.5523 4.99994 10 4.99994H5C3.34315 4.99994 2 6.34309 2 7.99994V18.9999C2 20.6568 3.34315 21.9999 5 21.9999H16C17.6569 21.9999 19 20.6568 19 18.9999V13.9999C19 13.4477 18.5523 12.9999 18 12.9999C17.4477 12.9999 17 13.4477 17 13.9999V18.9999C17 19.5522 16.5523 19.9999 16 19.9999H5C4.44772 19.9999 4 19.5522 4 18.9999V7.99994Z" fill="currentColor"></path>
                                    </svg>
                                    Edit
                                </span>
                            </button>
                    </div>

                    </div>
                </div>
            @endforeach
        </div>
    </main>
</div>

<script>
    function generateRandomColor() {
        const min = 30; // Minimum value for RGB
        const max = 200; // Maximum value for RGB
        const r = Math.floor(Math.random() * (max - min + 1)) + min;
        const g = Math.floor(Math.random() * (max - min + 1)) + min;
        const b = Math.floor(Math.random() * (max - min + 1)) + min;
        return `rgb(${r}, ${g}, ${b})`;
    }
</script>
