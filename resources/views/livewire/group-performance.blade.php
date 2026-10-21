<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden antialiased transition-all duration-200">
    <main
        class="scrollcontainer md:ml-16 px-2.5 xs:px-4 sm:px-6 lg:px-8 py-4 sm:py-6 pt-16 sm:pt-20 pb-12 sm:pb-16 max-w-[2560px] mx-auto">

        <!-- Back Button -->
        <div class="mb-3.5 sm:mb-5 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-gray-200/80 dark:border-slate-800 text-xs sm:text-sm font-semibold text-gray-700 dark:text-slate-200 hover:text-[rgb(7,139,221)] dark:hover:text-[rgb(7,139,221)] hover:border-[rgb(7,139,221)]/30 shadow-xs transition-all duration-150 active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-3.5 h-3.5 sm:w-4 sm:h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <!-- Header Banner Section -->
        <div
            class="mb-6 sm:mb-8 lg:mb-10 overflow-hidden rounded-xl sm:rounded-3xl border border-slate-300 dark:border-slate-800 bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl shadow-lg sm:shadow-xl shadow-slate-300/40 dark:shadow-none ring-1 ring-white/80 p-4 sm:p-6 lg:p-8 2xl:p-10 relative">
            <div class="flex flex-col gap-3.5 sm:gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div
                        class="flex h-10 w-10 sm:h-12 sm:w-12 lg:h-14 lg:w-14 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 shadow-xs">
                        <svg class="w-5 h-5 sm:w-7 sm:h-7 lg:w-8 lg:h-8" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="space-y-0.5 sm:space-y-1">
                        <h1
                            class="text-base sm:text-xl lg:text-2xl 2xl:text-3xl font-bold tracking-tight text-slate-900 dark:text-white truncate">
                            {{ $groupName }} Group Performance
                        </h1>
                        <p class="text-xs sm:text-sm lg:text-base text-slate-500 dark:text-slate-400 font-normal">
                            User Analytics & Metrics Overview
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Performance Grid Section -->
        <div class="mb-6">
            @if (count($users) === 0)
                <div class="flex justify-center items-center rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-6 sm:p-12 shadow-xs"
                    style="min-height: 40vh;">
                    <div class="text-center">
                        <div
                            class="w-11 h-11 sm:w-14 sm:h-14 bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900 rounded-xl sm:rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mx-auto mb-3 sm:mb-4 shadow-2xs">
                            <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 7.5A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5v9a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 014 16.5v-9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5V3m6 2V3" />
                            </svg>
                        </div>
                        <h4
                            class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            No Users
                            Found</h4>
                        <p class="text-[11px] sm:text-xs text-slate-400 dark:text-slate-500 mt-1 font-medium">There are
                            no active users
                            assigned to this group yet.</p>
                    </div>
                </div>
            @endif

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 3xl:grid-cols-6 gap-3.5 sm:gap-5 lg:gap-6 mt-4">
                @foreach ($users as $user)
                    <div
                        class="group relative rounded-xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-4 sm:p-6 shadow-xs hover:bg-blue-600 dark:hover:bg-blue-600 hover:border-blue-600 hover:shadow-xl hover:shadow-blue-500/20 hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col justify-between cursor-pointer transform-gpu">

                        <!-- Top Accent Highlight Glow -->
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        </div>

                        <div>
                            <!-- User Name Header -->
                            <div
                                class="flex items-center gap-3 sm:gap-3.5 pb-3 sm:pb-4 mb-3 sm:mb-4 border-b border-slate-100 dark:border-slate-800/80 group-hover:border-white/20 transition-colors">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 dark:bg-slate-800 group-hover:bg-white text-white group-hover:text-blue-600 flex items-center justify-center text-xs sm:text-sm font-black uppercase tracking-tight shadow-xs transition-colors shrink-0">
                                    {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div
                                    class="text-xs sm:text-base font-black text-slate-900 dark:text-white group-hover:text-white tracking-tight truncate">
                                    {{ ucwords($user['name']) }}
                                </div>
                            </div>

                            <!-- Progress bars for Pending, In Progress, Completed -->
                            <div class="space-y-2 sm:space-y-3">
                                @foreach (['pending' => '#f87171', 'in_progress' => '#fbbf24', 'completed' => '#34d399'] as $status => $color)
                                    @php
                                        $statusCount = $user[$status] ?? 0;
                                        $total = $user['total'] ?? 0;
                                        $percentage = $total > 0 ? ($statusCount / $total) * 100 : 0;
                                        $circumference = 565.48;
                                        $offset = $circumference - ($circumference * $percentage) / 100;
                                    @endphp

                                    <div
                                        class="flex items-center justify-between rounded-lg sm:rounded-2xl border border-slate-200/60 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-950/40 group-hover:bg-white/10 group-hover:border-white/20 p-2.5 sm:p-3 transition-all duration-150 shadow-2xs">
                                        <!-- SVG Progress Bar -->
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 shrink-0">
                                            <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                                <circle r="90" cx="100" cy="100" fill="transparent"
                                                    stroke="currentColor"
                                                    class="text-slate-200 dark:text-slate-800 group-hover:text-white/20"
                                                    stroke-width="16px" stroke-dasharray="565.48px"
                                                    stroke-dashoffset="0"></circle>
                                                <circle r="90" cx="100" cy="100"
                                                    stroke="{{ $color }}" stroke-width="16px"
                                                    stroke-linecap="round" stroke-dashoffset="{{ $offset }}"
                                                    fill="transparent" stroke-dasharray="565.48px"
                                                    class="transition-all duration-300 ease-out"></circle>
                                                <text
                                                    class="fill-current text-slate-700 dark:text-slate-200 group-hover:text-white"
                                                    x="52px" y="112px" font-size="44px" font-weight="900"
                                                    style="transform: rotate(90deg) translate(0px, -196px)">
                                                    {{ (int) $percentage }}%
                                                </text>
                                            </svg>
                                        </div>

                                        <!-- Task Completion Status -->
                                        <div class="ml-2.5 sm:ml-3 text-right truncate">
                                            <p
                                                class="text-[10px] sm:text-xs font-black text-slate-800 dark:text-slate-200 group-hover:text-white tracking-tight uppercase">
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </p>
                                            <p
                                                class="text-[9px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-400 group-hover:text-blue-100">
                                                {{ $statusCount }}/{{ $total }} Tasks
                                            </p>
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
