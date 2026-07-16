<div>
    @php
        $authUser = Auth::user();
        $isAdmin = $authUser ? $authUser->role === 'admin' : false;
        $isUser = $authUser ? $authUser->role === 'user' : false;

        $visibleCategories = collect($categories ?? []);
        $visibleTeam = collect($team ?? []);
        $visibleGroups = collect($groups ?? []);
        $visibleTasks = collect($tasksAssignedByUser ?? []);
    @endphp

    <div class="min-h-screen bg-[#F8FAFC] antialiased selection:bg-blue-500 selection:text-white">

        {{-- Yahan pt-24 lagaya hai jo navbar ke thik niche space generate karega --}}
        <main class="scrollcontainer h-auto px-4 pb-16 pt-24 sm:px-6 md:ml-16 lg:px-8 max-w-[1600px] mx-auto transition-all duration-300">

            {{-- Header Line --}}
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/60 pb-6">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <span class="hover:text-slate-600 transition cursor-pointer">Home</span>
                        <svg class="h-2.5 w-2.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        <span class="text-[#001b4d] font-extrabold">Dashboard</span>
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-[#001b4d] sm:text-3xl lg:text-4xl">
                        Hi, welcome back!
                    </h1>

                    <p class="mt-1.5 text-xs sm:text-sm font-medium text-slate-500/90 flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $isAdmin ? 'Welcome back, here is your team task overview.' : 'Welcome back, here is your assigned task overview.' }}
                    </p>
                </div>
            </div>

            {{-- Summary Cards Grid: Mobile me 1 column, Tablet me 2, Large screens me 4 --}}
            <div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($labels as $label)
                    @php
                        $title = strtolower($label['title']);

                        $cardGradient = match ($title) {
                            'pending' => 'from-rose-500 to-rose-600 shadow-rose-100/80 ring-rose-400/20',
                            'in progress' => 'from-amber-400 to-amber-500 shadow-amber-100/80 ring-amber-400/20',
                            'completed' => 'from-emerald-500 to-teal-600 shadow-emerald-100/80 ring-emerald-400/20',
                            'total' => 'from-[#0067f4] to-blue-600 shadow-blue-100/80 ring-blue-400/20',
                            default => 'from-slate-700 to-slate-800 shadow-slate-100/80 ring-slate-400/20',
                        };
                    @endphp

                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $cardGradient }} p-5 sm:p-6 text-white shadow-lg ring-1 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10 blur-xl transition-all duration-700 group-hover:scale-150 group-hover:bg-white/15"></div>
                        <div class="absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-black/5 blur-lg transition-all duration-500 group-hover:translate-x-2"></div>

                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div class="space-y-3 sm:space-y-4">
                                <p class="text-[10px] sm:text-[11px] font-black uppercase tracking-widest text-white/75">
                                    {{ $label['title'] }}
                                </p>

                                <h2 class="text-2xl sm:text-3xl font-black tracking-tight font-mono">
                                    {{ $label['count'] }}
                                </h2>

                                <div class="flex items-center gap-1.5 pt-0.5 text-[11px] font-semibold text-white/90 bg-white/10 rounded-full px-2.5 py-0.5 w-fit border border-white/5 backdrop-blur-sm">
                                    <svg class="h-3.5 w-3.5 opacity-90 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Compared to last week</span>
                                </div>
                            </div>

                            <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 shadow-md border border-white/20 backdrop-blur-md transition-transform duration-300 group-hover:rotate-6">
                                @if ($title === 'pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($title === 'in progress')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                                    </svg>
                                @elseif ($title === 'completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Category Report Section --}}
            <section class="mb-10 overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm ring-1 ring-slate-100/50">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-4 sm:px-6 py-4 sm:flex-row sm:items-center sm:justify-between bg-slate-50/50">
                    <div>
                        <h2 class="text-xs sm:text-sm font-black uppercase tracking-widest text-[#001b4d]">Category Report</h2>
                        <p class="mt-0.5 text-[11px] sm:text-xs font-medium text-slate-400">
                            {{ $isAdmin ? 'Category-wise task completion progress' : 'Your assigned category-wise task progress' }}
                        </p>
                    </div>
                    <a href="{{ route('categoryReport') }}" wire:navigate
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-[#001b4d] shadow-sm transition-all hover:bg-slate-50 hover:border-slate-300 hover:shadow active:scale-95 w-full sm:w-auto">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 sm:grid-cols-2 xl:grid-cols-3">
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
                            $badgeColor = match(true) {
                                $percentage >= 71 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                $percentage >= 31 => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-rose-50 text-rose-700 border-rose-200',
                            };
                        @endphp

                        <div class="group relative rounded-xl border border-slate-150 bg-white p-4 sm:p-5 transition-all duration-300 hover:border-blue-300/80 hover:shadow-md hover:shadow-blue-500/5">
                            <div class="mb-4 sm:mb-6 flex items-start justify-between gap-2">
                                <div class="truncate">
                                    <h3 class="truncate text-sm sm:text-base font-bold text-slate-800 transition-colors group-hover:text-blue-600">
                                        {{ ucwords($category['title']) }}
                                    </h3>
                                    <p class="text-[10px] sm:text-[11px] font-medium text-slate-400 mt-0.5">Task completion status</p>
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-4 sm:flex-row sm:gap-6">
                                <div class="relative flex h-16 w-16 sm:h-20 sm:w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F1F5F9" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" class="transition-all duration-700" />
                                    </svg>
                                    <span class="absolute text-xs sm:text-sm font-black text-[#001b4d] font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <div class="flex items-baseline justify-center gap-1 sm:justify-start">
                                        <span class="text-xl sm:text-2xl font-black tracking-tight text-[#001b4d] font-mono">{{ $category['completed'] }}</span>
                                        <span class="text-[11px] font-bold text-slate-400">/ {{ $category['total'] }} Tasks</span>
                                    </div>
                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Completed</p>

                                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full transition-all duration-700" style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-8 sm:p-10 text-center text-xs sm:text-sm font-semibold text-slate-400/90">
                            No category data available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Team Performance Section --}}
            <section class="mb-10 overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm ring-1 ring-slate-100/50">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-4 sm:px-6 py-4 sm:flex-row sm:items-center sm:justify-between bg-slate-50/50">
                    <div>
                        <h2 class="text-xs sm:text-sm font-black uppercase tracking-widest text-[#001b4d]">
                            {{ $isAdmin ? 'Team Performance' : 'My Performance' }}
                        </h2>
                        <p class="mt-0.5 text-[11px] sm:text-xs font-medium text-slate-400">
                            {{ $isAdmin ? 'User-wise task completion summary' : 'Your assigned task completion summary' }}
                        </p>
                    </div>
                    <a href="{{ route('teamPerformance') }}" wire:navigate
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-[#001b4d] shadow-sm transition-all hover:bg-slate-50 hover:border-slate-300 hover:shadow active:scale-95 w-full sm:w-auto">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
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

                        <div class="group relative rounded-xl border border-slate-150 bg-white p-4 sm:p-5 transition-all duration-300 hover:border-blue-300/80 hover:shadow-md hover:shadow-blue-500/5">
                            <div class="mb-4 sm:mb-6 flex items-center justify-between gap-3">
                                <div class="truncate">
                                    <h3 class="truncate text-sm sm:text-base font-bold text-slate-800 transition-colors group-hover:text-blue-600">
                                        {{ ucwords($member['name']) }}
                                    </h3>
                                    <p class="text-[10px] sm:text-[11px] font-medium text-slate-400 mt-0.5">Member task progress</p>
                                </div>
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-xs text-slate-600 border border-slate-200 uppercase">
                                    {{ substr($member['name'] ?? 'U', 0, 2) }}
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-4 sm:flex-row sm:gap-6">
                                <div class="relative flex h-16 w-16 sm:h-20 sm:w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F1F5F9" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent" stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" class="transition-all duration-700" />
                                    </svg>
                                    <span class="absolute text-xs sm:text-sm font-black text-[#001b4d] font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <div class="flex items-baseline justify-center gap-1 sm:justify-start">
                                        <span class="text-xl sm:text-2xl font-black tracking-tight text-[#001b4d] font-mono">{{ $member['completed'] }}</span>
                                        <span class="text-[11px] font-bold text-slate-400">/ {{ $member['total'] }} Tasks</span>
                                    </div>
                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Completed</p>

                                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full transition-all duration-700" style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-8 sm:p-10 text-center text-xs sm:text-sm font-semibold text-slate-400/90">
                            No performance data available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Groups Overview Section --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm ring-1 ring-slate-100/50">
                <div class="border-b border-slate-100 px-4 sm:px-6 py-4 bg-slate-50/50">
                    <h2 class="text-xs sm:text-sm font-black uppercase tracking-widest text-[#001b4d]">Groups Overview</h2>
                    <p class="mt-0.5 text-[11px] sm:text-xs font-medium text-slate-400">
                        {{ $isAdmin ? 'Group-wise task status overview' : 'Your group-wise task status overview' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
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
                            <div class="relative flex h-full flex-col justify-between rounded-xl border border-slate-150 bg-white p-4 sm:p-5 transition-all duration-300 hover:border-blue-300/80 hover:shadow-md hover:shadow-blue-500/5">
                                <div>
                                    <div class="mb-4 sm:mb-6 flex items-start justify-between gap-2">
                                        <div class="truncate">
                                            <h3 class="truncate text-sm sm:text-base font-bold text-slate-800 transition-colors group-hover:text-blue-600">
                                                {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                            </h3>
                                            <p class="text-[10px] sm:text-[11px] font-medium text-slate-400 mt-0.5">Group task status summary</p>
                                        </div>
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors border border-slate-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center gap-4 sm:flex-row sm:gap-5">
                                        <div class="relative flex h-16 w-16 sm:h-20 sm:w-20 shrink-0 items-center justify-center">
                                            <svg class="h-full w-full -rotate-90 drop-shadow-sm" viewBox="0 0 100 100">
                                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#F1F5F9" stroke-width="9" />
                                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" class="transition-all duration-700" />
                                            </svg>
                                            <span class="absolute text-xs sm:text-sm font-black text-[#001b4d] font-mono">{{ $percentage }}%</span>
                                        </div>

                                        <div class="w-full space-y-2 text-[11px] sm:text-xs font-bold text-slate-500">
                                            <div class="flex justify-between items-center border-b border-slate-100 pb-1.5">
                                                <span class="flex items-center gap-2 font-semibold text-slate-400"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Pending</span>
                                                <span class="font-black text-slate-750 font-mono">{{ $group['pending'] ?? 0 }}</span>
                                            </div>

                                            <div class="flex justify-between items-center border-b border-slate-100 pb-1.5">
                                                <span class="flex items-center gap-2 font-semibold text-slate-400"><span class="h-2 w-2 rounded-full bg-amber-400"></span>In Progress</span>
                                                <span class="font-black text-slate-750 font-mono">{{ $group['pending'] ?? 0 }}</span>
                                            </div>

                                            <div class="flex justify-between items-center pt-0.5">
                                                <span class="flex items-center gap-2 font-semibold text-slate-400"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Completed</span>
                                                <span class="font-black text-slate-750 font-mono">{{ $group['completed'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full transition-all duration-700" style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}"></div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-8 sm:p-10 text-center text-xs sm:text-sm font-semibold text-slate-400/90">
                            No group data available.
                        </div>
                    @endforelse
                </div>
            </section>

        </main>
    </div>
</div>
