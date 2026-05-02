<div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('isOpen') }"
    x-show="show" x-cloak>

    <!-- Background backdrop, controlled by x-show and transitions -->
    <div x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

    <!-- Drawer container -->
    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <!-- Slide-over panel with transitions -->
                <div x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto relative w-screen max-w-4xl">

                    <!-- Close button -->
                    {{-- <div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4">
                        <button type="button" @click="show = false"
                            class="relative rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                            <span class="sr-only">Close panel</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" inert>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div> --}}

                    <!-- Drawer content -->
                    <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                        <div class="px-2 sm:px-3" style="display: flex;">
                            <button type="button" @click="show = false"
                                class="relative rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
                                <span class="sr-only">Close panel</span>
                                <!-- Back arrow with line SVG -->
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </button>
                            <h1 class="px-2 text-xl font-semibold leading-6 text-gray-900" id="slide-over-title"
                                style="display: inline-block;">
                                Task Details
                            </h1>
                        </div>

                        <!-- Main Content -->
                        <div class="relative px-4 py-6 sm:px-6 overflow-y-auto">
                            <div class="bg-gray-100 p-3 rounded-md">
                                <!-- Task Title -->
                                <h2 class="text-md font-bold text-gray-900">{{ $taskList->title ?? 'N/A' }}</h2>
                                <div class="flex flex-col lg:flex-row mt-5">
                                    <!-- First Pair (1st Column) -->
                                    <div class="flex w-full gap-4 lg:w-1/2">
                                        <!-- Column 1 -->
                                        <div class="flex-col mb-2 w-24">
                                            <p class="font-semibold text-gray-900 whitespace-normal break-words">
                                                Assigned To</p>
                                        </div>
                                        <!-- Column 2 -->
                                        <div class="flex-col space-y-4">
                                            <!-- Repeat this block for each person -->
                                            @if ($taskList && $taskList->assignedUsers->isNotEmpty())
                                                @foreach ($taskList->assignedUsers as $user)
                                                    <div class="flex items-center space-x-4" style="margin-top: 2px;"
                                                        wire:key="{{ $user->id }}">
                                                        <!-- Display initials of the user as fallback for profile image -->
                                                        <div
                                                            class="w-10 h-10 bg-green-400 text-white rounded-full flex items-center justify-center">
                                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-900">{{ $user->first_name }}
                                                                {{ $user->last_name }}</p>
                                                            <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p>No users assigned</p>
                                            @endif
                                        </div>
                                    </div>
                                    <hr style="border-color: #a9a9a9;margin-top: 10px;margin-bottom: 10px;">
                                    <!-- Second Pair (3rd Column) -->
                                    <div class="flex w-full gap-4 lg:w-1/2">
                                        <!-- Column 3 -->
                                        <div class="flex-col mb-2 w-24">
                                            <p class="font-semibold text-gray-900 whitespace-normal break-words">
                                                Assigned By</p>
                                        </div>
                                        <!-- Column 4 -->
                                        <div class="flex-col space-y-4">
                                            <!-- Repeat this block for each person -->
                                            @if ($taskList && $taskList->assignedBy)
                                                <div class="flex items-center space-x-4">
                                                    <div
                                                        class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center">
                                                        {{ strtoupper(substr($taskList->assignedBy->first_name, 0, 1)) }}{{ strtoupper(substr($taskList->assignedBy->last_name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-900">{{ $taskList->assignedBy->first_name }}
                                                            {{ $taskList->assignedBy->last_name }}</p>
                                                        <p class="text-gray-500 text-xs">
                                                            {{ $taskList->assignedBy->email }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <p>No assigned by information available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="mt-5">
                                    <p class="text-sm"><strong>Created At:</strong>
                                        {{ $taskList ? $taskList->created_at->format('D, M j, Y - g:i A') : 'N/A' }}
                                    </p>
                                </div>
                                <div class="mt-5">
                                    <p class="text-sm mt-1">
                                        <strong>Due Date:</strong>
                                        <span
                                            class="{{ isset($taskList->due_date) && now()->greaterThan(\Carbon\Carbon::parse($taskList->due_date)) ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $taskList && $taskList->due_date ? \Carbon\Carbon::parse($taskList->due_date)->format('D, M j, Y - g:i A') : 'N/A' }}
                                        </span>
                                    </p>
                                </div>

                                <!-- Status and Priority -->
                                <div class="flex items-center justify-between mt-5">
                                    <p><strong>Status:</strong>
                                        @if ($taskList)
                                            @switch($taskList->status)
                                                @case('pending')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded">
                                                        {{ ucfirst($taskList->status) }}
                                                    </span>
                                                @break

                                                @case('in_progress')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded">
                                                        {{ ucfirst($taskList->status) }}
                                                    </span>
                                                @break

                                                @case('completed')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-green-800 bg-green-100 rounded">
                                                        {{ ucfirst($taskList->status) }}
                                                    </span>
                                                @break

                                                @default
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded">
                                                        {{ ucfirst($taskList->status) }}
                                                    </span>
                                            @endswitch
                                        @else
                                            <span
                                                class="px-2 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded">N/A</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="mt-5">
                                    <p class="mt-1"><strong>Category:</strong>
                                        {{ $taskList ? ucfirst($taskList->category->name) : 'N/A' }}</p>
                                </div>

                                <!-- Priority -->
                                <div class="mt-5">
                                    <div class="text-sm">
                                        <p><strong>Priority:</strong>
                                            @if ($taskList)
                                                @switch($taskList->priority)
                                                    @case('low')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-green-800">
                                                            <!-- Low Priority Flag Icon -->
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M5.75 1C6.16421 1 6.5 1.33579 6.5 1.75V3.6L8.22067 3.25587C9.8712 2.92576 11.5821 3.08284 13.1449 3.70797L13.3486 3.78943C14.9097 4.41389 16.628 4.53051 18.2592 4.1227C19.0165 3.93339 19.75 4.50613 19.75 5.28669V12.6537C19.75 13.298 19.3115 13.8596 18.6864 14.0159L18.472 14.0695C16.7024 14.5119 14.8385 14.3854 13.1449 13.708C11.5821 13.0828 9.8712 12.9258 8.22067 13.2559L6.5 13.6V21.75C6.5 22.1642 6.16421 22.5 5.75 22.5C5.33579 22.5 5 22.1642 5 21.75V1.75C5 1.33579 5.33579 1 5.75 1Z"
                                                                    fill="#22C55E" />
                                                            </svg>
                                                            {{ ucfirst($taskList->priority) }}
                                                        </span>
                                                    @break

                                                    @case('medium')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-yellow-800">
                                                            <!-- Medium Priority Flag Icon -->
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M5.75 1C6.16421 1 6.5 1.33579 6.5 1.75V3.6L8.22067 3.25587C9.8712 2.92576 11.5821 3.08284 13.1449 3.70797L13.3486 3.78943C14.9097 4.41389 16.628 4.53051 18.2592 4.1227C19.0165 3.93339 19.75 4.50613 19.75 5.28669V12.6537C19.75 13.298 19.3115 13.8596 18.6864 14.0159L18.472 14.0695C16.7024 14.5119 14.8385 14.3854 13.1449 13.708C11.5821 13.0828 9.8712 12.9258 8.22067 13.2559L6.5 13.6V21.75C6.5 22.1642 6.16421 22.5 5.75 22.5C5.33579 22.5 5 22.1642 5 21.75V1.75C5 1.33579 5.33579 1 5.75 1Z"
                                                                    fill="#FBBF24" />
                                                            </svg>
                                                            {{ ucfirst($taskList->priority) }}
                                                        </span>
                                                    @break

                                                    @case('high')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 text-sm font-medium text-red-800">
                                                            <!-- High Priority Flag Icon -->
                                                            <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M5.75 1C6.16421 1 6.5 1.33579 6.5 1.75V3.6L8.22067 3.25587C9.8712 2.92576 11.5821 3.08284 13.1449 3.70797L13.3486 3.78943C14.9097 4.41389 16.628 4.53051 18.2592 4.1227C19.0165 3.93339 19.75 4.50613 19.75 5.28669V12.6537C19.75 13.298 19.3115 13.8596 18.6864 14.0159L18.472 14.0695C16.7024 14.5119 14.8385 14.3854 13.1449 13.708C11.5821 13.0828 9.8712 12.9258 8.22067 13.2559L6.5 13.6V21.75C6.5 22.1642 6.16421 22.5 5.75 22.5C5.33579 22.5 5 22.1642 5 21.75V1.75C5 1.33579 5.33579 1 5.75 1Z"
                                                                    fill="#EF4444" />
                                                            </svg>
                                                            {{ ucfirst($taskList->priority) }}
                                                        </span>
                                                    @break
                                                @endswitch
                                            @else
                                                <span class="px-2 py-1 text-sm font-medium text-gray-800">N/A</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- reminder -->
                                <div class="mt-5">
                                    <div class="text-sm">
                                        <p><strong>Reminder:</strong>
                                            @if ($taskList && $taskList->reminders && $taskList->reminders->count())
                                                <ul class="mt-1 ml-4 list-disc text-gray-700">
                                                    @foreach ($taskList->reminders as $reminder)
                                                        <li>
                                                            {{ $reminder->user->first_name ?? 'Unknown User' }}:
                                                            {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('d-m-Y h:i a') }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="px-2 py-1 text-sm font-medium text-gray-800">N/A</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mt-5">
                                    <p class="text-sm"><strong>Description:</strong>
                                        {{ $taskList ? $taskList->description : 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11 6L21 6.00072M11 12L21 12.0007M11 18L21 18.0007M3 11.9444L4.53846 13.5L8 10M3 5.94444L4.53846 7.5L8 4M4.5 18H4.51M5 18C5 18.2761 4.77614 18.5 4.5 18.5C4.22386 18.5 4 18.2761 4 18C4 17.7239 4.22386 17.5 4.5 17.5C4.77614 17.5 5 17.7239 5 18Z"
                                            stroke="#000000" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <h1 class="text-xl font-semibold leading-6 text-gray-900" id="slide-over-title">
                                        Task Activity Log</h1>
                                </div>

                                <!-- Repeat this block for each update in the task updates list -->
                                @foreach ($taskUpdates as $update)
                                    <div class="mt-2 space-y-6 bg-gray-100 p-3 rounded-md"
                                        wire:key="{{ $update->id }}">

                                        <div class="flex items-start space-x-4">
                                            <!-- User's initials -->
                                            <div
                                                class="w-10 h-10 bg-indigo-500 text-white rounded-full flex items-center justify-center">
                                                {{ strtoupper(substr($update->user->first_name, 0, 1)) }}{{ strtoupper(substr($update->user->last_name, 0, 1)) }}
                                            </div>
                                            <!-- User's name and time -->
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $update->user->first_name }} {{ $update->user->last_name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $update->created_at->diffForHumans() }}
                                                    ({{ $update->updated_at->format('D, M j, Y - g:i A') }})</p>
                                            </div>
                                            <!-- Task status -->
                                            <div>
                                                <span
                                                    class="inline-block px-3 py-1 text-sm font-medium rounded-full
                                                        @if ($update->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($update->status === 'in_progress') bg-blue-100 text-blue-800
                                                        @elseif($update->status === 'complete_intimation') bg-amber-100
                                                        @elseif($update->status === 'completed') bg-green-100 text-green-800 @endif">
                                                    @switch($update->status)
                                                        @case('pending')
                                                            Pending
                                                        @break

                                                        @case('in_progress')
                                                            In Progress
                                                        @break

                                                        @case('complete_intimation')
                                                            Requested for Complete
                                                        @break

                                                        @case('completed')
                                                            Completed
                                                        @break

                                                        @default
                                                            {{ ucfirst($update->status) }}
                                                    @endswitch
                                                </span>
                                            </div>

                                        </div>

                                        <!-- Remark (new row) -->
                                        <div class="ml-14">
                                            <p class="text-sm text-gray-700">{{ $update->comment }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
