<div class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-data="{ show: @entangle('isOpen') }"
    x-show="show" x-cloak>

    <!-- Background backdrop -->
    <div x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" inert></div>

    <!-- Drawer container -->
    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-3 sm:pl-10">
                <!-- Slide-over panel with transitions -->
                <div x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto relative w-screen max-w-full sm:max-w-xl lg:max-w-3xl 2xl:max-w-4xl">

                    <!-- Drawer content -->
                    <div class="flex h-full flex-col overflow-y-auto bg-white dark:bg-gray-900 py-3 sm:py-6 shadow-2xl">
                        <!-- Drawer Header -->
                        <div
                            class="flex items-center gap-2.5 px-3.5 sm:px-6 pb-3 sm:pb-4 border-b border-slate-100 dark:border-gray-800">
                            <button type="button" @click="show = false"
                                class="p-2 -ml-2 rounded-xl text-slate-700 dark:text-gray-200 hover:bg-slate-100 dark:hover:bg-gray-800 focus:outline-none transition-all cursor-pointer active:scale-90">
                                <span class="sr-only">Close panel</span>
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </button>
                            <h1 class="text-base sm:text-xl lg:text-2xl font-bold tracking-tight text-slate-900 dark:text-white truncate"
                                id="slide-over-title">
                                Task Details
                            </h1>
                        </div>

                        <!-- Main Content -->
                        <div class="relative px-3.5 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6 overflow-y-auto">
                            <div
                                class="bg-slate-50/80 dark:bg-gray-800/50 border border-slate-200/80 dark:border-gray-800 p-3 sm:p-5 rounded-2xl shadow-xs space-y-3.5 sm:space-y-4">

                                <!-- Task Title -->
                                <h2
                                    class="text-sm xs:text-base sm:text-lg lg:text-xl font-bold text-slate-900 dark:text-white break-words leading-snug">
                                    {{ $taskList->title ?? 'N/A' }}
                                </h2>

                                <!-- Assigned To & Assigned By Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                                    <!-- Assigned To Column -->
                                    <div
                                        class="flex flex-col gap-2.5 bg-white dark:bg-gray-800/80 p-3 sm:p-4 rounded-xl border border-slate-200/60 dark:border-gray-700/60 shadow-2xs">
                                        <p
                                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-400">
                                            Assigned To</p>
                                        <div class="space-y-2.5">
                                            @if ($taskList && $taskList->assignedUsers->isNotEmpty())
                                                @foreach ($taskList->assignedUsers as $user)
                                                    <div class="flex items-center gap-2.5 min-w-0"
                                                        wire:key="{{ $user->id }}">
                                                        <div
                                                            class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold text-xs shadow-xs">
                                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p
                                                                class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                                {{ $user->first_name }} {{ $user->last_name }}</p>
                                                            <p
                                                                class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 truncate">
                                                                {{ $user->email }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-xs text-slate-500 italic">No users assigned</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Assigned By Column -->
                                    <div
                                        class="flex flex-col gap-2.5 bg-white dark:bg-gray-800/80 p-3 sm:p-4 rounded-xl border border-slate-200/60 dark:border-gray-700/60 shadow-2xs">
                                        <p
                                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-gray-400">
                                            Assigned By</p>
                                        <div class="space-y-2.5">
                                            @if ($taskList && $taskList->creator)
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div
                                                        class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-xs shadow-xs">
                                                        {{ strtoupper(substr($taskList->creator->first_name, 0, 1)) }}{{ strtoupper(substr($taskList->creator->last_name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p
                                                            class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ $taskList->creator->first_name }}
                                                            {{ $taskList->creator->last_name }}</p>
                                                        <p
                                                            class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 truncate">
                                                            {{ $taskList->creator->email }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-xs text-slate-500 italic">No assigned by information
                                                    available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Dates Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                                    <div
                                        class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60">
                                        <span
                                            class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider">Created
                                            At</span>
                                        <span
                                            class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5 block truncate">
                                            {{ $taskList ? $taskList->created_at->format('D, M j, Y - g:i A') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div
                                        class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60">
                                        <span
                                            class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider">Due
                                            Date</span>
                                        <span
                                            class="font-semibold mt-0.5 block truncate {{ isset($taskList->due_date) && now()->greaterThan(\Carbon\Carbon::parse($taskList->due_date)) ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ $taskList && $taskList->due_date ? \Carbon\Carbon::parse($taskList->due_date)->format('D, M j, Y - g:i A') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Status, Category & Priority Meta -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <!-- Status -->
                                    <div
                                        class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60 flex flex-col justify-between">
                                        <span
                                            class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider mb-1.5">Status</span>
                                        <div>
                                            @if ($taskList)
                                                @switch($taskList->status)
                                                    @case('pending')
                                                        <span
                                                            class="px-2.5 py-1 text-xs font-bold text-amber-800 bg-amber-100 rounded-lg inline-block">
                                                            {{ ucfirst($taskList->status) }}
                                                        </span>
                                                    @break

                                                    @case('in_progress')
                                                        <span
                                                            class="px-2.5 py-1 text-xs font-bold text-blue-800 bg-blue-100 rounded-lg inline-block">
                                                            {{ ucfirst($taskList->status) }}
                                                        </span>
                                                    @break

                                                    @case('completed')
                                                        <span
                                                            class="px-2.5 py-1 text-xs font-bold text-emerald-800 bg-emerald-100 rounded-lg inline-block">
                                                            {{ ucfirst($taskList->status) }}
                                                        </span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="px-2.5 py-1 text-xs font-bold text-slate-800 bg-slate-100 rounded-lg inline-block">
                                                            {{ ucfirst($taskList->status) }}
                                                        </span>
                                                @endswitch
                                            @else
                                                <span
                                                    class="px-2.5 py-1 text-xs font-bold text-slate-800 bg-slate-100 rounded-lg inline-block">N/A</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div
                                        class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60 flex flex-col justify-between">
                                        <span
                                            class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider mb-1.5">Category</span>
                                        <span
                                            class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate">
                                            {{ $taskList && $taskList->category ? ucfirst($taskList->category->name) : 'N/A' }}
                                        </span>
                                    </div>

                                    <!-- Priority -->
                                    <div
                                        class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60 flex flex-col justify-between">
                                        <span
                                            class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider mb-1.5">Priority</span>
                                        <div>
                                            @if ($taskList)
                                                @switch($taskList->priority)
                                                    @case('low')
                                                        <span
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-emerald-800 bg-emerald-50 rounded-lg">
                                                            <svg width="14px" height="14px" viewBox="0 0 24 24"
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
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-amber-800 bg-amber-50 rounded-lg">
                                                            <svg width="14px" height="14px" viewBox="0 0 24 24"
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
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-rose-800 bg-rose-50 rounded-lg">
                                                            <svg width="14px" height="14px" viewBox="0 0 24 24"
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
                                                <span class="px-2 py-1 text-xs font-bold text-slate-800">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Reminder -->
                                <div
                                    class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60">
                                    <span
                                        class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider mb-1">Reminder</span>
                                    @if ($taskList && $taskList->reminders && $taskList->reminders->count())
                                        <ul class="list-disc ml-4 space-y-1 text-xs text-slate-700 dark:text-gray-300">
                                            @foreach ($taskList->reminders as $reminder)
                                                <li>
                                                    <span
                                                        class="font-semibold">{{ $reminder->user->first_name ?? 'Unknown User' }}</span>:
                                                    {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('d-m-Y h:i a') }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-xs text-slate-500 italic">No reminders set</span>
                                    @endif
                                </div>

                                <!-- Description -->
                                <div
                                    class="bg-white dark:bg-gray-800/80 p-3 rounded-xl border border-slate-200/60 dark:border-gray-700/60">
                                    <span
                                        class="font-bold text-slate-400 dark:text-gray-400 block text-[10px] uppercase tracking-wider mb-1">Description</span>
                                    <p
                                        class="text-xs sm:text-sm text-slate-700 dark:text-gray-300 whitespace-pre-line leading-relaxed break-words">
                                        {{ $taskList && $taskList->description ? $taskList->description : 'No description provided' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Task Activity Log Section -->
                            <div class="mt-5 sm:mt-8 space-y-3 sm:space-y-4">
                                <div
                                    class="flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-gray-800">
                                    <svg class="w-5 h-5 text-slate-700 dark:text-gray-300 shrink-0"
                                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11 6L21 6.00072M11 12L21 12.0007M11 18L21 18.0007M3 11.9444L4.53846 13.5L8 10M3 5.94444L4.53846 7.5L8 4M4.5 18H4.51M5 18C5 18.2761 4.77614 18.5 4.5 18.5C4.22386 18.5 4 18.2761 4 18C4 17.7239 4.22386 17.5 4.5 17.5C4.77614 17.5 5 17.7239 5 18Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <h2
                                        class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                                        Task Activity Log
                                    </h2>
                                </div>

                                <div class="space-y-3">
                                    @forelse ($taskUpdates as $update)
                                        <div class="bg-slate-50/80 dark:bg-gray-800/50 border border-slate-200/80 dark:border-gray-800 p-3 sm:p-4 rounded-2xl shadow-2xs space-y-2.5"
                                            wire:key="{{ $update->id }}">

                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div
                                                        class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-xs shadow-xs">
                                                        {{ strtoupper(substr($update->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($update->user->last_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p
                                                            class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ $update->user->first_name ?? 'Unknown' }}
                                                            {{ $update->user->last_name ?? '' }}
                                                        </p>
                                                        <p
                                                            class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 truncate">
                                                            {{ $update->created_at->diffForHumans() }}
                                                            ({{ $update->updated_at->format('D, M j, Y - g:i A') }})
                                                        </p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <span
                                                        class="inline-block px-2.5 py-1 text-[11px] font-bold rounded-lg
                                                        @if ($update->status === 'pending') bg-amber-100 text-amber-800
                                                        @elseif($update->status === 'in_progress') bg-blue-100 text-blue-800
                                                        @elseif($update->status === 'complete_intimation') bg-orange-100 text-orange-800
                                                        @elseif($update->status === 'completed') bg-emerald-100 text-emerald-800
                                                        @else bg-slate-100 text-slate-800 @endif">
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

                                            @if (!empty($update->comment))
                                                <div class="pl-0 sm:pl-11 pt-1">
                                                    <p
                                                        class="text-xs sm:text-sm text-slate-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-2.5 rounded-xl border border-slate-200/60 dark:border-gray-700/60 break-words">
                                                        {{ $update->comment }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        @empty
                                            <div
                                                class="text-center py-6 text-xs text-slate-500 italic bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                                No activity logs available for this task.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
