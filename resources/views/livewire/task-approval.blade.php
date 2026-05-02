<div x-data="{ show: @entangle('taskUpdateModalOpen'), remark: '', 
    resetForm() {
        this.remark = '';
    } }" x-cloak x-show="show" class="fixed inset-0 flex items-center justify-center z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

    <div x-show="show" class="relative p-4 w-full max-w-2xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
            <!-- Modal header -->
            <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Task Remark
                </h3>
                <button type="button" @click="resetForm(); show = false;" class="text-gray-400 bg-gray-200 text-gray-900 hover:bg-red-500 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg inert class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form wire:submit.prevent="updateTaskRemark">
                <div class="mb-4">
                    <label for="users" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Users</label>
                    <div class="relative">
                        <button id="dropdownUsersButton" type="button"
                            data-dropdown-toggle="approvalDropdownUsers"
                            class="flex items-center justify-between w-full p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            Select Users
                            <svg class="w-2.5 h-2.5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                
                        <!-- Dropdown content -->
                        <div id="approvalDropdownUsers"
                            class="hidden z-10 bg-white rounded-lg shadow w-full dark:bg-gray-700">
                            <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200">
                                @foreach ($users as $user)
                                    <li wire:key="user-{{ $user->id }}" class="flex items-center py-2 px-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                        <input id="checkbox-user-{{ $user->id }}" type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <div class="ml-3 w-7 h-7 flex items-center justify-center text-white rounded-full"
                                            style="background-color: #1d4ed8;">
                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <label for="checkbox-user-{{ $user->id }}"
                                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            {{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @error('selectedUsers')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="remark" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remark</label>
                    <textarea id="remark" wire:model="remark" x-model="remark" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter Remark" rows="4"></textarea>
                    @error('remark') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center space-x-4">
                    <button type="submit"
                        class="relative flex w-full justify-center rounded-md bg-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Remark & Status</span>
                        <span wire:loading class="block inset-0 flex items-center justify-center">Updating Remark & Status... 
                            <svg class="inline-block animate-spin h-5 w-5 text-gray-500" viewBox="0 0 24 24">
                                <path fill="white"
                                    d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                    opacity="0.25" />
                                <path fill="white"
                                    d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"
                                    transform="rotate(360 12 12)" />
                            </svg>
                        </span>
                    </button>
                    <button @click="resetForm(); show = false;" type="button" class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
