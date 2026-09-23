<div>
    @php
        $authUser = Auth::user();
        $isAdmin = $authUser ? in_array($authUser->role, ['admin', 'super-admin'], true) : false;
        $isUser = $authUser ? $authUser->role === 'user' : false;
        $userName = $authUser ? $authUser->name ?? ($authUser->first_name ?? 'Commander') : 'Commander';

        $visibleCategories = collect($categories ?? []);
        $visibleTeam = collect($team ?? []);
        $visibleGroups = collect($groups ?? []);
        $visibleTasks = collect($tasksAssignedByUser ?? []);
    @endphp

    <div
        class="min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] antialiased selection:bg-[rgb(7,139,221)] selection:text-white text-gray-800">

        <main
            class="scrollcontainer h-auto px-3 sm:px-6 md:px-8 pb-20 pt-20 sm:pt-24 md:ml-16 lg:px-10 max-w-[1750px] mx-auto transition-all duration-300">

            {{-- Executive Header --}}
            <div
                class="mb-8 sm:mb-10 overflow-hidden rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.08)] ring-1 ring-black/5 p-6 sm:p-8 relative">

                <div
                    class="absolute -top-32 -left-32 w-96 h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-32 -right-32 w-96 h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
                </div>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                    <div class="space-y-2">
                        <div
                            class="flex items-center gap-2 text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-gray-600 overflow-x-auto py-1">
                            <span
                                class="text-gray-800 font-bold bg-white/90 px-3 py-1.5 rounded-xl border border-gray-200/80 shadow-xs shrink-0">Dashboard</span>
                        </div>

                        <h1
                            class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 flex flex-wrap items-center gap-2">
                            <span>Welcome back {{ $userName }}</span>
                        </h1>

                    </div>
                </div>
            </div>

            {{-- Summary Cards Grid (Colorful & Vibrant Redesign with Original Clean Backgrounds) --}}
            <div class="mb-10 sm:mb-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach ($labels as $label)
                    @php
                        $title = strtolower($label['title']);

                        // Original clean background style with rich colorful glowing borders & shadows
                        $cardStyle = match ($title) {
                            'pending'
                                => 'bg-white/95 border-rose-300/80 text-gray-900 shadow-[0_15px_35px_rgba(244,63,94,0.12)] hover:border-rose-400',
                            'in progress'
                                => 'bg-white/95 border-amber-300/80 text-gray-900 shadow-[0_15px_35px_rgba(245,158,11,0.12)] hover:border-amber-400',
                            'completed'
                                => 'bg-white/95 border-emerald-300/80 text-gray-900 shadow-[0_15px_35px_rgba(16,185,129,0.12)] hover:border-emerald-400',
                            'total'
                                => 'bg-white/95 border-[rgb(7,139,221)]/40 text-gray-900 shadow-[0_15px_35px_rgba(7,139,221,0.12)] hover:border-[rgb(7,139,221)]',
                            default
                                => 'bg-white/95 border-gray-200 text-gray-900 shadow-[0_15px_35px_rgba(0,0,0,0.06)]',
                        };

                        $badgeStyle = match ($title) {
                            'pending' => 'bg-rose-50 text-rose-700 border-rose-200 font-semibold',
                            'in progress' => 'bg-amber-50 text-amber-700 border-amber-200 font-semibold',
                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 font-semibold',
                            'total'
                                => 'bg-[rgb(7,139,221)]/10 text-[rgb(7,139,221)] border-[rgb(7,139,221)]/30 font-semibold',
                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                        };

                        $iconBg = match ($title) {
                            'pending'
                                => 'bg-gradient-to-tr from-rose-600 to-rose-400 text-white shadow-lg shadow-rose-500/40',
                            'in progress'
                                => 'bg-gradient-to-tr from-amber-600 to-amber-400 text-white shadow-lg shadow-amber-500/40',
                            'completed'
                                => 'bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white shadow-lg shadow-emerald-500/40',
                            'total'
                                => 'bg-gradient-to-tr from-[rgb(7,139,221)] to-cyan-400 text-white shadow-lg shadow-[rgb(7,139,221)]/40',
                            default => 'bg-gray-800 text-white shadow-lg shadow-gray-500/30',
                        };

                        $countColor = match ($title) {
                            'pending' => 'text-rose-600',
                            'in progress' => 'text-amber-600',
                            'completed' => 'text-emerald-600',
                            'total' => 'text-[rgb(7,139,221)]',
                            default => 'text-gray-900',
                        };
                    @endphp

                    <div
                        class="group relative overflow-hidden rounded-3xl {{ $cardStyle }} backdrop-blur-xl p-6 border-2 ring-1 ring-white/80 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl">

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="space-y-3">
                                <p
                                    class="text-[11px] font-bold uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full bg-current opacity-80 animate-pulse"></span>
                                    {{ $label['title'] }}
                                </p>

                                <h2
                                    class="text-3xl sm:text-4xl font-extrabold tracking-tight font-mono {{ $countColor }}">
                                    {{ $label['count'] }}
                                </h2>

                                <div
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] rounded-full border backdrop-blur-md shadow-xs transition-transform group-hover:scale-105 {{ $badgeStyle }}">
                                    <svg class="h-3.5 w-3.5 opacity-90 transition-transform group-hover:translate-x-0.5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Compared to last week</span>
                                </div>
                            </div>

                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $iconBg }} backdrop-blur-xl transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                                @if ($title === 'pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($title === 'in progress')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                                    </svg>
                                @elseif ($title === 'completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Category Report Section --}}
            <section
                class="mb-10 sm:mb-12 overflow-hidden rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.08)] ring-1 ring-black/5">
                <div
                    class="flex flex-col gap-3 border-b border-gray-100 px-6 sm:px-8 py-6 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-gray-50/50 via-[rgb(7,139,221)]/5 to-transparent">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[rgb(7,139,221)] animate-pulse"></span>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700">Category
                                Analytics Report</h2>
                        </div>
                        <p class="text-xs font-medium text-gray-500">
                            {{ $isAdmin ? 'Category-wise deep task completion progress across enterprise' : 'Your assigned category-wise task progress metrics' }}
                        </p>
                    </div>
                    <a href="{{ route('categoryReport') }}" wire:navigate
                        class="group inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200/80 bg-white px-5 py-2.5 text-xs font-semibold text-gray-700 shadow-sm transition-all hover:bg-[rgb(7,139,221)] hover:text-white hover:border-[rgb(7,139,221)] hover:shadow-md active:scale-95 w-full sm:w-auto">
                        <span>View All Categories</span>
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 p-6 sm:p-8">
                    @forelse ($visibleCategories as $category)
                        @php
                            $percentage =
                                isset($category['percentage']) && is_numeric($category['percentage'])
                                    ? (float) $category['percentage']
                                    : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match (true) {
                                $percentage >= 71 => '#10B981',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <div
                            class="group relative rounded-2xl border border-gray-200/80 bg-white/90 backdrop-blur-md p-6 transition-all duration-300 hover:border-[rgb(7,139,221)]/50 hover:shadow-xl hover:shadow-[rgb(7,139,221)]/5 hover:-translate-y-1">
                            <div class="mb-6 flex items-start justify-between gap-3">
                                <div class="truncate">
                                    <h3
                                        class="truncate text-base font-semibold text-gray-800 transition-colors group-hover:text-[rgb(7,139,221)]">
                                        {{ ucwords($category['title']) }}
                                    </h3>
                                    <p class="text-[11px] font-medium text-gray-500 mt-0.5">Task
                                        completion status metric</p>
                                </div>
                                <span
                                    class="h-10 w-10 rounded-2xl bg-gray-50 border border-gray-200/80 flex items-center justify-center text-gray-500 group-hover:bg-[rgb(7,139,221)] group-hover:text-white group-hover:border-[rgb(7,139,221)] transition-all shadow-xs shrink-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </span>
                            </div>

                            <div class="flex flex-col items-center gap-6 sm:flex-row">
                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F3F4F6"
                                            stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $offset }}"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <span
                                        class="absolute text-sm font-bold text-gray-800 font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <div class="flex items-baseline justify-center gap-1.5 sm:justify-start">
                                        <span
                                            class="text-2xl font-bold tracking-tight text-gray-900 font-mono">{{ $category['completed'] }}</span>
                                        <span class="text-xs font-semibold text-gray-500">/
                                            {{ $category['total'] }} Tasks</span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-gray-500 mt-0.5 uppercase tracking-wider">
                                        Completed Output</p>

                                    <div
                                        class="mt-4 h-2.5 w-full overflow-hidden rounded-full bg-gray-100 p-0.5 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000 shadow-sm"
                                            style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-gray-50/50 p-12 text-center text-sm font-medium text-gray-500">
                            No category data available at the moment.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Team Performance Section --}}
            <section
                class="mb-10 sm:mb-12 overflow-hidden rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.08)] ring-1 ring-black/5">
                <div
                    class="flex flex-col gap-3 border-b border-gray-100 px-6 sm:px-8 py-6 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-gray-50/50 via-[rgb(7,139,221)]/5 to-transparent">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[rgb(7,139,221)] animate-pulse"></span>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700">
                                {{ $isAdmin ? 'Team Performance Intelligence' : 'My Personal Performance' }}
                            </h2>
                        </div>
                        <p class="text-xs font-medium text-gray-500">
                            {{ $isAdmin ? 'Detailed user-wise execution summary and efficiency ratings' : 'Your assigned execution and workflow summary' }}
                        </p>
                    </div>
                    <a href="{{ route('teamPerformance') }}" wire:navigate
                        class="group inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200/80 bg-white px-5 py-2.5 text-xs font-semibold text-gray-700 shadow-sm transition-all hover:bg-[rgb(7,139,221)] hover:text-white hover:border-[rgb(7,139,221)] hover:shadow-md active:scale-95 w-full sm:w-auto">
                        <span>View All Performance</span>
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-5 p-6 sm:p-8">
                    @forelse ($visibleTeam as $member)
                        @php
                            $percentage =
                                isset($member['percentage']) && is_numeric($member['percentage'])
                                    ? (float) $member['percentage']
                                    : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match (true) {
                                $percentage >= 71 => '#10B981',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <div
                            class="group relative rounded-2xl border border-gray-200/80 bg-white/90 backdrop-blur-md p-6 transition-all duration-300 hover:border-[rgb(7,139,221)]/50 hover:shadow-xl hover:shadow-[rgb(7,139,221)]/5 hover:-translate-y-1">
                            <div class="mb-6 flex items-center justify-between gap-3">
                                <div class="truncate">
                                    <h3
                                        class="truncate text-base font-semibold text-gray-800 transition-colors group-hover:text-[rgb(7,139,221)]">
                                        {{ ucwords($member['name']) }}
                                    </h3>
                                    <p class="text-[11px] font-medium text-gray-500 mt-0.5">Member task
                                        progress</p>
                                </div>
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gray-50 font-bold text-xs text-[rgb(7,139,221)] border border-gray-200/80 shadow-xs uppercase tracking-wider">
                                    {{ substr($member['name'] ?? 'U', 0, 2) }}
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-6 sm:flex-row">
                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="#F3F4F6" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $offset }}"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <span
                                        class="absolute text-sm font-bold text-gray-800 font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <div class="flex items-baseline justify-center gap-1.5 sm:justify-start">
                                        <span
                                            class="text-2xl font-bold tracking-tight text-gray-900 font-mono">{{ $member['completed'] }}</span>
                                        <span class="text-xs font-semibold text-gray-500">/
                                            {{ $member['total'] }} Tasks</span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-gray-500 mt-0.5 uppercase tracking-wider">
                                        Completed Output</p>

                                    <div
                                        class="mt-4 h-2.5 w-full overflow-hidden rounded-full bg-gray-100 p-0.5 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000 shadow-sm"
                                            style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-gray-50/50 p-12 text-center text-sm font-medium text-gray-500">
                            No performance metrics available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Groups Overview Section --}}
            <section
                class="overflow-hidden rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.08)] ring-1 ring-black/5">
                <div
                    class="border-b border-gray-100 px-6 sm:px-8 py-6 bg-gradient-to-r from-gray-50/50 via-[rgb(7,139,221)]/5 to-transparent space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[rgb(7,139,221)] animate-pulse"></span>
                        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700">Groups Status
                            Overview</h2>
                    </div>
                    <p class="text-xs font-medium text-gray-500">
                        {{ $isAdmin ? 'Group-wise workflow status & granular task distribution overview' : 'Your group-wise workflow status & progress overview' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 p-6 sm:p-8">
                    @forelse ($visibleGroups as $group)
                        @php
                            $percentage =
                                isset($group['percentage']) && is_numeric($group['percentage'])
                                    ? (float) $group['percentage']
                                    : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match (true) {
                                $percentage >= 71 => '#10B981',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <a href="{{ route('group.details', ['id' => $group['id'] ?? 0]) }}" wire:navigate
                            class="block h-full group outline-none">
                            <div
                                class="relative flex h-full flex-col justify-between rounded-2xl border border-gray-200/80 bg-white/90 backdrop-blur-md p-6 transition-all duration-300 hover:border-[rgb(7,139,221)]/50 hover:shadow-xl hover:shadow-[rgb(7,139,221)]/5 hover:-translate-y-1">
                                <div>
                                    <div class="mb-6 flex items-start justify-between gap-3">
                                        <div class="truncate">
                                            <h3
                                                class="truncate text-base font-semibold text-gray-800 transition-colors group-hover:text-[rgb(7,139,221)]">
                                                {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                            </h3>
                                            <p class="text-[11px] font-medium text-gray-500 mt-0.5">
                                                Group task analytics summary</p>
                                        </div>
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-50 text-gray-500 group-hover:bg-[rgb(7,139,221)] group-hover:text-white transition-all border border-gray-200/80 shadow-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center gap-6 sm:flex-row">
                                        <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                                            <svg class="h-full w-full -rotate-90 drop-shadow-sm"
                                                viewBox="0 0 100 100">
                                                <circle r="40" cx="50" cy="50" fill="transparent"
                                                    stroke="#F3F4F6" stroke-width="9" />
                                                <circle r="40" cx="50" cy="50" fill="transparent"
                                                    stroke="{{ $strokeColor }}" stroke-width="9"
                                                    stroke-linecap="round" stroke-dasharray="{{ $circumference }}"
                                                    stroke-dashoffset="{{ $offset }}"
                                                    class="transition-all duration-1000 ease-out" />
                                            </svg>
                                            <span
                                                class="absolute text-sm font-bold text-gray-800 font-mono">{{ $percentage }}%</span>
                                        </div>

                                        <div class="w-full space-y-2 text-xs font-semibold text-gray-600">
                                            <div
                                                class="flex justify-between items-center border-b border-gray-100 pb-2">
                                                <span class="flex items-center gap-2 font-medium text-gray-500"><span
                                                        class="h-2 w-2 rounded-full bg-rose-500 shadow-xs"></span>Pending</span>
                                                <span
                                                    class="font-bold text-gray-800 font-mono">{{ $group['pending'] ?? 0 }}</span>
                                            </div>

                                            <div
                                                class="flex justify-between items-center border-b border-gray-100 pb-2">
                                                <span class="flex items-center gap-2 font-medium text-gray-500"><span
                                                        class="h-2 w-2 rounded-full bg-amber-400 shadow-xs"></span>In
                                                    Progress</span>
                                                <span
                                                    class="font-bold text-gray-800 font-mono">{{ $group['pending'] ?? 0 }}</span>
                                            </div>

                                            <div class="flex justify-between items-center pt-0.5">
                                                <span class="flex items-center gap-2 font-medium text-gray-500"><span
                                                        class="h-2 w-2 rounded-full bg-emerald-500 shadow-xs"></span>Completed</span>
                                                <span
                                                    class="font-bold text-gray-800 font-mono">{{ $group['completed'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="mt-6 h-2.5 w-full overflow-hidden rounded-full bg-gray-100 p-0.5 shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-1000 shadow-sm"
                                        style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div
                            class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-gray-50/50 p-12 text-center text-sm font-medium text-gray-500">
                            No groups data available.
                        </div>
                    @endforelse
                </div>
            </section>

        </main>
    </div>
</div>
