<div
    class="min-h-screen bg-slate-950/5 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">
    <!-- Main content container with appropriate padding and spacing -->
    <main class="scrollcontainer md:ml-16 px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16 transition-all duration-300">

        <div class="mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/80 backdrop-blur-md border border-gray-200/80 text-sm font-semibold text-gray-700 hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-sm transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25a7.5 7.5 0 0115 0" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900">
                            Category Report
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-normal">
                            Category-wise task status overview and analytics performance
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Report Grid Section -->
        <div class="mb-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach ($categories as $category)
                    @php
                        $pendingTotal = $category['pending']['total'] ?? 0;
                        $pendingCompleted = $category['pending']['completed'] ?? 0;
                        $progressCompleted = $category['in_progress']['completed'] ?? 0;
                        $completedCompleted = $category['completed']['completed'] ?? 0;
                        $totalTasks = max(
                            $pendingTotal,
                            $category['in_progress']['total'] ?? 0,
                            $category['completed']['total'] ?? 0,
                        );
                    @endphp

                    <div
                        class="group rounded-3xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900 p-6 shadow-xl shadow-slate-950/[0.02] transition-all duration-300 hover:border-blue-500/40 dark:hover:border-blue-500/40 hover:shadow-2xl hover:shadow-blue-500/[0.08] hover:-translate-y-1 relative overflow-hidden flex flex-col justify-between">

                        {{-- Top Accent Highlight Glow --}}
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <div>
                            <div class="relative z-10 mb-6 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div
                                        class="mb-3.5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-100/80 dark:border-blue-900/50 shadow-inner ring-4 ring-blue-50/50 dark:ring-blue-950/20">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M2 4.5A2.5 2.5 0 0 1 4.5 2h11A2.5 2.5 0 0 1 18 4.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 2 15.5v-11Zm3 1.75A1.25 1.25 0 1 0 5 8.75a1.25 1.25 0 0 0 0-2.5Zm4-.25a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9Zm0 4a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9Zm0 4a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9ZM5 10.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Z" />
                                        </svg>
                                    </div>

                                    <h4
                                        class="truncate text-base font-bold text-slate-900 dark:text-white tracking-tight">
                                        {{ ucwords($category['title']) }}
                                    </h4>

                                    @if (!empty($category['creator_name']))
                                        <p class="mt-0.5 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                            By: <span
                                                class="font-semibold text-slate-700 dark:text-slate-300">{{ $category['creator_name'] }}</span>
                                        </p>
                                    @endif

                                    <p
                                        class="mt-1 text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                        Total Scope: <span
                                            class="text-slate-700 dark:text-slate-300 font-extrabold">{{ $totalTasks }}
                                            Tasks</span>
                                    </p>
                                </div>
                            </div>

                            <div class="relative z-10 space-y-3 mb-2">
                                @foreach (['pending' => '#f43f5e', 'in_progress' => '#f59e0b', 'completed' => '#10b981'] as $status => $color)
                                    @php
                                        $circumference = 565.48;
                                        $statusData = $category[$status];
                                        $percentage = $statusData['percentage'];
                                        $completed = $statusData['completed'];
                                        $total = $statusData['total'];
                                        $offset = $circumference - ($circumference * $percentage) / 100;

                                        $labelName = match ($status) {
                                            'pending' => 'Pending',
                                            'in_progress' => 'In Progress',
                                            'completed' => 'Completed',
                                            default => ucwords($status),
                                        };

                                        // FIXED: Passed as pure positional array to eliminate named unpacking errors
                                        $categoryStatusUrl = route('tasks.category', [$category['title'], $status]);
                                    @endphp

                                    <a href="{{ $categoryStatusUrl }}" wire:navigate
                                        class="block rounded-2xl border border-slate-100 dark:border-slate-800/60 bg-slate-50/80 dark:bg-slate-950/40 p-3.5 transition-all duration-200 hover:bg-slate-100/80 dark:hover:bg-slate-950/80 hover:border-blue-300 dark:hover:border-blue-700 hover:scale-[1.01] cursor-pointer">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-10 w-10 shrink-0 relative flex items-center justify-center">
                                                    <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        style="transform: rotate(-90deg)">
                                                        <circle r="90" cx="100" cy="100" fill="transparent"
                                                            stroke="currentColor"
                                                            class="text-slate-200 dark:text-slate-800" stroke-width="22"
                                                            stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                                                        <circle r="90" cx="100" cy="100"
                                                            stroke="{{ $color }}" stroke-width="22"
                                                            stroke-linecap="round"
                                                            stroke-dashoffset="{{ $offset }}" fill="transparent"
                                                            stroke-dasharray="565.48"></circle>
                                                    </svg>
                                                    <span
                                                        class="absolute text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                                        {{ (int) $percentage }}%
                                                    </span>
                                                </div>

                                                <div class="overflow-hidden">
                                                    <p
                                                        class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-tight truncate">
                                                        {{ $labelName }}
                                                    </p>
                                                    <p
                                                        class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 mt-0.5">
                                                        <span
                                                            class="text-slate-700 dark:text-slate-300 font-bold">{{ $completed }}</span>
                                                        / {{ $total }} Tasks
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200/80 dark:bg-slate-800">
                                            <div class="h-full rounded-full transition-all duration-500"
                                                style="width: {{ $percentage }}%; background-color: {{ $color }}">
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
