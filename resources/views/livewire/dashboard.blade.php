<div>
    @php
        $authUser = Auth::user();
        $isAdmin = $authUser->role === 'admin';
        $isUser = $authUser->role === 'user';

        $visibleCategories = collect($categories ?? []);
        $visibleTeam = collect($team ?? []);
        $visibleGroups = collect($groups ?? []);
    @endphp

    <div class="min-h-screen bg-[#f3f6fb] antialiased">
        <main class="scrollcontainer p-4 md:ml-16 h-auto pt-14 pb-16">

            {{-- Header --}}
            <div class="mb-8">
                <div class="mb-3 flex items-center gap-2 text-sm text-gray-500">
                    <span>Home</span>
                    <span>/</span>
                    <span class="font-semibold text-gray-800">Dashboard</span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-gray-500">
                    {{ $isAdmin ? 'Welcome back, here is your team task overview.' : 'Welcome back, here is your assigned task overview.' }}
                </p>
            </div>

            {{-- Summary Cards --}}
            <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($labels as $label)
                    @php
                        $title = strtolower($label['title']);
                    @endphp

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                    {{ $label['title'] }}
                                </p>

                                <h2 class="mt-3 text-2xl font-medium text-gray-900">
                                    {{ $label['count'] }}
                                </h2>
                            </div>

                            <div class="flex h-12 w-12 items-center justify-center rounded-full"
                                style="background-color: {{ $label['bg'] }}; border: 2px solid {{ $label['border'] }};">
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
                                @elseif ($title === 'total')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                        <path
                                            d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Category Report --}}
            <section class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Category Report</h2>
                        <p class="text-sm text-gray-500">
                            {{ $isAdmin ? 'Category-wise task completion progress' : 'Your assigned category-wise task progress' }}
                        </p>
                    </div>

                    <a href="{{ route('categoryReport') }}" wire:navigate
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
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
                            } elseif ($percentage >= 31) {
                                $dotColor = 'bg-yellow-400';
                                $strokeColor = '#facc15';
                            } else {
                                $dotColor = 'bg-red-500';
                                $strokeColor = '#ef4444';
                            }
                        @endphp

                        <div class="relative rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <div class="absolute right-4 top-4 h-3 w-3 rounded-full {{ $dotColor }}"></div>

                            <h3 class="mb-5 pr-6 text-lg font-bold text-gray-900">
                                {{ ucwords($category['title']) }}
                            </h3>

                            <div class="flex items-center gap-5">
                                <div class="h-20 w-20">
                                    <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                        xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                        <circle r="90" cx="100" cy="100" fill="transparent" stroke="#e5e7eb"
                                            stroke-width="16" stroke-dasharray="565.48" stroke-dashoffset="0" />

                                        <circle r="90" cx="100" cy="100" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="16" stroke-linecap="round"
                                            stroke-dasharray="565.48" stroke-dashoffset="{{ $offset }}" />

                                        <text x="72px" y="108px" fill="#111827" font-size="30px" font-weight="bold"
                                            style="transform: rotate(90deg) translate(0px, -196px)">
                                            {{ $percentage }}%
                                        </text>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ $category['completed'] }}/{{ $category['total'] }}
                                    </p>
                                    <p class="text-sm text-gray-500">Completed</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
                            No category data available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Team / My Performance --}}
            <section class="mb-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $isAdmin ? 'Team Performance' : 'My Performance' }}
                        </h2>

                        <p class="text-sm text-gray-500">
                            {{ $isAdmin ? 'User-wise task completion summary' : 'Your assigned task completion summary' }}
                        </p>
                    </div>

                    <a href="{{ route('teamPerformance') }}" wire:navigate
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        View All
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
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
                            } elseif ($percentage >= 31) {
                                $dotColor = 'bg-yellow-400';
                                $strokeColor = '#facc15';
                            } else {
                                $dotColor = 'bg-red-500';
                                $strokeColor = '#ef4444';
                            }
                        @endphp

                        <div class="relative rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <div class="absolute right-4 top-4 h-3 w-3 rounded-full {{ $dotColor }}"></div>

                            <h3 class="mb-5 pr-6 text-lg font-bold text-gray-900">
                                {{ ucwords($member['name']) }}
                            </h3>

                            <div class="flex items-center gap-5">
                                <div class="h-20 w-20">
                                    <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                        xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                        <circle r="90" cx="100" cy="100" fill="transparent" stroke="#e5e7eb"
                                            stroke-width="16" stroke-dasharray="565.48" stroke-dashoffset="0" />

                                        <circle r="90" cx="100" cy="100" fill="transparent"
                                            stroke="{{ $strokeColor }}" stroke-width="16" stroke-linecap="round"
                                            stroke-dasharray="565.48" stroke-dashoffset="{{ $offset }}" />

                                        <text x="72px" y="108px" fill="#111827" font-size="30px" font-weight="bold"
                                            style="transform: rotate(90deg) translate(0px, -196px)">
                                            {{ $percentage }}%
                                        </text>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ $member['completed'] }}/{{ $member['total'] }}
                                    </p>
                                    <p class="text-sm text-gray-500">Completed</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
                            No performance data available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Groups Overview --}}
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6 border-b border-gray-100 pb-4">
                    <h2 class="text-xl font-bold text-gray-900">Groups Overview</h2>
                    <p class="text-sm text-gray-500">
                        {{ $isAdmin ? 'Group-wise task status overview' : 'Your group-wise task status overview' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
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

                        <a href="{{ route('group.details', ['id' => $group['id'] ?? 0]) }}" wire:navigate>
                            <div
                                class="relative rounded-xl border border-gray-200 bg-gray-50 p-5 hover:bg-white hover:shadow-sm">
                                <div class="absolute right-4 top-4 h-3 w-3 rounded-full {{ $dotColor }}"></div>

                                <h3 class="mb-5 pr-6 text-lg font-bold text-gray-900">
                                    {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                </h3>

                                <div class="flex items-center gap-5">
                                    <div class="h-20 w-20">
                                        <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                            xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                            <circle r="90" cx="100" cy="100" fill="transparent"
                                                stroke="#e5e7eb" stroke-width="16" stroke-dasharray="565.48"
                                                stroke-dashoffset="0" />

                                            <circle r="90" cx="100" cy="100" fill="transparent"
                                                stroke="{{ $strokeColor }}" stroke-width="16"
                                                stroke-linecap="round" stroke-dasharray="565.48"
                                                stroke-dashoffset="{{ $offset }}" />

                                            <text x="72px" y="108px" fill="#111827" font-size="30px"
                                                font-weight="bold"
                                                style="transform: rotate(90deg) translate(0px, -196px)">
                                                {{ $percentage }}%
                                            </text>
                                        </svg>
                                    </div>

                                    <div class="space-y-1 text-sm font-medium text-gray-600">
                                        <p>Pending: {{ $group['pending'] ?? 0 }}/{{ $group['total'] ?? 0 }}</p>
                                        <p>In Progress: {{ $group['in_progress'] ?? 0 }}/{{ $group['total'] ?? 0 }}
                                        </p>
                                        <p>Completed: {{ $group['completed'] ?? 0 }}/{{ $group['total'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
                            No group data available.
                        </div>
                    @endforelse
                </div>
            </section>

        </main>
    </div>
</div>
