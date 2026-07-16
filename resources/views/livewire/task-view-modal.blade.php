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
                                <div class="mt-6 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

                                    <div class="bg-gray-50 px-4 py-3 border-b">
                                        <h3 class="font-semibold text-lg text-gray-800">
                                            Task Information
                                        </h3>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">

                                            <tbody class="divide-y divide-gray-200">

                                                <tr>
                                                    <th
                                                        class="w-56 bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                                        Created At
                                                    </th>
                                                    <td class="px-4 py-3 text-sm text-gray-900">
                                                        {{ $taskList->created_at ? $taskList->created_at->format('D, M j, Y - g:i A') : 'N/A' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                                        Due Date
                                                    </th>
                                                    <td class="px-4 py-3 text-sm">
                                                        <span
                                                            class="{{ $taskList->due_date && now()->greaterThan(\Carbon\Carbon::parse($taskList->due_date)) ? 'text-red-600 font-medium' : 'text-green-600 font-medium' }}">
                                                            {{ $taskList->due_date ? \Carbon\Carbon::parse($taskList->due_date)->format('D, M j, Y - g:i A') : 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                                        Status
                                                    </th>
                                                    <td class="px-4 py-3">

                                                        @switch($taskList->status)
                                                            @case('pending')
                                                                <span
                                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                    Pending
                                                                </span>
                                                            @break

                                                            @case('in_progress')
                                                                <span
                                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                    In Progress
                                                                </span>
                                                            @break

                                                            @case('complete_intimation')
                                                                <span
                                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                                    Requested For Complete
                                                                </span>
                                                            @break

                                                            @case('completed')
                                                                <span
                                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                    Completed
                                                                </span>
                                                            @break

                                                            @default
                                                                <span
                                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                    {{ ucfirst($taskList->status) }}
                                                                </span>
                                                        @endswitch

                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                                        Category
                                                    </th>
                                                    <td class="px-4 py-3 text-sm text-gray-900">
                                                        {{ $taskList->category ? ucfirst($taskList->category->name) : 'N/A' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                                        Priority
                                                    </th>
                                                    <td class="px-4 py-3 text-sm text-gray-900">
                                                        {{ ucfirst($taskList->priority ?? 'N/A') }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700 align-top">
                                                        Reminder
                                                    </th>
                                                    <td class="px-4 py-3 text-sm text-gray-900">

                                                        @if ($taskList->reminders && $taskList->reminders->count())

                                                            <div class="space-y-2">

                                                                @foreach ($taskList->reminders as $reminder)
                                                                    <div class="bg-gray-50 rounded px-3 py-2">

                                                                        <strong>
                                                                            {{ $reminder->user->first_name ?? 'Unknown User' }}
                                                                        </strong>

                                                                        <br>

                                                                        {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('d-m-Y h:i a') }}

                                                                    </div>
                                                                @endforeach

                                                            </div>
                                                        @else
                                                            N/A

                                                        @endif

                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700 align-top">
                                                        Description
                                                    </th>
                                                    <td class="px-4 py-3 text-sm text-gray-900">
                                                        {{ $taskList->description ?? 'N/A' }}
                                                    </td>
                                                </tr>

                                            </tbody>

                                        </table>
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
