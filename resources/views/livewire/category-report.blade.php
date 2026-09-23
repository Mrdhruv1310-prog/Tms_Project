<div
    class="min-h-screen bg-slate-950/5 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">
    <!-- Main content container with appropriate padding and spacing -->
    <main class="scrollcontainer md:ml-16 px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16 transition-all duration-300">

        {{-- Executive Header / Background --}}
        <div
            class="mb-8 sm:mb-10 overflow-hidden rounded-3xl border border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/85 backdrop-blur-2xl shadow-xl shadow-slate-950/[0.03] p-6 sm:p-8 relative">
            <div
                class="absolute -top-20 -left-20 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-2">
                    <div
                        class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">
                        <button type="button" onclick="window.history.back()"
                            class="group hover:text-blue-700 dark:hover:text-blue-300 transition-all cursor-pointer flex items-center gap-1.5 shrink-0 bg-transparent border-none p-0 text-slate-500 dark:text-slate-400 font-bold">
                            <svg class="h-4 w-4 transform transition-transform group-hover:-translate-x-0.5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back to Dashboard
                        </button>
                    </div>

                    <div class="space-y-1">
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                            Category Report
                        </h1>
                        <p class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">
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
                                    @endphp

                                    <div
                                        class="rounded-2xl border border-slate-100 dark:border-slate-800/60 bg-slate-50/80 dark:bg-slate-950/40 p-3.5 transition-all duration-200 hover:bg-slate-100/80 dark:hover:bg-slate-950/80 hover:border-slate-200 dark:hover:border-slate-700">
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
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
