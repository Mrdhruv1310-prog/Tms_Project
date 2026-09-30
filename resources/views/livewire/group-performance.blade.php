<div
    class="relative min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] overflow-x-hidden">
    <main class="scrollcontainer md:ml-16 px-4 sm:px-6 lg:px-8 py-6 pt-20 pb-16">

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
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h1 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900">
                            {{ $groupName }} Group Performance
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-normal">
                            User Analytics & Metrics Overview
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Section with Back Button & Centered Title -->
        {{-- <div
            class="relative flex items-center mb-8 sm:mb-10 overflow-hidden rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white/80 dark:bg-slate-900/85 backdrop-blur-2xl shadow-xl shadow-slate-200/50 dark:shadow-none p-6 sm:p-8">
            <div
                class="absolute -top-20 -left-20 w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent rounded-full blur-3xl pointer-events-none">
            </div>

            <!-- Back Button (Left-Aligned) -->
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

            <!-- Title (Centered) -->
            <div class="absolute left-1/2 -translate-x-1/2 text-center px-4">
                <h3 class="text-lg sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ $groupName }} Group Performance
                </h3>
                <p
                    class="text-[11px] sm:text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">
                    User Analytics & Metrics Overview
                </p>
            </div>
        </div> --}}

        <!-- Group Performance Section -->
        <div class="mb-6">
            @if (count($users) === 0)
                <div class="flex justify-center items-center rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-12 shadow-sm"
                    style="min-height: 40vh;">
                    <div class="text-center">
                        <div
                            class="w-14 h-14 bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mx-auto mb-4 shadow-2xs">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 7.5A2.5 2.5 0 016.5 5h11A2.5 2.5 0 0120 7.5v9a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 014 16.5v-9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5V3m6 2V3" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">No Users
                            Found</h4>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 font-medium">There are no active users
                            assigned to this group yet.</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-4">
                @foreach ($users as $user)
                    <div
                        class="group relative rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 sm:p-6 shadow-sm hover:bg-blue-600 dark:hover:bg-blue-600 hover:border-blue-600 hover:shadow-2xl hover:shadow-blue-500/20 hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between">

                        <!-- Top Accent Highlight Glow -->
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <div>
                            <!-- User Name Header -->
                            <div
                                class="flex items-center gap-3.5 pb-4 mb-4 border-b border-slate-100 dark:border-slate-800/80 group-hover:border-white/20 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-900 dark:bg-slate-800 group-hover:bg-white text-white group-hover:text-blue-600 flex items-center justify-center text-xs font-black uppercase tracking-tight shadow-sm transition-colors">
                                    {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div
                                    class="text-base font-black text-slate-900 dark:text-white group-hover:text-white tracking-tight truncate">
                                    {{ ucwords($user['name']) }}
                                </div>
                            </div>

                            <!-- Progress bars for Pending, In Progress, Completed -->
                            <div class="space-y-3">
                                @foreach (['pending' => '#f87171', 'in_progress' => '#fbbf24', 'completed' => '#34d399'] as $status => $color)
                                    @php
                                        $statusCount = $user[$status] ?? 0;
                                        $total = $user['total'] ?? 0;
                                        $percentage = $total > 0 ? ($statusCount / $total) * 100 : 0;
                                        $circumference = 565.48;
                                        $offset = $circumference - ($circumference * $percentage) / 100;
                                    @endphp

                                    <div
                                        class="flex items-center justify-between rounded-2xl border border-slate-200/60 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-950/40 group-hover:bg-white/10 group-hover:border-white/20 p-3 transition-all duration-200 shadow-2xs">
                                        <!-- SVG Progress Bar -->
                                        <div class="w-10 h-10 shrink-0">
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
                                                    fill="transparent" stroke-dasharray="565.48px"></circle>
                                                <text
                                                    class="fill-current text-slate-700 dark:text-slate-200 group-hover:text-white"
                                                    x="52px" y="112px" font-size="44px" font-weight="900"
                                                    style="transform: rotate(90deg) translate(0px, -196px)">
                                                    {{ (int) $percentage }}%
                                                </text>
                                            </svg>
                                        </div>

                                        <!-- Task Completion Status -->
                                        <div class="ml-3 text-right truncate">
                                            <p
                                                class="text-xs font-black text-slate-800 dark:text-slate-200 group-hover:text-white tracking-tight uppercase">
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </p>
                                            <p
                                                class="text-[11px] font-bold text-slate-400 dark:text-slate-400 group-hover:text-blue-100">
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
