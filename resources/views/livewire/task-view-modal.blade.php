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
                                    {{ optional($taskList)->title ?? 'N/A' }}
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
                                                        wire:key="assigned-user-{{ $user->id }}">
                                                        <div
                                                            class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold text-xs shadow-xs">
                                                            {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p
                                                                class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                                {{ $user->first_name ?? '' }}
                                                                {{ $user->last_name ?? '' }}</p>
                                                            <p
                                                                class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 truncate">
                                                                {{ $user->email ?? '' }}</p>
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
                                                        {{ strtoupper(substr(optional($taskList->creator)->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(optional($taskList->creator)->last_name ?? '', 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p
                                                            class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ optional($taskList->creator)->first_name ?? '' }}
                                                            {{ optional($taskList->creator)->last_name ?? '' }}</p>
                                                        <p
                                                            class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 truncate">
                                                            {{ optional($taskList->creator)->email ?? '' }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-xs text-slate-500 italic">No assigned by information
                                                    available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Task Transfer History Section -->
                                @if (!empty($taskTransfers) && count($taskTransfers) > 0)
                                    <div
                                        class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 p-3 sm:p-4 rounded-xl shadow-2xs space-y-2.5">
                                        <span
                                            class="font-bold text-amber-800 dark:text-amber-400 block text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            Task Transfer History
                                        </span>
                                        <div class="space-y-2">
                                            @foreach ($taskTransfers as $transfer)
                                                <div
                                                    class="bg-white dark:bg-gray-800 p-2.5 rounded-lg border border-amber-100 dark:border-amber-900/40 text-xs space-y-1">
                                                    <div
                                                        class="flex flex-wrap items-center justify-between text-slate-700 dark:text-slate-300 font-medium gap-1">
                                                        <span>
                                                            <strong
                                                                class="text-blue-600 dark:text-blue-400">{{ $transfer['from_name'] }}</strong>
                                                            ➔
                                                            <strong
                                                                class="text-emerald-600 dark:text-emerald-400">{{ $transfer['to_name'] }}</strong>
                                                        </span>
                                                        @if ($transfer['transferred_at'])
                                                            <span
                                                                class="text-[10px] text-slate-400">{{ $transfer['transferred_at'] }}</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-slate-600 dark:text-slate-400 italic">
                                                        "{{ $transfer['transfer_notes'] }}"</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

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
                                            @if ($taskList && $taskList->priority)
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-lg
                                                    @if ($taskList->priority === 'low') text-emerald-800 bg-emerald-50
                                                    @elseif($taskList->priority === 'medium') text-amber-800 bg-amber-50
                                                    @else text-rose-800 bg-rose-50 @endif">
                                                    {{ ucfirst($taskList->priority) }}
                                                </span>
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
                                                <li wire:key="reminder-item-{{ $reminder->id }}">
                                                    <span
                                                        class="font-semibold">{{ optional($reminder->user)->first_name ?? 'Unknown User' }}</span>:
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
                                    <h2
                                        class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                                        Task Activity Log
                                    </h2>
                                </div>

                                <div class="space-y-3">
                                    @forelse ($taskUpdates as $update)
                                        <div class="bg-slate-50/80 dark:bg-gray-800/50 border border-slate-200/80 dark:border-gray-800 p-3 sm:p-4 rounded-2xl shadow-2xs space-y-2.5"
                                            wire:key="task-update-{{ $update->id }}">

                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div
                                                        class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-xs shadow-xs">
                                                        {{ strtoupper(substr(optional($update->user)->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(optional($update->user)->last_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p
                                                            class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                            {{ optional($update->user)->first_name ?? 'Unknown' }}
                                                            {{ optional($update->user)->last_name ?? '' }}
                                                        </p>
                                                        <p
                                                            class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 truncate">
                                                            {{ $update->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <span
                                                        class="inline-block px-2.5 py-1 text-[11px] font-bold rounded-lg
                                                        @if ($update->status === 'pending') bg-amber-100 text-amber-800
                                                        @elseif($update->status === 'in_progress') bg-blue-100 text-blue-800
                                                        @elseif($update->status === 'completed') bg-emerald-100 text-emerald-800
                                                        @else bg-slate-100 text-slate-800 @endif">
                                                        {{ ucfirst($update->status) }}
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
