<div>
    @php
        $authUser = Auth::user();
        $isAdmin = $authUser->role === 'admin';
        $isUser = $authUser->role === 'user';

        $visibleCategories = collect($categories ?? []);
        $visibleTeam = collect($team ?? []);
        $visibleGroups = collect($groups ?? []);
    @endphp

    <div class="min-h-screen bg-[#eef3fb] antialiased">
        <main class="scrollcontainer h-auto px-3 pb-16 pt-14 sm:px-4 md:ml-16 lg:px-6">

            {{-- Header --}}
            <div class="mb-6 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <span>Home</span>
                        <span>/</span>
                        <span class="text-[#001b4d]">Dashboard</span>
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-[#001b4d] sm:text-3xl">
                        Hi, welcome back!
                    </h1>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        {{ $isAdmin ? 'Welcome back, here is your team task overview.' : 'Welcome back, here is your assigned task overview.' }}
                    </p>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($labels as $label)
                    @php
                        $title = strtolower($label['title']);

                        $cardGradient = match ($title) {
                            'pending' => 'from-[#0067f4] to-[#6aaeff]',
                            'in progress' => 'from-[#f5365c] to-[#fb7896]',
                            'completed' => 'from-[#00a06a] to-[#45d6aa]',
                            'total' => 'from-[#fb7b24] to-[#ffb167]',
                            default => 'from-[#0067f4] to-[#6aaeff]',
                        };
                    @endphp

                    <div
                        class="relative min-h-[145px] overflow-hidden rounded-xl bg-gradient-to-r {{ $cardGradient }} p-5 text-white shadow-sm">
                        <div class="relative z-10 flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide sm:text-sm">
                                    {{ $label['title'] }}
                                </p>

                                <h2 class="mt-4 text-2xl font-black tracking-tight sm:text-3xl">
                                    {{ $label['count'] }}
                                </h2>

                                <p class="mt-2 text-xs font-medium text-white/85 sm:text-sm">
                                    Compared to last week
                                </p>
                            </div>

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 backdrop-blur sm:h-10 sm:w-10">
                                @if ($title === 'pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                                        <path
                                            d="M7.5 3a.5.5 0 0 1 .5.5V9l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                                    </svg>
                                @elseif ($title === 'in progress')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9" />
                                        <path fill-rule="evenodd"
                                            d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z" />
                                    </svg>
                                @elseif ($title === 'completed')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <svg class="absolute bottom-0 left-0 h-14 w-full opacity-35 sm:h-16" viewBox="0 0 400 80"
                            preserveAspectRatio="none">
                            <path
                                d="M0,60 L25,48 L50,62 L75,58 L100,65 L125,40 L150,25 L175,35 L200,48 L225,34 L250,42 L275,62 L300,52 L325,60 L350,35 L375,58 L400,42 L400,80 L0,80 Z"
                                fill="white"></path>
                        </svg>
                    </div>
                @endforeach
            </div>

            {{-- Category Report --}}
            <section class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-4 border-b border-slate-100 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="text-base font-black uppercase text-[#001b4d] sm:text-lg">Category Report</h2>
                        <p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">
                            {{ $isAdmin ? 'Category-wise task completion progress' : 'Your assigned category-wise task progress' }}
                        </p>
                    </div>

                    <a href="{{ route('categoryReport') }}" wire:navigate
                        class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-2 text-xs font-bold text-[#001b4d] shadow-sm hover:bg-[#f3f7ff] sm:w-auto">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 md:grid-cols-2 2xl:grid-cols-3">
                    @forelse ($visibleCategories as $category)
                        @php
                            $circumference = 565.48;
                            $percentage =
                                isset($category['percentage']) && is_numeric($category['percentage'])
                                    ? (float) $category['percentage']
                                    : 0;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            if ($percentage >= 71) {
                                $dotColor = 'bg-green-500';
                                $strokeColor = '#22c55e';
                                $badgeBg = 'bg-green-50 text-green-700';
                            } elseif ($percentage >= 31) {
                                $dotColor = 'bg-yellow-400';
                                $strokeColor = '#facc15';
                                $badgeBg = 'bg-yellow-50 text-yellow-700';
                            } else {
                                $dotColor = 'bg-red-500';
                                $strokeColor = '#ef4444';
                                $badgeBg = 'bg-red-50 text-red-700';
                            }
                        @endphp

                        <div
                            class="relative rounded-xl border border-slate-200 bg-[#f8fbff] p-4 transition hover:bg-white hover:shadow-md sm:p-5">
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-semibold text-slate-900 sm:text-base">
                                        {{ ucwords($category['title']) }}
                                    </h3>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">
                                        Task completion status
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 xs:flex-row sm:flex-row sm:items-center">
                                <div class="mx-auto h-20 w-20 shrink-0 sm:mx-0">
                                    <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                        xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                        <circle r="90" cx="100" cy="100" fill="transparent" stroke="#e5e7eb"
                                            stroke-width="16" stroke-dasharray="565.48" stroke-dashoffset="0" />
                                        <circle r="90" cx="100" cy="100" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="16" stroke-linecap="round"
                                            stroke-dasharray="565.48" stroke-dashoffset="{{ $offset }}" />
                                        <text x="72px" y="108px" fill="#001b4d" font-size="30px" font-weight="bold"
                                            style="transform: rotate(90deg) translate(0px, -196px)">
                                            {{ $percentage }}%
                                        </text>
                                    </svg>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <p class="text-2xl font-black text-[#001b4d]">
                                        {{ $category['completed'] }}/{{ $category['total'] }}
                                    </p>
                                    <p class="text-xs font-bold text-slate-500">Completed</p>

                                    <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full"
                                            style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm font-semibold text-slate-500">
                            No category data available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Team / My Performance --}}
            <section class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div
                    class="flex flex-col gap-4 border-b border-slate-100 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="text-base font-black uppercase text-[#001b4d] sm:text-lg">
                            {{ $isAdmin ? 'Team Performance' : 'My Performance' }}
                        </h2>

                        <p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">
                            {{ $isAdmin ? 'User-wise task completion summary' : 'Your assigned task completion summary' }}
                        </p>
                    </div>

                    <a href="{{ route('teamPerformance') }}" wire:navigate
                        class="inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-2 text-xs font-bold text-[#001b4d] shadow-sm hover:bg-[#f3f7ff] sm:w-auto">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                    @forelse ($visibleTeam as $member)
                        @php
                            $circumference = 565.48;
                            $percentage =
                                isset($member['percentage']) && is_numeric($member['percentage'])
                                    ? (float) $member['percentage']
                                    : 0;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            if ($percentage >= 71) {
                                $dotColor = 'bg-green-500';
                                $strokeColor = '#22c55e';
                                $badgeBg = 'bg-green-50 text-green-700';
                            } elseif ($percentage >= 31) {
                                $dotColor = 'bg-yellow-400';
                                $strokeColor = '#facc15';
                                $badgeBg = 'bg-yellow-50 text-yellow-700';
                            } else {
                                $dotColor = 'bg-red-500';
                                $strokeColor = '#ef4444';
                                $badgeBg = 'bg-red-50 text-red-700';
                            }
                        @endphp

                        <div
                            class="relative rounded-xl border border-slate-200 bg-[#f8fbff] p-4 transition hover:bg-white hover:shadow-md sm:p-5">
                            <div class="mb-5 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-semibold text-slate-900 sm:text-base">
                                        {{ ucwords($member['name']) }}
                                    </h3>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">
                                        Member task progress
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 xs:flex-row sm:flex-row sm:items-center">
                                <div class="mx-auto h-20 w-20 shrink-0 sm:mx-0">
                                    <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                        xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                        <circle r="90" cx="100" cy="100" fill="transparent"
                                            stroke="#e5e7eb" stroke-width="16" stroke-dasharray="565.48"
                                            stroke-dashoffset="0" />
                                        <circle r="90" cx="100" cy="100" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="16" stroke-linecap="round"
                                            stroke-dasharray="565.48" stroke-dashoffset="{{ $offset }}" />
                                        <text x="72px" y="108px" fill="#001b4d" font-size="30px" font-weight="bold"
                                            style="transform: rotate(90deg) translate(0px, -196px)">
                                            {{ $percentage }}%
                                        </text>
                                    </svg>
                                </div>

                                <div class="w-full text-center sm:text-left">
                                    <p class="text-2xl font-black text-[#001b4d]">
                                        {{ $member['completed'] }}/{{ $member['total'] }}
                                    </p>
                                    <p class="text-xs font-bold text-slate-500">Completed</p>

                                    <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full"
                                            style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm font-semibold text-slate-500">
                            No performance data available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Groups Overview --}}
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-5 sm:px-6">
                    <h2 class="text-base font-black uppercase text-[#001b4d] sm:text-lg">Groups Overview</h2>
                    <p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">
                        {{ $isAdmin ? 'Group-wise task status overview' : 'Your group-wise task status overview' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:p-6 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                    @forelse ($visibleGroups as $group)
                        @php
                            $circumference = 565.48;
                            $percentage =
                                isset($group['percentage']) && is_numeric($group['percentage'])
                                    ? (float) $group['percentage']
                                    : 0;
                            $offset = $circumference - ($circumference * $percentage) / 100;

                            if ($percentage >= 71) {
                                $dotColor = 'bg-green-500';
                                $strokeColor = '#22c55e';
                            } elseif ($percentage >= 31) {
                                $dotColor = 'bg-yellow-400';
                                $strokeColor = '#facc15';
                            } else {
                                $dotColor = 'bg-red-500';
                                $strokeColor = '#ef4444';
                            }
                        @endphp

                        <a href="{{ route('group.details', ['id' => $group['id'] ?? 0]) }}" wire:navigate
                            class="block h-full">
                            <div
                                class="relative h-full rounded-xl border border-slate-200 bg-[#f8fbff] p-4 transition hover:bg-white hover:shadow-md sm:p-5">
                                <div class="mb-5 flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-semibold text-slate-900 sm:text-base">
                                            {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                        </h3>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">
                                            Group task overview
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <div class="mx-auto h-20 w-20 shrink-0 sm:mx-0">
                                        <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                            xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                            <circle r="90" cx="100" cy="100" fill="transparent"
                                                stroke="#e5e7eb" stroke-width="16" stroke-dasharray="565.48"
                                                stroke-dashoffset="0" />
                                            <circle r="90" cx="100" cy="100" fill="transparent"
                                                stroke="{{ $strokeColor }}" stroke-width="16"
                                                stroke-linecap="round" stroke-dasharray="565.48"
                                                stroke-dashoffset="{{ $offset }}" />
                                            <text x="72px" y="108px" fill="#001b4d" font-size="30px"
                                                font-weight="bold"
                                                style="transform: rotate(90deg) translate(0px, -196px)">
                                                {{ $percentage }}%
                                            </text>
                                        </svg>
                                    </div>

                                    <div class="w-full space-y-2 text-xs font-bold text-slate-600">
                                        <div class="flex justify-between gap-3">
                                            <span>Pending</span>
                                            <span
                                                class="text-[#001b4d]">{{ $group['pending'] ?? 0 }}/{{ $group['total'] ?? 0 }}</span>
                                        </div>

                                        <div class="flex justify-between gap-3">
                                            <span>In Progress</span>
                                            <span
                                                class="text-[#001b4d]">{{ $group['in_progress'] ?? 0 }}/{{ $group['total'] ?? 0 }}</span>
                                        </div>

                                        <div class="flex justify-between gap-3">
                                            <span>Completed</span>
                                            <span
                                                class="text-[#001b4d]">{{ $group['completed'] ?? 0 }}/{{ $group['total'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full"
                                        style="width: {{ $percentage }}%; background-color: {{ $strokeColor }}">
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm font-semibold text-slate-500">
                            No group data available.
                        </div>
                    @endforelse
                </div>
            </section>

        </main>
    </div>
</div>
