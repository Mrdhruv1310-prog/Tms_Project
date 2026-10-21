<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden antialiased transform-gpu transition-all duration-150">
    <main
        class="scrollcontainer md:ml-16 px-2.5 xs:px-4 sm:px-6 lg:px-8 2xl:px-12 3xl:px-16 pt-14 sm:pt-20 pb-12 sm:pb-16 max-w-[2560px] mx-auto h-auto">

        <!-- Top Bar with Back Button and Dynamic Title -->
        <div
            class="relative flex flex-col sm:flex-row items-center justify-between gap-3 mb-6 sm:mb-10 pb-4 sm:pb-5 border-b border-slate-200/80 dark:border-slate-800/80">
            <div class="flex items-center justify-between w-full sm:w-auto">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 xs:px-3 sm:px-4 sm:py-2 rounded-xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-gray-200/80 dark:border-slate-800 text-xs sm:text-sm font-semibold text-gray-700 dark:text-slate-200 hover:text-[rgb(7,139,221)] dark:hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-xs transition-all duration-150 active:scale-95 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>

            <!-- Dynamic Title based on User Role -->
            <h3
                class="sm:absolute sm:left-1/2 sm:-translate-x-1/2 text-sm xs:text-base sm:text-lg lg:text-xl 2xl:text-2xl font-bold tracking-tight text-slate-900 dark:text-white text-center truncate max-w-full sm:max-w-[65%]">
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
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 3xl:grid-cols-6 gap-3.5 xs:gap-4 sm:gap-6">
                    @foreach ($team as $member)
                        <div
                            class="group relative bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl p-4 xs:p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800/80 shadow-md hover:border-blue-500/40 dark:hover:border-blue-500/40 hover:shadow-xl hover:shadow-blue-500/[0.08] transition-all duration-150 flex flex-col justify-between overflow-hidden">

                            <!-- Top Gradient Accent Line on Hover -->
                            <div
                                class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                            </div>

                            <!-- Member Header with Initials Badge -->
                            <div class="flex items-center gap-3 sm:gap-3.5 mb-4 sm:mb-6">
                                <div
                                    class="w-9 h-9 sm:w-11 sm:h-11 flex items-center justify-center text-white text-xs sm:text-sm font-bold uppercase rounded-xl sm:rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-md shadow-blue-500/25 ring-2 sm:ring-4 ring-blue-50 dark:ring-blue-950/40 shrink-0">
                                    {{ strtoupper(substr($member['name'], 0, 2)) }}
                                </div>
                                <div class="overflow-hidden min-w-0">
                                    <div
                                        class="text-xs xs:text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight truncate">
                                        {{ ucwords($member['name']) }}
                                    </div>
                                    <p
                                        class="text-[10px] xs:text-[11px] font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                        Member Analytics
                                    </p>
                                </div>
                            </div>

                            <!-- Progress bars for Pending, In Progress, Completed -->
                            <div class="space-y-2 sm:space-y-3">
                                @foreach (['pending' => '#f87171', 'in_progress' => '#fbbf24', 'completed' => '#67d697'] as $status => $color)
                                    @php
                                        $statusCount = $member[$status] ?? 0;
                                        $total = $member['total'] ?? 0;
                                        $percentage = $total > 0 ? ($statusCount / $total) * 100 : 0;
                                        $circumference = 565.48;
                                        $offset = $circumference - ($circumference * $percentage) / 100;

                                        // Direct Clean URL Generation
                                        $userStatusUrl = url("/tasks/user/{$status}");
                                    @endphp

                                    <a href="{{ $userStatusUrl }}" wire:navigate
                                        class="flex items-center justify-between p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-50/80 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/60 hover:bg-blue-50/60 dark:hover:bg-blue-950/40 hover:border-blue-300 dark:hover:border-blue-700 transition-all cursor-pointer block active:scale-[0.98]">

                                        <!-- SVG Circular Progress Bar -->
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 relative flex items-center justify-center shrink-0">
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
                                                class="absolute text-[9px] sm:text-[10px] font-bold text-slate-700 dark:text-slate-200">
                                                {{ (int) $percentage }}%
                                            </span>
                                        </div>

                                        <!-- Task Completion Status -->
                                        <div class="ml-2.5 sm:ml-3 text-right overflow-hidden min-w-0">
                                            <p
                                                class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </p>
                                            <p
                                                class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                                                <span
                                                    class="text-blue-600 dark:text-blue-400 font-extrabold">{{ $statusCount }}</span>
                                                <span class="text-slate-400 dark:text-slate-600">/</span>
                                                {{ $total }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State Message when no performance records found -->
                <div
                    class="flex flex-col items-center justify-center p-8 xs:p-10 sm:p-12 lg:p-20 bg-white/95 dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl text-center mt-4">
                    <div
                        class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 mb-3 sm:mb-4 shadow-inner ring-4 sm:ring-8 ring-blue-50/50 dark:ring-blue-950/20 shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight">
                        No Performance Data Found
                    </h3>
                    <p
                        class="text-xs sm:text-sm font-medium text-slate-400 dark:text-slate-500 mt-1 max-w-xs sm:max-w-sm">
                        There are no tasks or members available to display performance right now.
                    </p>
                </div>
            @endif
        </div>
    </main>
</div>
