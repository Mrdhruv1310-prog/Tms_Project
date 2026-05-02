<div>
    <!-- Modal -->
        <div x-data="{ show: @entangle('isOpen'), isDropdownOpen: false }" x-cloak x-show="show" class="fixed inset-0 flex items-center justify-center z-50">

        <div x-show="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

            <div x-show="show" class="relative p-4 bg-white rounded-lg shadow-lg w-full max-w-xl">

                <!-- Header -->
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <h3 class="text-lg font-semibold">Manage Users in {{ ucwords(strtolower($labelName)) }}</h3>
                    <button @click="$wire.isOpen = false" class="text-gray-400 bg-gray-200 text-gray-900 hover:bg-red-500 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg inert class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Dropdown for Adding Users -->
                <div class="w-full mb-6">
                    <label for="users" class="block mb-2 text-sm font-medium text-gray-900">Add Users</label>
                    <div class="relative" x-data="{ loadingUserId: null }">
                        <!-- Dropdown Toggle Button -->
                        <button id="dropdownGroupUsersButton" data-dropdown-toggle="dropdownGroupUsers"
                            class="flex justify-between items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
                            Select User
                            <svg class="w-2.5 h-2.5 ml-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Dropdown Content -->
                        <div id="dropdownGroupUsers" class="hidden z-10 bg-white rounded-lg shadow-md w-full">
                            <!-- Apply fixed height and scrollable overflow -->
                            <ul class="max-h-60 px-3 pb-3 overflow-y-auto text-sm text-gray-700">
                                @if ($availableUsers && is_iterable($availableUsers))
                                    @foreach ($availableUsers as $user)
                                        <li class="flex justify-between items-center py-2 px-2 rounded hover:bg-gray-100">
                                            <div class="flex items-center">
                                                <div class="w-7 h-7 ml-3 flex items-center justify-center text-white rounded-full bg-blue-600">
                                                    <span>{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                                                </div>
                                                <label class="ml-2 text-sm font-medium text-gray-900" >{{ $user['name'] }}</label>
                                            </div>
                                            <button type="button" 
                                                @click="loadingUserId = {{ $user['id'] }}"
                                                x-bind:disabled="loadingUserId === {{ $user['id'] }}"
                                                wire:click="addUser({{ $user['id'] }})"
                                                class="ml-3 px-2 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 flex items-center justify-center">
                                                <!-- Show loader if user is being added -->
                                                <span x-show="loadingUserId !== {{ $user['id'] }}">Add</span>
                                                <svg x-show="loadingUserId === {{ $user['id'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="20" height="20">
                                                    <circle stroke-dasharray="164.93361431346415 56.97787143782138" r="35" stroke-width="10" stroke="#ffffff" fill="none" cy="50" cx="50">
                                                        <animateTransform keyTimes="0;1" values="0 50 50;360 50 50" dur="1s" repeatCount="indefinite" type="rotate" attributeName="transform"></animateTransform>
                                                    </circle>
                                                </svg>
                                            </button>
                                        </li>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 text-center">No available users.</p>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Group Users List -->
                <div x-data="{ loadingUserId: null }">
                    <h4 class="text-md font-medium mb-2">Users in Group</h4>
                    @if(count($groupUsers) > 0)
                        <ul class="max-h-60 overflow-y-auto divide-y divide-gray-200">
                            @foreach($groupUsers as $user)
                                <li class="flex justify-between px-3 py-2">
                                    <span>{{ $user['first_name'] }} {{ $user['last_name'] }}</span>
                                    <button 
                                        @click="loadingUserId = {{ $user['id'] }}" 
                                        x-bind:disabled="loadingUserId === {{ $user['id'] }}"
                                        wire:click="deleteUser({{ $user['id'] }})"
                                        class="hover:underline ml-3 px-2 py-1 text-sm font-medium text-white bg-red-500 rounded flex items-center justify-center">
                                        <!-- Show loader if user is being deleted -->
                                        <span x-show="loadingUserId !== {{ $user['id'] }}">Remove</span>
                                        <svg x-show="loadingUserId === {{ $user['id'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="20" height="20">
                                            <circle stroke-dasharray="164.93361431346415 56.97787143782138" r="35" stroke-width="10" stroke="#ffffff" fill="none" cy="50" cx="50">
                                                <animateTransform keyTimes="0;1" values="0 50 50;360 50 50" dur="1s" repeatCount="indefinite" type="rotate" attributeName="transform"></animateTransform>
                                            </circle>
                                        </svg>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">No users in this group.</p>
                    @endif
                </div>                

                <!-- Footer -->
                <div class="flex justify-end mt-4">
                </div>

            </div>
        </div>
</div>
