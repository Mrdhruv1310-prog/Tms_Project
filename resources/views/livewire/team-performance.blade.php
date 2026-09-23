<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">
    <main class="scrollcontainer p-4 md:ml-16 h-auto pt-20 pb-16">

        <!-- Top Bar with Back Button and Dynamic Title -->
        <div
            class="relative flex items-center justify-between mb-8 sm:mb-10 pb-5 border-b border-slate-200/80 dark:border-slate-800/80">
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

            <!-- Dynamic Title based on User Role -->
            <h3
                class="absolute left-1/2 -translate-x-1/2 text-sm sm:text-lg lg:text-xl font-bold tracking-tight text-slate-900 dark:text-white text-center truncate max-w-[45%] sm:max-w-[65%]">
                @php
                    $role = Auth::user()->role ?? 'user';
                @endphp

                @if ($role === 'super-admin')
                    All Company Tasks Performance
                @elseif($role === 'admin')
                    Team Performance
                @else
                    Assigned Tasks Performance
                @endif
            </h3>
        </div>

        <!-- Team Performance Section -->
        <div class="mb-6">
            @if (!empty($team) && count($team) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($team as $member)
                        <div
                            class="group relative bg-white dark:bg-slate-900/90 backdrop-blur-xl p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-xl shadow-slate-950/[0.03] hover:border-blue-500/40 dark:hover:border-blue-500/40 hover:shadow-2xl hover:shadow-blue-500/[0.08] transition-all duration-300 flex flex-col justify-between overflow-hidden">

                            <!-- Top Gradient Accent Line on Hover -->
                            <div
                                class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <!-- Member Header with Initials Badge -->
                            <div class="flex items-center gap-3.5 mb-6">
                                <div
                                    class="w-11 h-11 flex items-center justify-center text-white text-xs font-bold uppercase rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-md shadow-blue-500/25 ring-4 ring-blue-50 dark:ring-blue-950/40">
                                    {{ strtoupper(substr($member['name'], 0, 2)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div
                                        class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight truncate">
                                        {{ ucwords($member['name']) }}
                                    </div>
                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-0.5">
                                        Member Analytics
                                    </p>
                                </div>
                            </div>

                            <!-- Progress bars for Pending, In Progress, Completed -->
                            <div class="space-y-3">
                                @foreach (['pending' => '#f87171', 'in_progress' => '#fbbf24', 'completed' => '#67d697'] as $status => $color)
                                    @php
                                        $statusCount = $member[$status] ?? 0;
                                        $total = $member['total'] ?? 0;
                                        $percentage = $total > 0 ? ($statusCount / $total) * 100 : 0;
                                        $circumference = 565.48;
                                        $offset = $circumference - ($circumference * $percentage) / 100;
                                    @endphp

                                    <div
                                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/80 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-950/80 transition-colors">

                                        <!-- SVG Circular Progress Bar -->
                                        <div class="w-10 h-10 relative flex items-center justify-center shrink-0">
                                            <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                                <circle r="90" cx="100" cy="100" fill="transparent"
                                                    stroke="#e2e8f0" class="dark:stroke-slate-800" stroke-width="22px"
                                                    stroke-dasharray="565.48px" stroke-dashoffset="0"></circle>
                                                <circle r="90" cx="100" cy="100"
                                                    stroke="{{ $color }}" stroke-width="22px"
                                                    stroke-linecap="round" stroke-dashoffset="{{ $offset }}"
                                                    fill="transparent" stroke-dasharray="565.48px"></circle>
                                            </svg>
                                            <span
                                                class="absolute text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                                {{ (int) $percentage }}%
                                            </span>
                                        </div>

                                        <!-- Task Completion Status -->
                                        <div class="ml-3 text-right overflow-hidden">
                                            <p
                                                class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </p>
                                            <p class="text-xs font-bold text-slate-900 dark:text-white mt-0.5">
                                                <span
                                                    class="text-blue-600 dark:text-blue-400 font-extrabold">{{ $statusCount }}</span>
                                                <span class="text-slate-400 dark:text-slate-600">/</span>
                                                {{ $total }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State Message when no performance records found -->
                <div
                    class="flex flex-col items-center justify-center p-12 lg:p-20 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-950/[0.03] text-center mt-4">
                    <div
                        class="w-16 h-16 flex items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 mb-4 shadow-inner ring-8 ring-blue-50/50 dark:ring-blue-950/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">No Performance
                        Data
                        Found</h3>
                    <p class="text-xs sm:text-sm font-medium text-slate-400 dark:text-slate-500 mt-1.5 max-w-sm">
                        There are no tasks or members available to display performance right now.
                    </p>
                </div>
            @endif
        </div>
    </main>
</div>
