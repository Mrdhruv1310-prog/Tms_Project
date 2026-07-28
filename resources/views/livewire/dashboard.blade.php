<div>
    @php
        $authUser = Auth::user();
        $isAdmin = $authUser ? $authUser->role === 'admin' : false;
        $isUser = $authUser ? $authUser->role === 'user' : false;
        $userName = $authUser ? ($authUser->name ?? $authUser->first_name ?? 'Commander') : 'Commander';

        $visibleCategories = collect($categories ?? []);
        $visibleTeam = collect($team ?? []);
        $visibleGroups = collect($groups ?? []);
        $visibleTasks = collect($tasksAssignedByUser ?? []);
    @endphp

    {{-- Clean, premium light corporate background (80% light off-white/cream tones with subtle 20% dark modern slate contrast) --}}
    <div class="min-h-screen bg-gradient-to-br from-[#F8FAFC] via-[#F1F5F9] to-[#E2E8F0] antialiased selection:bg-blue-600 selection:text-white text-slate-700">

        {{-- Main Container optimized seamlessly for all screens: Smartphones, Tablets, Laptops, Desktops, & 4K TVs --}}
        <main class="scrollcontainer h-auto px-3 sm:px-6 md:px-8 pb-20 pt-20 sm:pt-24 md:ml-16 lg:px-10 max-w-[1750px] mx-auto transition-all duration-300">

            {{-- Executive Header / Background updated to match the Groups Status Overview section style --}}
            <div class="mb-8 sm:mb-10 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white p-6 sm:p-8 relative">
                <div class="absolute -top-16 -left-16 w-72 sm:w-80 h-72 sm:h-80 bg-gradient-to-br from-blue-500/10 to-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between relative z-10">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-slate-500 overflow-x-auto py-1">
                            <span class="hover:text-blue-600 transition cursor-pointer flex items-center gap-1 shrink-0">
                                <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Home
                            </span>
                            <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            <span class="text-slate-700 font-bold bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 shadow-2xs shrink-0">Dashboard</span>
                        </div>

                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight text-slate-700 flex flex-wrap items-center gap-2">
                            <span>Welcome back {{ $userName }}</span>
                        </h1>

                        <p class="text-[11px] sm:text-xs md:text-sm font-medium text-slate-500 flex items-center gap-2 pt-0.5">
                            <span class="relative flex h-2 w-2 sm:h-2.5 sm:w-2.5 shrink-0">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 sm:h-2.5 sm:w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="truncate">{{ $isAdmin ? 'Corporate Team Overview & Operational Analytics Hub' : 'Personal Assigned Task Analytics & Progress Tracker' }}</span>
                        </p>
                    </div>

                </div>
            </div>

            {{-- Summary Cards Grid --}}
            <div class="mb-10 sm:mb-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach ($labels as $label)
                    @php
                        $title = strtolower($label['title']);

                        $cardGradient = match ($title) {
                            'pending' => 'from-rose-500 to-rose-600 shadow-rose-500/25 ring-rose-400/20',
                            'in progress' => 'from-amber-400 to-amber-500 shadow-amber-500/25 ring-amber-400/20',
                            'completed' => 'from-emerald-500 to-teal-600 shadow-emerald-500/25 ring-emerald-400/20',
                            'total' => 'from-blue-600 to-blue-700 shadow-blue-500/25 ring-blue-400/20',
                            default => 'from-slate-700 to-slate-800 shadow-slate-500/25 ring-slate-400/20',
                        };
                    @endphp

                    <div class="group relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br {{ $cardGradient }} p-5 sm:p-6 text-white shadow-xl ring-1 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl">
                        {{-- Lighting Effects --}}
                        <div class="absolute -right-8 -top-8 h-32 sm:h-36 w-32 sm:w-36 rounded-full bg-white/20 blur-2xl transition-all duration-700 group-hover:scale-150 group-hover:bg-white/30"></div>
                        <div class="absolute -bottom-10 -left-10 h-28 sm:h-32 w-28 sm:w-32 rounded-full bg-black/15 blur-xl transition-all duration-500 group-hover:translate-x-3"></div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="space-y-2.5 sm:space-y-3">
                                <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-white/90 drop-shadow-sm flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    {{ $label['title'] }}
                                </p>

                                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight font-mono drop-shadow-md">
                                    {{ $label['count'] }}
                                </h2>

                                <div class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 text-[10px] sm:text-[11px] font-medium text-white bg-white/20 rounded-full border border-white/15 backdrop-blur-md shadow-inner transition-transform group-hover:scale-105">
                                    <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 opacity-90 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Compared to last week</span>
                                </div>
                            </div>

                            <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 shadow-lg border border-white/30 backdrop-blur-xl transition-all duration-500 group-hover:rotate-12 group-hover:scale-110">
                                @if ($title === 'pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($title === 'in progress')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                                    </svg>
                                @elseif ($title === 'completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Category Report Section --}}
            <section class="mb-10 sm:mb-12 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-4 sm:px-8 py-5 sm:py-6 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-widest text-slate-700">Category Analytics Report</h2>
                        </div>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-500">
                            {{ $isAdmin ? 'Category-wise deep task completion progress across enterprise' : 'Your assigned category-wise task progress metrics' }}
                        </p>
                    </div>
                    <a href="{{ route('categoryReport') }}" wire:navigate
                        class="group inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl border border-slate-300 bg-white px-4 sm:px-5 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs transition-all hover:bg-slate-700 hover:text-white hover:border-slate-700 hover:shadow-md active:scale-95 w-full sm:w-auto">
                        <span>View All Categories</span>
                        <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 p-4 sm:p-8">
                    @forelse ($visibleCategories as $category)
                        @php
                            $percentage = isset($category['percentage']) && is_numeric($category['percentage']) ? (float) $category['percentage'] : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match(true) {
                                $percentage >= 71 => '#10B981',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <div class="group relative rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 transition-all duration-300 hover:border-slate-400 hover:shadow-xl hover:shadow-slate-300/40 hover:-translate-y-1">
                            <div class="mb-5 sm:mb-6 flex items-start justify-between gap-3">
                                <div class="truncate">
                                    <h3 class="truncate text-sm sm:text-base font-semibold text-slate-700 transition-colors group-hover:text-blue-600">
                                        {{ ucwords($category['title']) }}
                                    </h3>
                                    <p class="text-[10px] sm:text-[11px] font-medium text-slate-500 mt-0.5">Task completion status metric</p>
                                </div>
                                <span class="h-9 w-9 sm:h-10 sm:w-10 rounded-xl sm:rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 group-hover:bg-slate-700 group-hover:text-white group-hover:border-slate-700 transition-all shadow-2xs shrink-0">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </span>
                            </div>

                            <div class="flex flex-col items-center gap-4 sm:gap-6 sm:flex-row">
                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F1F5F9" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <span class="absolute text-xs sm:text-sm font-bold text-slate-700 font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <div class="flex items-baseline justify-center gap-1.5 sm:justify-start">
                                        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-700 font-mono">{{ $category['completed'] }}</span>
                                        <span class="text-[11px] sm:text-xs font-semibold text-slate-500">/ {{ $category['total'] }} Tasks</span>
                                    </div>
                                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 mt-0.5 uppercase tracking-wider">Completed Output</p>

                                    <div class="mt-3.5 sm:mt-4 h-2 sm:h-2.5 w-full overflow-hidden rounded-full bg-slate-100 p-0.5 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-10 sm:p-12 text-center text-xs sm:text-sm font-medium text-slate-500">
                            No category data available at the moment.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Team Performance Section --}}
            <section class="mb-10 sm:mb-12 overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-4 sm:px-8 py-5 sm:py-6 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-widest text-slate-700">
                                {{ $isAdmin ? 'Team Performance Intelligence' : 'My Personal Performance' }}
                            </h2>
                        </div>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-500">
                            {{ $isAdmin ? 'Detailed user-wise execution summary and efficiency ratings' : 'Your assigned execution and workflow summary' }}
                        </p>
                    </div>
                    <a href="{{ route('teamPerformance') }}" wire:navigate
                        class="group inline-flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl border border-slate-300 bg-white px-4 sm:px-5 py-2.5 text-xs font-semibold text-slate-700 shadow-2xs transition-all hover:bg-slate-700 hover:text-white hover:border-slate-700 hover:shadow-md active:scale-95 w-full sm:w-auto">
                        <span>View All Performance</span>
                        <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4 sm:gap-6 p-4 sm:p-8">
                    @forelse ($visibleTeam as $member)
                        @php
                            $percentage = isset($member['percentage']) && is_numeric($member['percentage']) ? (float) $member['percentage'] : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match(true) {
                                $percentage >= 71 => '#10B981',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <div class="group relative rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 transition-all duration-300 hover:border-slate-400 hover:shadow-xl hover:shadow-slate-300/40 hover:-translate-y-1">
                            <div class="mb-5 sm:mb-6 flex items-center justify-between gap-3">
                                <div class="truncate">
                                    <h3 class="truncate text-sm sm:text-base font-semibold text-slate-700 transition-colors group-hover:text-blue-600">
                                        {{ ucwords($member['name']) }}
                                    </h3>
                                    <p class="text-[10px] sm:text-[11px] font-medium text-slate-500 mt-0.5">Member task progress</p>
                                </div>
                                <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl bg-slate-100 font-bold text-xs text-blue-600 border border-slate-200 shadow-2xs uppercase tracking-wider">
                                    {{ substr($member['name'] ?? 'U', 0, 2) }}
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-4 sm:gap-6 sm:flex-row">
                                <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F1F5F9" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <span class="absolute text-xs sm:text-sm font-bold text-slate-700 font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <div class="flex items-baseline justify-center gap-1.5 sm:justify-start">
                                        <span class="text-xl sm:text-2xl font-bold tracking-tight text-slate-700 font-mono">{{ $member['completed'] }}</span>
                                        <span class="text-[11px] sm:text-xs font-semibold text-slate-500">/ {{ $member['total'] }} Tasks</span>
                                    </div>
                                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 mt-0.5 uppercase tracking-wider">Completed Output</p>

                                    <div class="mt-3.5 sm:mt-4 h-2 sm:h-2.5 w-full overflow-hidden rounded-full bg-slate-100 p-0.5 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-10 sm:p-12 text-center text-xs sm:text-sm font-medium text-slate-500">
                            No performance metrics available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Groups Overview Section --}}
            <section class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-300 bg-white/95 backdrop-blur-xl shadow-xl shadow-slate-300/50 ring-1 ring-white">
                <div class="border-b border-slate-200 px-4 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50 via-blue-50/30 to-transparent space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-widest text-slate-700">Groups Status Overview</h2>
                    </div>
                    <p class="text-[11px] sm:text-xs font-medium text-slate-500">
                        {{ $isAdmin ? 'Group-wise workflow status & granular task distribution overview' : 'Your group-wise workflow status & progress overview' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 p-4 sm:p-8">
                    @forelse ($visibleGroups as $group)
                        @php
                            $percentage = isset($group['percentage']) && is_numeric($group['percentage']) ? (float) $group['percentage'] : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match(true) {
                                $percentage >= 71 => '#10B981',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <a href="{{ route('group.details', ['id' => $group['id'] ?? 0]) }}" wire:navigate class="block h-full group outline-none">
                            <div class="relative flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 transition-all duration-300 hover:border-slate-400 hover:shadow-xl hover:shadow-slate-300/40 hover:-translate-y-1">
                                <div>
                                    <div class="mb-5 sm:mb-6 flex items-start justify-between gap-3">
                                        <div class="truncate">
                                            <h3 class="truncate text-sm sm:text-base font-semibold text-slate-700 transition-colors group-hover:text-blue-600">
                                                {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                            </h3>
                                            <p class="text-[10px] sm:text-[11px] font-medium text-slate-500 mt-0.5">Group task analytics summary</p>
                                        </div>
                                        <div class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-slate-700 group-hover:text-white transition-all border border-slate-200 shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center gap-4 sm:gap-6 sm:flex-row">
                                        <div class="relative flex h-20 w-20 shrink-0 items-center justify-center">
                                            <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F1F5F9" stroke-width="9" />
                                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" class="transition-all duration-1000 ease-out" />
                                            </svg>
                                            <span class="absolute text-xs sm:text-sm font-bold text-slate-700 font-mono">{{ $percentage }}%</span>
                                        </div>

                                        <div class="w-full space-y-2 sm:space-y-2.5 text-[11px] sm:text-xs font-semibold text-slate-600">
                                            <div class="flex justify-between items-center border-b border-slate-100 pb-1.5 sm:pb-2">
                                                <span class="flex items-center gap-2 font-medium text-slate-500"><span class="h-2 w-2 rounded-full bg-rose-500 shadow-2xs"></span>Pending</span>
                                                <span class="font-bold text-slate-700 font-mono">{{ $group['pending'] ?? 0 }}</span>
                                            </div>

                                            <div class="flex justify-between items-center border-b border-slate-100 pb-1.5 sm:pb-2">
                                                <span class="flex items-center gap-2 font-medium text-slate-500"><span class="h-2 w-2 rounded-full bg-amber-400 shadow-2xs"></span>In Progress</span>
                                                <span class="font-bold text-slate-700 font-mono">{{ $group['pending'] ?? 0 }}</span>
                                            </div>

                                            <div class="flex justify-between items-center pt-0.5">
                                                <span class="flex items-center gap-2 font-medium text-slate-500"><span class="h-2 w-2 rounded-full bg-emerald-500 shadow-2xs"></span>Completed</span>
                                                <span class="font-bold text-slate-700 font-mono">{{ $group['completed'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 sm:mt-6 h-2 sm:h-2.5 w-full overflow-hidden rounded-full bg-slate-100 p-0.5 shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}"></div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-10 sm:p-12 text-center text-xs sm:text-sm font-medium text-slate-500">
                            No groups data available.
                        </div>
                    @endforelse
                </div>
            </section>

        </main>
    </div>
</div>
