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
                        Manage Users
                    </h3>
                </div>
                <!-- User Cards Grid -->
                <div x-data="{ userDeleteModalOpen: false, userId: null }" @userdeleted.window="userDeleteModalOpen = false"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <!-- User Cards -->
                    @foreach ($users as $user)
                        @php
                            $initials =
                                strtoupper(substr($user->first_name, 0, 1)) .
                                strtoupper(substr($user->last_name, 0, 1));
                        @endphp

                        <div wire:key="{{$user->id}}"
                            class="w-full max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                            <div class="flex flex-col items-center px-4 pt-8 pb-10">
                                <!-- Profile Image or Initials -->
                                <div x-data="{ backgroundColor: generateRandomColor() }" :style="{ backgroundColor: backgroundColor }"
                                    class="w-24 h-24 mb-3 rounded-full shadow-lg flex items-center justify-center text-white text-2xl font-bold">
                                    {{ $initials }}
                                </div>
                                <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">
                                    {{ Str::ucfirst($user->first_name) . ' ' . Str::ucfirst($user->last_name) }}
                                </h5>
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400">{{ Str::ucfirst($user->role) }}</span>
                                <div class="flex mt-4 md:mt-6">

                                    <button wire:click="$dispatch('edituser', { id: {{ $user->id }} })"
                                        class="inline-flex items-center px-4 py-2 text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M12 20h9" />
                                            <path d="M16.5 3.5l4 4L7 21H3v-4L16.5 3.5z" />
                                        </svg>
                                        Edit
                                    </button>

                                    @if (auth()->user()->id !== $user->id)
                                        <button @click="userDeleteModalOpen=true;userId={{ $user->id }}"
                                            class="py-2 px-4 ms-2 text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                            <svg class="mr-1 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (session()->has('message'))
                        <div x-init="$dispatch('notify', { msg: '{{ session('message') }}', type: 'success' })"></div>
                    @endif

                    <!-- Modal/Dialog -->
                    <div x-show="userDeleteModalOpen" x-cloak x-transition class="relative z-50"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert>
                        </div>

                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div
                                class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                                <div
                                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                        <div class="sm:flex sm:items-start">
                                            <div
                                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" inert>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                                </svg>
                                            </div>
                                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                <h3 class="text-base font-semibold leading-6 text-gray-900"
                                                    id="modal-title">Delete User</h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500">Are you sure you want to delete
                                                        user?</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 mt-3 sm:flex sm:flex-row justify-end sm:px-6">
                                        <form wire:submit.prevent="delete(userId)">
                                            <button wire:loading.attr="disabled" type="submit"
                                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:mt-0 sm:w-auto">
                                                <span wire:loading.remove>Delete User</span>
                                                <span wire:loading
                                                    class="inset-0 flex items-center justify-center">Deleting User...
                                                    <svg class="inline-block animate-spin h-5 w-5 text-gray-500"
                                                        viewBox="0 0 24 24">
                                                        <path fill="white"
                                                            d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                                            opacity="0.25" />
                                                        <path fill="white"
                                                            d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                                                            transform="rotate(360 12 12)" />
                                                    </svg>
                                                </span>
                                            </button>
                                        </form>

                                        <button
                                            @click="userDeleteModalOpen = false"
                                            type="button"
                                            class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mt-3 sm:ml-3 sm:mt-0 sm:w-auto">Cancel</button>
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
        const min = 10; // minimum value for R, G, B
        const max = 230; // maximum value for R, G, B

        const r = Math.floor(Math.random() * (max - min + 1)) + min;
        const g = Math.floor(Math.random() * (max - min + 1)) + min;
        const b = Math.floor(Math.random() * (max - min + 1)) + min;

        return `rgb(${r}, ${g}, ${b})`;
    }
</script>
