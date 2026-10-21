<div class="relative z-50 transform-gpu" aria-labelledby="slide-over-title" role="dialog" aria-modal="true"
    x-data="{ show: @entangle('isOpen') }" x-show="show" x-cloak wire:key="task-view-modal-{{ $taskId }}">

    <!-- Backdrop Overlay -->
    <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" inert></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-0 xs:pl-4 sm:pl-10">

                <!-- Slide-Over Content Box -->
                <div x-show="show"
                    x-transition:enter="transform transition ease-out duration-200 sm:duration-300 transform-gpu"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-150 sm:duration-200 transform-gpu"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto relative w-screen max-w-full xs:max-w-md sm:max-w-xl lg:max-w-2xl xl:max-w-4xl 2xl:max-w-5xl 3xl:max-w-6xl">

                    <div
                        class="flex h-full flex-col overflow-y-auto bg-white dark:bg-slate-900 py-4 sm:py-6 shadow-2xl custom-scrollbar border-l border-slate-200 dark:border-slate-800">

                        <!-- Panel Header -->
                        <div
                            class="px-3 xs:px-4 sm:px-6 flex items-center justify-between pb-3 sm:pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <button type="button" wire:click="close"
                                    class="p-1.5 sm:p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer shrink-0">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                    </svg>
                                </button>
                                <h1 class="text-base xs:text-lg sm:text-xl lg:text-2xl font-bold text-slate-900 dark:text-white tracking-tight"
                                    id="slide-over-title">
                                    Task Details
                                </h1>
                            </div>
                        </div>

                        <!-- Panel Body -->
                        <div class="relative px-3 xs:px-4 sm:px-6 py-4 sm:py-6 overflow-y-auto">
                            @if ($taskList)
                                <div
                                    class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">

                                    <div
                                        class="bg-slate-50 dark:bg-slate-800/50 px-3.5 xs:px-4 sm:px-5 py-3 border-b border-slate-200/80 dark:border-slate-800">
                                        <h3
                                            class="font-bold text-sm xs:text-base sm:text-lg text-slate-800 dark:text-white">
                                            Task Information
                                        </h3>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                                                <tr>
                                                    <th
                                                        class="w-32 xs:w-40 sm:w-56 bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 shrink-0">
                                                        Created At
                                                    </th>
                                                    <td
                                                        class="px-3 xs:px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-900 dark:text-slate-200">
                                                        {{ $taskList->created_at ? $taskList->created_at->format('D, M j, Y - g:i A') : 'N/A' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                        Due Date
                                                    </th>
                                                    <td class="px-3 xs:px-4 py-2.5 sm:py-3 text-xs sm:text-sm">
                                                        <span
                                                            class="{{ $taskList->due_date && now()->greaterThan(\Carbon\Carbon::parse($taskList->due_date)) ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-emerald-600 dark:text-emerald-400 font-semibold' }}">
                                                            {{ $taskList->due_date ? \Carbon\Carbon::parse($taskList->due_date)->format('D, M j, Y - g:i A') : 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                        Status
                                                    </th>
                                                    <td class="px-3 xs:px-4 py-2.5 sm:py-3">
                                                        @switch($taskList->status)
                                                            @case('pending')
                                                                <span
                                                                    class="px-2.5 py-0.5 sm:px-3 sm:py-1 text-[11px] sm:text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400">
                                                                    Pending
                                                                </span>
                                                            @break

                                                            @case('in_progress')
                                                                <span
                                                                    class="px-2.5 py-0.5 sm:px-3 sm:py-1 text-[11px] sm:text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-400">
                                                                    In Progress
                                                                </span>
                                                            @break

                                                            @case('complete_intimation')
                                                                <span
                                                                    class="px-2.5 py-0.5 sm:px-3 sm:py-1 text-[11px] sm:text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-400">
                                                                    Requested For Complete
                                                                </span>
                                                            @break

                                                            @case('completed')
                                                                <span
                                                                    class="px-2.5 py-0.5 sm:px-3 sm:py-1 text-[11px] sm:text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400">
                                                                    Completed
                                                                </span>
                                                            @break

                                                            @default
                                                                <span
                                                                    class="px-2.5 py-0.5 sm:px-3 sm:py-1 text-[11px] sm:text-xs font-semibold rounded-full bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                                                    {{ ucfirst($taskList->status) }}
                                                                </span>
                                                        @endswitch
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                        Category
                                                    </th>
                                                    <td
                                                        class="px-3 xs:px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-900 dark:text-slate-200">
                                                        {{ $taskList->category ? ucfirst($taskList->category->name) : 'N/A' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                        Priority
                                                    </th>
                                                    <td
                                                        class="px-3 xs:px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-900 dark:text-slate-200 capitalize">
                                                        {{ $taskList->priority ?? 'N/A' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th
                                                        class="bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 align-top">
                                                        Reminder
                                                    </th>
                                                    <td
                                                        class="px-3 xs:px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-900 dark:text-slate-200">
                                                        @if ($taskList->reminders && $taskList->reminders->count())
                                                            <div class="space-y-1.5 sm:space-y-2">
                                                                @foreach ($taskList->reminders as $reminder)
                                                                    <div
                                                                        class="bg-slate-100 dark:bg-slate-800 rounded-lg px-2.5 py-1.5 sm:px-3 sm:py-2">
                                                                        <strong
                                                                            class="font-bold text-slate-900 dark:text-white">
                                                                            {{ $reminder->user->first_name ?? 'Unknown User' }}
                                                                        </strong>
                                                                        <br>
                                                                        <span
                                                                            class="text-slate-500 dark:text-slate-400">
                                                                            {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('d-m-Y h:i a') }}
                                                                        </span>
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
                                                        class="bg-slate-50/70 dark:bg-slate-800/30 px-3 xs:px-4 py-2.5 sm:py-3 text-left text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 align-top">
                                                        Description
                                                    </th>
                                                    <td
                                                        class="px-3 xs:px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-900 dark:text-slate-200 leading-relaxed">
                                                        {{ $taskList->description ?? 'N/A' }}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Activity Log Section -->
                                <div class="mt-5 sm:mt-6">
                                    <div class="flex items-center mb-3 sm:mb-4">
                                        <h2
                                            class="text-base xs:text-lg sm:text-xl font-bold text-slate-900 dark:text-white">
                                            Task Activity Log
                                        </h2>
                                    </div>

                                    <div class="space-y-3 sm:space-y-4">
                                        @forelse ($taskUpdates as $update)
                                            <div class="bg-slate-50 dark:bg-slate-800/60 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-2xs"
                                                wire:key="task-update-{{ $update->id }}-{{ $update->status }}">
                                                <div class="flex items-start justify-between gap-2.5 xs:gap-3 sm:gap-4">

                                                    <div class="flex items-start gap-2.5 xs:gap-3 min-w-0">
                                                        <div
                                                            class="w-8 h-8 sm:w-10 sm:h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-xs sm:text-sm font-bold uppercase shrink-0 shadow-xs">
                                                            {{ strtoupper(substr($update->user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($update->user->last_name ?? '', 0, 1)) }}
                                                        </div>

                                                        <div class="min-w-0">
                                                            <p
                                                                class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                                                                {{ $update->user->first_name ?? 'Unknown' }}
                                                                {{ $update->user->last_name ?? '' }}
                                                            </p>
                                                            <p
                                                                class="text-[10px] xs:text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                                                @if ($update->created_at)
                                                                    {{ \Carbon\Carbon::parse($update->created_at)->diffForHumans() }}
                                                                    ({{ \Carbon\Carbon::parse($update->created_at)->format('D, M j, Y - g:i A') }})
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="shrink-0">
                                                        <span
                                                            class="inline-block px-2.5 py-0.5 sm:px-3 sm:py-1 text-[10px] xs:text-xs font-semibold rounded-full
                                                            @if ($update->status === 'pending') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400
                                                            @elseif ($update->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-400
                                                            @elseif ($update->status === 'complete_intimation') bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-400
                                                            @elseif ($update->status === 'completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400
                                                            @else bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 @endif">
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

                                                <div class="mt-2.5 sm:mt-3 pl-10 sm:pl-13">
                                                    <p
                                                        class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                                        {{ $update->comment }}
                                                    </p>
                                                </div>
                                            </div>
                                            @empty
                                                <div
                                                    class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl text-center border border-slate-200/60 dark:border-slate-800">
                                                    <p
                                                        class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">
                                                        No activity found.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl text-center border border-slate-200/60 dark:border-slate-800">
                                        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Task
                                            data not found.</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
