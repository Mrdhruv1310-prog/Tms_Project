<div>
    @php
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        $role = strtolower($authUser->role ?? 'employee');

        $isSuperAdmin = $role === 'super-admin';
        $isAdmin = $role === 'admin';
        $isHr = $role === 'hr';
        $isManager = $role === 'manager';
        $isEmployee = in_array($role, ['employee', 'user'], true);

        $hasFullView = $isSuperAdmin || $isAdmin || $isHr || $isManager;

        $userName = $authUser
            ? (trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? '')) ?:
            $authUser->name ?? 'User')
            : 'User';

        $roleLabel = match ($role) {
            'super-admin' => 'Super Admin',
            'admin' => 'Admin',
            'hr' => 'HR',
            'manager' => 'Manager',
            'employee' => 'Employee',
            default => ucfirst($role),
        };

        $visibleCategories = collect($categories ?? []);
        $visibleTeam = collect($team ?? []);
        $visibleGroups = collect($groups ?? []);
    @endphp

    <div
        class="min-h-screen bg-gradient-to-br from-[rgb(230,242,234)] via-[rgb(240,245,248)] to-[rgb(225,238,247)] antialiased selection:bg-[rgb(7,139,221)] selection:text-white text-gray-800">

        <main
            class="scrollcontainer h-auto px-2.5 sm:px-6 md:px-8 pb-12 pt-14 sm:pt-24 md:ml-16 lg:px-10 max-w-[1750px] mx-auto transition-all duration-300"
            x-data="{ showFilter: false, showAllCategories: false }">

            {{-- Executive Header --}}
            <div
                class="mb-4 sm:mb-8 overflow-hidden rounded-xl sm:rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_10px_25px_rgba(0,0,0,0.05)] ring-1 ring-black/5 p-3.5 sm:p-8 relative">
                <div
                    class="absolute -top-32 -left-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(114,204,74)]/15 rounded-full blur-3xl pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-32 -right-32 w-64 sm:w-96 h-64 sm:h-96 bg-[rgb(7,139,221)]/15 rounded-full blur-3xl pointer-events-none">
                </div>

                <div class="flex flex-row items-center justify-between gap-2.5 relative z-10">
                    <div class="space-y-1 min-w-0">
                        <div
                            class="flex items-center gap-2 text-[9px] sm:text-[11px] font-semibold uppercase tracking-wider text-gray-600 overflow-x-auto py-0.5">
                            <span
                                class="text-gray-800 font-bold bg-white/90 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md sm:rounded-xl border border-gray-200/80 shadow-xs shrink-0 truncate">
                                Dashboard
                            </span>
                        </div>

                        <h1 class="text-base sm:text-3xl font-bold tracking-tight text-gray-900 truncate">
                            <span>Welcome back, {{ $userName }}</span>
                        </h1>
                    </div>

                    <button type="button" @click="showFilter = !showFilter"
                        class="flex h-9 w-9 sm:h-12 sm:w-12 items-center justify-center rounded-xl sm:rounded-2xl bg-sky-50 text-[rgb(7,139,221)] border border-sky-100/80 shadow-xs hover:bg-[rgb(7,139,221)] hover:text-white transition-all duration-300 active:scale-95 shrink-0"
                        title="Toggle Timeline Filter">
                        <svg class="h-4 w-4 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.447.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Timeline Filter Drawer --}}
            <div x-show="showFilter" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
                class="mb-4 sm:mb-8 rounded-xl sm:rounded-3xl border border-white/80 bg-white/95 backdrop-blur-2xl shadow-[0_10px_25px_rgba(0,0,0,0.08)] ring-1 ring-black/5 p-3.5 sm:p-6 transition-all"
                style="display: none;">

                <div class="flex flex-col gap-3.5 max-w-xl mx-auto sm:max-w-none">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-xl sm:rounded-2xl bg-sky-50 text-[rgb(7,139,221)] border border-sky-100/80 shrink-0">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.447.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-800">TASK
                                TIMELINE FILTER</h2>
                            <p class="text-[10px] sm:text-[11px] font-medium text-gray-500">Filter records by creation
                                timeline</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                        <div class="space-y-0.5">
                            <label
                                class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-gray-400 px-1">RANGE</label>
                            <select wire:model.live="selectedTimeRange"
                                class="w-full text-xs font-semibold text-gray-700 bg-gray-50/90 hover:bg-white border border-gray-200 rounded-lg sm:rounded-xl py-2 px-2.5 sm:px-3 focus:border-[rgb(7,139,221)] focus:ring-2 focus:ring-[rgb(7,139,221)]/20 transition-all cursor-pointer">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="this_week">This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="this_year">This Year</option>
                            </select>
                        </div>

                        <div class="space-y-0.5">
                            <label
                                class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-gray-400 px-1">YEAR</label>
                            <select wire:model.live="selectedYear"
                                class="w-full text-xs font-semibold text-gray-700 bg-gray-50/90 hover:bg-white border border-gray-200 rounded-lg sm:rounded-xl py-2 px-2.5 sm:px-3 focus:border-[rgb(7,139,221)] focus:ring-2 focus:ring-[rgb(7,139,221)]/20 transition-all cursor-pointer">
                                <option value="">Select Year</option>
                                @foreach (range(date('Y'), 2020) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-0.5">
                            <label
                                class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-gray-400 px-1">MONTH</label>
                            <select wire:model.live="selectedMonth"
                                class="w-full text-xs font-semibold text-gray-700 bg-gray-50/90 hover:bg-white border border-gray-200 rounded-lg sm:rounded-xl py-2 px-2.5 sm:px-3 focus:border-[rgb(7,139,221)] focus:ring-2 focus:ring-[rgb(7,139,221)]/20 transition-all cursor-pointer">
                                <option value="">Select Month</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-0.5" wire:ignore>
                            <label
                                class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-gray-400 px-1">SPECIFIC
                                DATE</label>
                            <div class="relative" x-data="{
                                picker: null,
                                init() {
                                    this.picker = flatpickr(this.$refs.dateInput, {
                                        dateFormat: 'Y-m-d',
                                        disableMobile: 'true',
                                        allowInput: true,
                                        clickOpens: true,
                                        onChange: (selectedDates, dateStr) => {
                                            $wire.set('selectedDate', dateStr);
                                        }
                                    });
                                }
                            }">
                                <input x-ref="dateInput" type="text" wire:model.live="selectedDate"
                                    placeholder="dd-mm-yyyy"
                                    class="w-full text-xs font-semibold text-gray-700 bg-gray-50/90 hover:bg-white border border-gray-200 rounded-lg sm:rounded-xl py-2 pr-7 pl-2.5 sm:px-3 focus:border-[rgb(7,139,221)] focus:ring-2 focus:ring-[rgb(7,139,221)]/20 transition-all cursor-pointer"
                                    @click="picker && picker.open()" @focus="picker && picker.open()">

                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 cursor-pointer text-gray-400"
                                    @click="picker && picker.open()">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="button" wire:click="clearDateFilters"
                            class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-bold text-gray-600 hover:text-rose-600 bg-gray-100 hover:bg-rose-50 rounded-lg sm:rounded-xl transition-all border border-gray-200 hover:border-rose-200 active:scale-98">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Reset Filters</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Summary Cards Grid --}}
            <div class="mb-6 sm:mb-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
                @php
                    $dateParams = $this->getDateQueryParams();
                    $queryString = !empty($dateParams) ? '?' . http_build_query($dateParams) : '';
                @endphp

                @foreach ($labels as $label)
                    @php
                        $title = strtolower($label['title']);
                        $statusKey = $label['status'] ?? 'total';

                        $baseUrl = match ($statusKey) {
                            'pending' => url('/tasks/pending'),
                            'in_progress' => url('/tasks/in_progress'),
                            'completed' => url('/tasks/completed'),
                            default => url('/tasks'),
                        };

                        $redirectUrl = $baseUrl . $queryString;

                        $cardStyle = match ($title) {
                            'pending'
                                => 'bg-white/95 border-rose-300/80 text-gray-900 shadow-[0_10px_25px_rgba(244,63,94,0.1)] hover:border-rose-400',
                            'in progress'
                                => 'bg-white/95 border-amber-300/80 text-gray-900 shadow-[0_10px_25px_rgba(245,158,11,0.1)] hover:border-amber-400',
                            'completed'
                                => 'bg-emerald-50/50 border-emerald-300/80 text-gray-900 shadow-[0_10px_25px_rgba(16,185,129,0.1)] hover:border-emerald-400',
                            'total'
                                => 'bg-white/95 border-[rgb(7,139,221)]/40 text-gray-900 shadow-[0_10px_25px_rgba(7,139,221,0.1)] hover:border-[rgb(7,139,221)]',
                            default
                                => 'bg-white/95 border-gray-200 text-gray-900 shadow-[0_10px_25px_rgba(0,0,0,0.05)]',
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
                                => 'bg-gradient-to-tr from-rose-600 to-rose-400 text-white shadow-md shadow-rose-500/30',
                            'in progress'
                                => 'bg-gradient-to-tr from-amber-600 to-amber-400 text-white shadow-md shadow-amber-500/30',
                            'completed'
                                => 'bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white shadow-md shadow-emerald-500/30',
                            'total'
                                => 'bg-gradient-to-tr from-[rgb(7,139,221)] to-cyan-400 text-white shadow-md shadow-[rgb(7,139,221)]/30',
                            default => 'bg-gray-800 text-white shadow-md shadow-gray-500/30',
                        };

                        $countColor = match ($title) {
                            'pending' => 'text-rose-600',
                            'in progress' => 'text-amber-600',
                            'completed' => 'text-emerald-600',
                            'total' => 'text-[rgb(7,139,221)]',
                            default => 'text-gray-900',
                        };
                    @endphp

                    <a href="{{ $redirectUrl }}" wire:navigate
                        class="group relative block overflow-hidden rounded-xl sm:rounded-3xl {{ $cardStyle }} backdrop-blur-xl p-3.5 sm:p-6 border-2 ring-1 ring-white/80 transition-all duration-300 hover:-translate-y-0.5 sm:hover:-translate-y-1 hover:shadow-xl">

                        <div class="relative z-10 flex items-start justify-between gap-2.5">
                            <div class="space-y-1.5 sm:space-y-3">
                                <p
                                    class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-gray-500 flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-80 animate-pulse"></span>
                                    {{ $label['title'] }}
                                </p>

                                <h2
                                    class="text-xl sm:text-4xl font-extrabold tracking-tight font-mono {{ $countColor }}">
                                    {{ $label['count'] }}
                                </h2>

                                <div
                                    class="inline-flex items-center gap-1 px-2 py-0.5 sm:px-3 sm:py-1 text-[9px] sm:text-[11px] rounded-full border backdrop-blur-md shadow-xs transition-transform group-hover:scale-105 {{ $badgeStyle }}">
                                    <svg class="h-2.5 w-2.5 sm:h-3.5 sm:w-3.5 opacity-90" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Compared to last week</span>
                                </div>
                            </div>

                            <div
                                class="flex h-9 w-9 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-lg sm:rounded-2xl {{ $iconBg }} backdrop-blur-xl transition-all duration-300 group-hover:rotate-6 group-hover:scale-105">
                                @if ($title === 'pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($title === 'in progress')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                                    </svg>
                                @elseif ($title === 'completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-6 sm:w-6"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 022-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Category Analytics Section --}}
            <section
                class="mb-6 sm:mb-12 overflow-hidden rounded-xl sm:rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_10px_25px_rgba(0,0,0,0.06)] ring-1 ring-black/5">
                <div
                    class="flex flex-col gap-2.5 border-b border-gray-100 p-3.5 sm:px-8 sm:py-6 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-gray-50/50 via-[rgb(7,139,221)]/5 to-transparent">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[rgb(7,139,221)] animate-pulse"></span>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700">Category
                                Analytics Report</h2>
                        </div>
                        <p class="text-[10px] sm:text-xs font-medium text-gray-500">
                            {{ $isAdmin ? 'Category-wise deep task completion progress across enterprise' : 'Your assigned category-wise task progress metrics' }}
                        </p>
                    </div>

                    @if (count($visibleCategories) > 3)
                        <button type="button" @click="showAllCategories = !showAllCategories"
                            class="group inline-flex items-center justify-center gap-2 rounded-lg sm:rounded-xl border border-gray-200/80 bg-white px-3.5 py-1.5 sm:px-4 sm:py-2 text-xs font-semibold text-gray-700 shadow-xs transition-all hover:bg-[rgb(7,139,221)] hover:text-white hover:border-[rgb(7,139,221)] active:scale-95 w-full sm:w-auto">
                            <span x-text="showAllCategories ? 'Show Top Categories' : 'View All Categories'">View All
                                Categories</span>
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 transition-transform duration-300"
                                :class="{ 'rotate-90': showAllCategories }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-5 p-3.5 sm:p-8">
                    @forelse ($visibleCategories as $index => $category)
                        @php
                            $percentage =
                                isset($category['percentage']) && is_numeric($category['percentage'])
                                    ? (float) $category['percentage']
                                    : 0;
                            $circumference = 251.2;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            $strokeColor = match (true) {
                                $percentage >= 71 => '#EF4444',
                                $percentage >= 31 => '#F59E0B',
                                default => '#EF4444',
                            };
                        @endphp

                        <div class="group relative rounded-lg sm:rounded-2xl border border-gray-200/80 bg-white/90 transition-all duration-300 p-3.5 sm:p-6 backdrop-blur-md cursor-pointer hover:border-[rgb(7,139,221)]/50 hover:shadow-lg active:scale-98"
                            x-show="showAllCategories || {{ $index }} < 3"
                            wire:click="filterCategoryPendingTasks('{{ addslashes($category['title']) }}')">

                            <div class="mb-3 sm:mb-6 flex items-start justify-between gap-2.5">
                                <div class="truncate">
                                    <h3
                                        class="truncate text-xs sm:text-base font-semibold text-gray-800 group-hover:text-[rgb(7,139,221)]">
                                        {{ ucwords($category['title']) }}
                                    </h3>
                                    <p class="text-[9px] sm:text-[11px] font-medium text-gray-500 mt-0.5">Pending tasks
                                        status metric</p>
                                </div>
                                <span
                                    class="h-7 w-7 sm:h-10 sm:w-10 rounded-lg sm:rounded-xl bg-gray-50 border border-gray-200/80 flex items-center justify-center text-gray-500 group-hover:bg-[rgb(7,139,221)] group-hover:text-white transition-all shrink-0">
                                    <svg class="h-3.5 w-3.5 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </span>
                            </div>

                            <div class="flex flex-row items-center gap-3 sm:gap-6">
                                <div
                                    class="relative flex h-14 w-14 sm:h-20 sm:w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-xs" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="#F3F4F6" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $offset }}"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <span
                                        class="absolute text-[11px] sm:text-sm font-bold text-gray-800 font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-left">
                                    <div class="flex items-baseline justify-start gap-1">
                                        <span
                                            class="text-lg sm:text-2xl font-bold tracking-tight text-rose-600 font-mono">{{ $category['pending'] ?? 0 }}</span>
                                        <span class="text-[10px] sm:text-xs font-semibold text-gray-500">/
                                            {{ $category['total'] ?? 0 }} Tasks</span>
                                    </div>
                                    <p class="text-[9px] font-semibold text-gray-500 mt-0.5 uppercase tracking-wider">
                                        Pending Output</p>

                                    <div
                                        class="mt-2 sm:mt-4 h-1.5 sm:h-2 w-full overflow-hidden rounded-full bg-gray-100 p-0.5 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000 shadow-xs"
                                            style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-6 sm:p-12 text-center text-xs sm:text-sm font-medium text-gray-500">
                            No category data available at the moment.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Team Performance Section --}}
            <section
                class="mb-6 sm:mb-12 overflow-hidden rounded-xl sm:rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_10px_25px_rgba(0,0,0,0.06)] ring-1 ring-black/5"
                x-data="{ showAllTeam: false }">
                <div
                    class="flex flex-col gap-2.5 border-b border-gray-100 p-3.5 sm:px-8 sm:py-6 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-gray-50/50 via-[rgb(7,139,221)]/5 to-transparent">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-[rgb(7,139,221)] animate-pulse"></span>
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700">
                                {{ $isAdmin ? 'Team Performance Intelligence' : 'My Personal Performance' }}
                            </h2>
                        </div>
                        <p class="text-[10px] sm:text-xs font-medium text-gray-500">
                            {{ $isAdmin ? 'Detailed user-wise execution summary and efficiency ratings' : 'Your assigned execution and workflow summary' }}
                        </p>
                    </div>

                    @if (count($visibleTeam) > 3)
                        <button type="button" @click="showAllTeam = !showAllTeam"
                            class="group inline-flex items-center justify-center gap-2 rounded-lg sm:rounded-xl border border-gray-200/80 bg-white px-3.5 py-1.5 sm:px-4 sm:py-2 text-xs font-semibold text-gray-700 shadow-xs transition-all hover:bg-[rgb(7,139,221)] hover:text-white hover:border-[rgb(7,139,221)] active:scale-95 w-full sm:w-auto">
                            <span x-text="showAllTeam ? 'Show Top Performance' : 'View All Performance'">View All
                                Performance</span>
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 transition-transform duration-300"
                                :class="{ 'rotate-90': showAllTeam }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('teamPerformance') }}" wire:navigate
                            class="group inline-flex items-center justify-center gap-2 rounded-lg sm:rounded-xl border border-gray-200/80 bg-white px-3.5 py-1.5 sm:px-4 sm:py-2 text-xs font-semibold text-gray-700 shadow-xs transition-all hover:bg-[rgb(7,139,221)] hover:text-white hover:border-[rgb(7,139,221)] active:scale-95 w-full sm:w-auto">
                            <span>View All Performance</span>
                            <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 transition-transform group-hover:translate-x-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-5 p-3.5 sm:p-8">
                    @forelse ($visibleTeam as $index => $member)
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

                        <div x-show="showAllTeam || {{ $index }} < 3"
                            wire:click="navigateToUserPendingTasks({{ $member['id'] }})"
                            class="group relative rounded-lg sm:rounded-2xl border border-gray-200/80 bg-white/90 backdrop-blur-md p-3.5 sm:p-6 transition-all duration-300 hover:border-[rgb(7,139,221)]/50 hover:shadow-lg cursor-pointer">

                            <div class="mb-3 sm:mb-6 flex items-center justify-between gap-2.5">
                                <div class="truncate">
                                    <h3
                                        class="truncate text-xs sm:text-base font-semibold text-gray-800 group-hover:text-[rgb(7,139,221)]">
                                        {{ ucwords($member['name']) }}
                                        @if ((int) $member['id'] === (int) Auth::id())
                                            <span
                                                class="ml-1 text-[10px] bg-sky-100 text-[rgb(7,139,221)] px-2 py-0.5 rounded-full font-bold">You</span>
                                        @endif
                                    </h3>
                                    <p class="text-[9px] sm:text-[11px] font-medium text-gray-500 mt-0.5">Member task
                                        progress</p>
                                </div>
                                <div
                                    class="flex h-8 w-8 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-gray-50 font-bold text-[10px] sm:text-xs text-[rgb(7,139,221)] border border-gray-200/80 shadow-xs uppercase tracking-wider">
                                    {{ substr($member['name'] ?? 'U', 0, 2) }}
                                </div>
                            </div>

                            <div class="flex flex-row items-center gap-3 sm:gap-6">
                                <div
                                    class="relative flex h-14 w-14 sm:h-20 sm:w-20 shrink-0 items-center justify-center">
                                    <svg class="h-full w-full -rotate-90 drop-shadow-xs" viewBox="0 0 100 100">
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="#F3F4F6" stroke-width="9" />
                                        <circle r="40" cx="50" cy="50" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round"
                                            stroke-dasharray="{{ $circumference }}"
                                            stroke-dashoffset="{{ $offset }}"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <span
                                        class="absolute text-[11px] sm:text-sm font-bold text-gray-800 font-mono">{{ $percentage }}%</span>
                                </div>

                                <div class="w-full text-left">
                                    <div class="flex items-baseline justify-start gap-1">
                                        <span
                                            class="text-lg sm:text-2xl font-bold tracking-tight text-rose-600 font-mono">{{ $member['pending'] ?? 0 }}</span>
                                        <span class="text-[10px] sm:text-xs font-semibold text-gray-500">/
                                            {{ $member['total'] ?? 0 }} Tasks</span>
                                    </div>
                                    <p class="text-[9px] font-semibold text-gray-500 mt-0.5 uppercase tracking-wider">
                                        Pending Output</p>

                                    <div
                                        class="mt-2 sm:mt-4 h-1.5 sm:h-2 w-full overflow-hidden rounded-full bg-gray-100 p-0.5 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000 shadow-xs"
                                            style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-6 sm:p-12 text-center text-xs sm:text-sm font-medium text-gray-500">
                            No performance metrics available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Groups Overview Section --}}
            <section
                class="overflow-hidden rounded-xl sm:rounded-3xl border border-white/80 bg-white/80 backdrop-blur-xl shadow-[0_10px_25px_rgba(0,0,0,0.06)] ring-1 ring-black/5">
                <div
                    class="border-b border-gray-100 p-3.5 sm:px-8 sm:py-6 bg-gradient-to-r from-gray-50/50 via-[rgb(7,139,221)]/5 to-transparent space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-[rgb(7,139,221)] animate-pulse"></span>
                        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-700">Groups Status
                            Overview</h2>
                    </div>
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500">
                        {{ $isAdmin ? 'Group-wise workflow status & granular task distribution overview' : 'Your group-wise workflow status & progress overview' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5 p-3.5 sm:p-8">
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

                        <div wire:click="navigateToGroupTasks({{ $group['id'] }})"
                            class="relative flex h-full flex-col justify-between rounded-lg sm:rounded-2xl border border-gray-200/80 bg-white/90 backdrop-blur-md p-3.5 sm:p-6 transition-all duration-300 hover:border-[rgb(7,139,221)]/50 hover:shadow-lg cursor-pointer group">
                            <div>
                                <div class="mb-3 sm:mb-6 flex items-start justify-between gap-2.5">
                                    <div class="truncate">
                                        <h3
                                            class="truncate text-xs sm:text-base font-semibold text-gray-800 group-hover:text-[rgb(7,139,221)]">
                                            {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                        </h3>
                                        <p class="text-[9px] sm:text-[11px] font-medium text-gray-500 mt-0.5">Group
                                            task analytics summary</p>
                                    </div>
                                </div>

                                <div class="flex flex-row items-center gap-3 sm:gap-6">
                                    <div
                                        class="relative flex h-14 w-14 sm:h-20 sm:w-20 shrink-0 items-center justify-center">
                                        <svg class="h-full w-full -rotate-90 drop-shadow-xs" viewBox="0 0 100 100">
                                            <circle r="40" cx="50" cy="50" fill="transparent"
                                                stroke="#F3F4F6" stroke-width="9" />
                                            <circle r="40" cx="50" cy="50" fill="transparent"
                                                stroke="{{ $strokeColor }}" stroke-width="9" stroke-linecap="round"
                                                stroke-dasharray="{{ $circumference }}"
                                                stroke-dashoffset="{{ $offset }}"
                                                class="transition-all duration-1000 ease-out" />
                                        </svg>
                                        <span
                                            class="absolute text-[11px] sm:text-sm font-bold text-gray-800 font-mono">{{ $percentage }}%</span>
                                    </div>

                                    <div class="w-full space-y-1 text-xs font-semibold text-gray-600">
                                        <div class="flex justify-between items-center border-b border-gray-100 pb-1">
                                            <span
                                                class="flex items-center gap-1 text-[10px] sm:text-[11px] font-medium text-gray-500"><span
                                                    class="h-1.5 w-1.5 rounded-full bg-rose-500 shadow-xs"></span>Pending</span>
                                            <span
                                                class="font-bold text-gray-800 font-mono text-xs">{{ $group['pending'] ?? 0 }}</span>
                                        </div>

                                        <div class="flex justify-between items-center border-b border-gray-100 pb-1">
                                            <span
                                                class="flex items-center gap-1 text-[10px] sm:text-[11px] font-medium text-gray-500"><span
                                                    class="h-1.5 w-1.5 rounded-full bg-amber-400 shadow-xs"></span>In
                                                Progress</span>
                                            <span
                                                class="font-bold text-gray-800 font-mono text-xs">{{ $group['in_progress'] ?? 0 }}</span>
                                        </div>

                                        <div class="flex justify-between items-center pt-0.5">
                                            <span
                                                class="flex items-center gap-1 text-[10px] sm:text-[11px] font-medium text-gray-500"><span
                                                    class="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-xs"></span>Completed</span>
                                            <span
                                                class="font-bold text-gray-800 font-mono text-xs">{{ $group['completed'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-3 sm:mt-6 h-1.5 sm:h-2 w-full overflow-hidden rounded-full bg-gray-100 p-0.5 shadow-inner">
                                <div class="h-full rounded-full transition-all duration-1000 shadow-xs"
                                    style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-6 sm:p-12 text-center text-xs sm:text-sm font-medium text-gray-500">
                            No groups data available.
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</div>
