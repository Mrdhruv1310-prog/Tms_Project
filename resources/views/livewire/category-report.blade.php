<div class="min-h-screen bg-[#eef3fb] antialiased">
    <main class="scrollcontainer h-auto px-3 pb-16 pt-20 sm:px-4 md:ml-16 lg:px-6">
        <div class="relative mb-8 flex items-center justify-center">
            <button type="button"
                class="absolute left-0 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#2563eb] text-white shadow-md shadow-blue-200 transition hover:-translate-y-0.5 hover:bg-[#1d4ed8] focus:outline-none focus:ring-4 focus:ring-blue-200 sm:h-12 sm:w-12"
                @click="window.history.back()">
                <svg class="h-5 w-5 transform rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                </svg>
                <span class="sr-only">Go Back</span>
            </button>

            <div class="text-center">
                <h3 class="text-2xl font-black tracking-tight text-[#001b4d] sm:text-3xl">
                    Category Report
                </h3>
                <p class="mt-1 text-xs font-semibold text-slate-500 sm:text-sm">
                    Category-wise task status overview
                </p>
            </div>
        </div>

        <!-- Category Report Section -->
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

                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-100">

                        <div class="relative z-10 mb-6 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#2563eb] text-white shadow-md shadow-blue-200">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path d="M2 4.5A2.5 2.5 0 0 1 4.5 2h11A2.5 2.5 0 0 1 18 4.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 2 15.5v-11Zm3 1.75A1.25 1.25 0 1 0 5 8.75a1.25 1.25 0 0 0 0-2.5Zm4-.25a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9Zm0 4a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9Zm0 4a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5H9ZM5 10.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Z" />
                                    </svg>
                                </div>

                                <h4 class="truncate text-lg font-black text-[#001b4d]">
                                    {{ ucwords($category['title']) }}
                                </h4>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    Total Tasks: {{ $totalTasks }}
                                </p>
                            </div>

                            {{-- <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">
                                Report
                            </span> --}}
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

                                <div class="rounded-xl border border-slate-100 bg-[#f8fbff] p-3 transition group-hover:bg-white">
                                    <div class="mb-3 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-11 w-11 shrink-0">
                                                <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                    xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                                    <circle r="90" cx="100" cy="100" fill="transparent"
                                                        stroke="#e5e7eb" stroke-width="18" stroke-dasharray="565.48"
                                                        stroke-dashoffset="0"></circle>
                                                    <circle r="90" cx="100" cy="100" stroke="{{ $color }}"
                                                        stroke-width="18" stroke-linecap="round"
                                                        stroke-dashoffset="{{ $offset }}" fill="transparent"
                                                        stroke-dasharray="565.48"></circle>
                                                    <text x="52px" y="112px" fill="#001b4d" font-size="44px"
                                                        font-weight="bold"
                                                        style="transform: rotate(90deg) translate(0px, -196px)">
                                                        {{ (int) $percentage }}%
                                                    </text>
                                                </svg>
                                            </div>

                                            <div>
                                                <p class="text-sm font-black text-[#001b4d]">
                                                    {{ $labelName }}
                                                </p>
                                                <p class="text-xs font-semibold text-slate-500">
                                                    {{ $completed }}/{{ $total }} Tasks
                                                </p>
                                            </div>
                                        </div>

                                        {{-- <span class="text-sm font-black text-[#001b4d]">
                                            {{ (int) $percentage }}%
                                        </span> --}}
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
