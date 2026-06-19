<div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('isOpen') }"
    x-show="show" x-cloak wire:key="task-view-modal-{{ $taskId }}">

    <div x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" inert></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto relative w-screen max-w-4xl">

                    <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                        <div class="px-2 sm:px-3" style="display: flex;">
                            <button type="button" wire:click="close"
                                class="relative rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
                                <span class="sr-only">Close panel</span>
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

                        <div class="relative px-4 py-6 sm:px-6 overflow-y-auto">
                            @if ($taskList)
                                <div class="bg-gray-100 p-3 rounded-md">
                                    <h2 class="text-md font-bold text-gray-900">{{ $taskList->title ?? 'N/A' }}</h2>

                                    <div class="flex flex-col lg:flex-row mt-5">
                                        <div class="flex w-full gap-4 lg:w-1/2">
                                            <div class="flex-col mb-2 w-24">
                                                <p class="font-semibold text-gray-900 whitespace-normal break-words">
                                                    Assigned To</p>
                                            </div>
                                            <div class="flex-col space-y-4">
                                                @forelse ($taskList->assignedUsers as $user)
                                                    <div class="flex items-center space-x-4" style="margin-top: 2px;"
                                                        wire:key="assigned-user-{{ $taskList->id }}-{{ $user->id }}">
                                                        <div
                                                            class="w-10 h-10 bg-green-400 text-white rounded-full flex items-center justify-center">
                                                            {{ strtoupper(substr($user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-900">{{ $user->first_name }}
                                                                {{ $user->last_name }}</p>
                                                            <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p>No users assigned</p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <hr style="border-color: #a9a9a9;margin-top: 10px;margin-bottom: 10px;">

                                        <div class="flex w-full gap-4 lg:w-1/2">
                                            <div class="flex-col mb-2 w-24">
                                                <p class="font-semibold text-gray-900 whitespace-normal break-words">
                                                    Assigned By</p>
                                            </div>
                                            <div class="flex-col space-y-4">
                                                @if ($taskList->assignedBy)
                                                    <div class="flex items-center space-x-4">
                                                        <div
                                                            class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center">
                                                            {{ strtoupper(substr($taskList->assignedBy->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($taskList->assignedBy->last_name ?? '', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-900">
                                                                {{ $taskList->assignedBy->first_name }}
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

                                    <div class="mt-5">
                                        <p class="text-sm"><strong>Created At:</strong>
                                            {{ $taskList->created_at ? $taskList->created_at->format('D, M j, Y - g:i A') : 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="mt-5">
                                        <p class="text-sm mt-1"><strong>Due Date:</strong>
                                            <span
                                                class="{{ $taskList->due_date && now()->greaterThan(\Carbon\Carbon::parse($taskList->due_date)) ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $taskList->due_date ? \Carbon\Carbon::parse($taskList->due_date)->format('D, M j, Y - g:i A') : 'N/A' }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between mt-5">
                                        <p><strong>Status:</strong>
                                            @switch($taskList->status)
                                                @case('pending')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded">Pending</span>
                                                @break

                                                @case('in_progress')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-blue-800 bg-blue-100 rounded">In
                                                        Progress</span>
                                                @break

                                                @case('complete_intimation')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-amber-800 bg-amber-100 rounded">Requested
                                                        for Complete</span>
                                                @break

                                                @case('completed')
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-green-800 bg-green-100 rounded">Completed</span>
                                                @break

                                                @default
                                                    <span
                                                        class="px-2 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded">{{ ucfirst($taskList->status) }}</span>
                                            @endswitch
                                        </p>
                                    </div>

                                    <div class="mt-5">
                                        <p class="mt-1"><strong>Category:</strong>
                                            {{ $taskList->category ? ucfirst($taskList->category->name) : 'N/A' }}</p>
                                    </div>

                                    <div class="mt-5">
                                        <p><strong>Priority:</strong> {{ ucfirst($taskList->priority ?? 'N/A') }}</p>
                                    </div>

                                    <div class="mt-5">
                                        <div class="text-sm">
                                            <p><strong>Reminder:</strong></p>
                                            @if ($taskList->reminders && $taskList->reminders->count())
                                                <ul class="mt-1 ml-4 list-disc text-gray-700">
                                                    @foreach ($taskList->reminders as $reminder)
                                                        <li wire:key="reminder-{{ $reminder->id }}">
                                                            {{ $reminder->user->first_name ?? 'Unknown User' }}:
                                                            {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('d-m-Y h:i a') }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="px-2 py-1 text-sm font-medium text-gray-800">N/A</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <p class="text-sm"><strong>Description:</strong>
                                            {{ $taskList->description ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <div class="flex items-center">
                                        <h1 class="text-xl font-semibold leading-6 text-gray-900">Task Activity Log</h1>
                                    </div>

                                    @forelse ($taskUpdates as $update)
                                        <div class="mt-2 space-y-6 bg-gray-100 p-3 rounded-md"
                                            wire:key="task-update-{{ $update->id }}-{{ $update->status }}">
                                            <div class="flex items-start space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-indigo-500 text-white rounded-full flex items-center justify-center">
                                                    {{ strtoupper(substr($update->user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($update->user->last_name ?? '', 0, 1)) }}
                                                </div>

                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $update->user->first_name ?? 'Unknown' }}
                                                        {{ $update->user->last_name ?? '' }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        @if ($update->created_at)
                                                            {{ \Carbon\Carbon::parse($update->created_at)->diffForHumans() }}
                                                            ({{ \Carbon\Carbon::parse($update->created_at)->format('D, M j, Y - g:i A') }})
                                                        @endif
                                                    </p>
                                                </div>

                                                <div>
                                                    <span
                                                        class="inline-block px-3 py-1 text-sm font-medium rounded-full
                                                        @if ($update->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif ($update->status === 'in_progress') bg-blue-100 text-blue-800
                                                        @elseif ($update->status === 'complete_intimation') bg-amber-100 text-amber-800
                                                        @elseif ($update->status === 'completed') bg-green-100 text-green-800
                                                        @else bg-gray-100 text-gray-800 @endif">
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

                                            <div class="ml-14">
                                                <p class="text-sm text-gray-700">{{ $update->comment }}</p>
                                            </div>
                                        </div>
                                        @empty
                                            <div class="mt-2 bg-gray-100 p-3 rounded-md">
                                                <p class="text-sm text-gray-700">No activity found.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                @else
                                    <div class="bg-gray-100 p-3 rounded-md">
                                        <p class="text-sm text-gray-700">Task data not found.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
