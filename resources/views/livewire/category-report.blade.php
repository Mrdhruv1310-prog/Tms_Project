<div class="min-h-screen bg-gradient-to-br from-[#F8FAFC] via-[#F1F5F9] to-[#E2E8F0] antialiased selection:bg-blue-600 selection:text-white text-slate-700">
    <main class="scrollcontainer px-4 sm:px-6 md:px-8 pb-20 pt-20 sm:pt-24 md:ml-16 max-w-[1750px] mx-auto transition-all duration-300">

        {{-- Executive Header / Background --}}
        <div class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
            <div class="absolute -top-16 -left-16 w-72 sm:w-80 h-72 sm:h-80 bg-gradient-to-br from-blue-500/10 to-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-medium uppercase tracking-widest text-slate-400 overflow-x-auto py-1">
                        <button type="button" onclick="window.history.back()" class="hover:text-blue-600 transition cursor-pointer flex items-center gap-1 shrink-0 bg-transparent border-none p-0 text-slate-400 font-medium">
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                    </div>

                    <h1 class="text-base sm:text-lg md:text-xl font-medium tracking-tight text-slate-700">
                        Category Report
                    </h1>

                    <p class="text-[11px] sm:text-xs font-normal text-slate-500">
                        Category-wise task status overview
                    </p>
                </div>

            </div>
        </div>

        <!-- Category Report Grid Section -->
        <div class="mb-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach ($categories as $category)
                    @php
                        $pendingTotal = $category['pending']['total'] ?? 0;
                        $pendingCompleted = $category['pending']['completed'] ?? 0;
                        $progressCompleted = $category['in_progress']['completed'] ?? 0;
                        $completedCompleted = $category['completed']['completed'] ?? 0;
                        $totalTasks = max($pendingTotal, $category['in_progress']['total'] ?? 0, $category['completed']['total'] ?? 0);
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm transition-all duration-300 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1">

                        <div class="relative z-10 mb-6 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100 shadow-2xs">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path d="M2 4.5A2.5 2.5 0 0 1 4.5 2h11A2.5 2.5 0 0 1 18 4.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 2 15.5v-11Zm3 1.75A1.25 1.25 0 1 0 5 8.75a1.25 1.25 0 0 0 0-2.5Zm4-.25a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9Zm0 4a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9Zm0 4a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9ZM5 10.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Z" />
                                    </svg>
                                </div>

                                <h4 class="truncate text-sm sm:text-base font-medium text-slate-700">
                                    {{ ucwords($category['title']) }}
                                </h4>
                                <p class="mt-1 text-xs font-normal text-slate-500">
                                    Total Tasks: {{ $totalTasks }}
                                </p>
                            </div>
                        </div>

                        <div class="relative z-10 space-y-4">
                            @foreach (['pending' => '#fb7185', 'in_progress' => '#fbbf24', 'completed' => '#22c55e'] as $status => $color)
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

                                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3 transition hover:bg-white hover:border-blue-300">
                                    <div class="mb-3 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-11 w-11 shrink-0">
                                                <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                    xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                                    <circle r="90" cx="100" cy="100" fill="transparent"
                                                        stroke="#e2e8f0" stroke-width="18" stroke-dasharray="565.48"
                                                        stroke-dashoffset="0"></circle>
                                                    <circle r="90" cx="100" cy="100" stroke="{{ $color }}"
                                                        stroke-width="18" stroke-linecap="round"
                                                        stroke-dashoffset="{{ $offset }}" fill="transparent"
                                                        stroke-dasharray="565.48"></circle>
                                                    <text x="52px" y="112px" fill="#334155" font-size="44px"
                                                        font-weight="bold"
                                                        style="transform: rotate(90deg) translate(0px, -196px)">
                                                        {{ (int) $percentage }}%
                                                    </text>
                                                </svg>
                                            </div>

                                            <div>
                                                <p class="text-xs sm:text-sm font-medium text-slate-700">
                                                    {{ $labelName }}
                                                </p>
                                                <p class="text-[11px] font-normal text-slate-500">
                                                    {{ $completed }}/{{ $total }} Tasks
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full transition-all duration-500"
                                            style="width: {{ $percentage }}%; background-color: {{ $color }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
