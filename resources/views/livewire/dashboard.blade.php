<div>
    <div class="antialiased dark:bg-gray-900">
        <main class="scrollcontainer p-4 md:ml-16 h-auto pt-14 pb-16">
            <h3 class="text-2xl font-semibold mb-6 text-center">Dashboard</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                @foreach ($labels as $label)
                    <div class="p-4 text-center shadow-md rounded-lg"
                        style="background-color: {{ $label['bg'] }}; border: 3px solid {{ $label['border'] }};">
                        <div class="flex justify-between items-center">
                            <div>{{ $label['title'] }}</div>
                            <div class="font-bold">{{ $label['count'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Category Report Section -->
            <div class="mb-6">
                <div class="flex flex-row justify-between">
                    <h3 class="text-xl font-semibold">Category Report</h3>
                    <a href="{{ route('categoryReport') }}" wire:navigate
                        class="text-blue-600 rounded-lg border-black border-solid p-1.5 bg-gray-200">View All</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">

                    @foreach ($categories as $category)
                        @php
                            $circumference = 565.48;
                            $offset = $circumference - ($circumference * $category['percentage']) / 100;

                            // Determine dot color based on completion percentage
                            if ($category['percentage'] >= 71) {
                                $dotColor = 'bg-green-500';
                            } elseif ($category['percentage'] >= 31) {
                                $dotColor = 'bg-yellow-300';
                            } else {
                                $dotColor = 'bg-red-600';
                            }
                        @endphp
                        <div class="relative bg-white px-4 py-2 rounded-lg shadow-md">
                            <!-- Dot for the status -->
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full {{ $dotColor }}"></div>

                            <!-- Title -->
                            <div class="text-lg font-semibold">
                                {{ ucwords($category['title']) }}
                            </div>

                            <!-- Progress -->
                            <div class="flex items-center">
                                <div class="w-20 h-20">
                                    <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                        xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">

                                        <circle r="90" cx="100" cy="100" fill="transparent" stroke="#e0e0e0"
                                            stroke-width="16px" stroke-dasharray="565.48px" stroke-dashoffset="0">
                                        </circle>

                                        <circle r="90" cx="100" cy="100" stroke="#67d697"
                                            stroke-width="16px" stroke-linecap="round" stroke-dasharray="565.48px"
                                            stroke-dashoffset="{{ $offset }}" fill="transparent"></circle>

                                        <text x="70px" y="105px" fill="#000" font-size="32px" font-weight="bold"
                                            style="transform: rotate(90deg) translate(0px, -196px)">
                                            {{ $category['percentage'] }}%
                                        </text>
                                    </svg>
                                </div>

                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">
                                        {{ $category['completed'] }}/{{ $category['total'] }} Completed
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                </d .iv>

                <!-- Team Performance Section -->
                <div class="mb-6">
                    <div class="flex flex-row justify-between">
                        <h3 class="text-xl font-semibold">
                            {{ Auth::user()->role === 'user' ? 'Assigned To Others' : 'Team Performance' }}
                        </h3>
                        <a href="{{ route('teamPerformance') }}" wire:navigate
                            class="text-blue-600 rounded-lg border-black border-solid p-1.5 bg-gray-200">View All</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($team as $member)
                            @php
                                $circumference = 565.48;
                                $offset = $circumference - ($circumference * $member['percentage']) / 100;

                                // Determine dot color based on completion percentage
                                if ($member['percentage'] >= 71) {
                                    $dotColor = 'bg-green-500';
                                } elseif ($member['percentage'] >= 31) {
                                    $dotColor = 'bg-yellow-300';
                                } else {
                                    $dotColor = 'bg-red-600';
                                }
                            @endphp
                            <div class="relative bg-white px-4 py-2 rounded-lg shadow-md">
                                <!-- Dot for the status -->
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full {{ $dotColor }}"></div>

                                <!-- First row (Member Name) -->
                                <div class="text-lg font-semibold mb-2">
                                    {{ ucwords($member['name']) }}
                                </div>

                                <!-- Second row (Progress Bar + Task Completion Status) -->
                                <div class="flex items-center">
                                    <!-- SVG Progress Bar -->
                                    <div class="w-20 h-20">
                                        <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                            xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                            <circle r="90" cx="100" cy="100" fill="transparent"
                                                stroke="#e0e0e0" stroke-width="16px" stroke-dasharray="565.48px"
                                                stroke-dashoffset="0">
                                            </circle>
                                            <circle r="90" cx="100" cy="100" stroke="#67d697"
                                                stroke-width="16px" stroke-linecap="round"
                                                stroke-dashoffset="{{ $offset }}" fill="transparent"
                                                stroke-dasharray="565.48px"></circle>
                                            <text x="70px" y="105px" fill="#000000" font-size="32px" font-weight="bold"
                                                style="transform: rotate(90deg) translate(0px, -196px)">
                                                {{ $member['percentage'] }}%
                                            </text>
                                        </svg>
                                    </div>

                                    <!-- Task Completion Status -->
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">
                                            {{ $member['completed'] }}/{{ $member['total'] }} Completed
                                        </p>
                                    </div>

                                </div>
<<<<<<< HEAD
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Groups Section -->
                <div class="mb-6">
                    <div class="flex flex-row justify-between">
                        <h3 class="text-xl font-semibold">Groups Overview</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
                        @foreach ($groups as $group)
                            @php
                                $circumference = 565.48;

                                $percentage =
                                    isset($group['percentage']) && is_numeric($group['percentage'])
                                        ? (float) $group['percentage']
                                        : 0;

                                $offset = $circumference - ($circumference * $percentage) / 100;

                                if ($percentage >= 71) {
                                    $dotColor = 'bg-green-500';
                                } elseif ($percentage >= 31) {
                                    $dotColor = 'bg-yellow-300';
                                } else {
                                    $dotColor = 'bg-red-600';
                                }
                            @endphp

                            <a href="{{ route('group.details', ['id' => $group['id'] ?? 0]) }}" wire:navigate
                                class="block">

                                <div class="relative bg-white px-4 py-2 rounded-lg shadow-md">

                                    <!-- Status Dot -->
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full {{ $dotColor }}"></div>

                                    <!-- Group Name -->
                                    <div class="text-lg font-semibold mb-2">
                                        {{ !empty($group['name']) ? ucwords($group['name']) : 'No Group Name' }}
                                    </div>

                                    <!-- Progress -->
                                    <div class="flex items-center">

                                        <div class="w-20 h-20">
                                            <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">

                                                <circle r="90" cx="100" cy="100" fill="transparent"
                                                    stroke="#e0e0e0" stroke-width="16px" stroke-dasharray="565.48px"
                                                    stroke-dashoffset="0">
                                                </circle>

                                                <circle r="90" cx="100" cy="100" stroke="#67d697"
                                                    stroke-width="16px" stroke-linecap="round"
                                                    stroke-dashoffset="{{ $offset }}" fill="transparent"
                                                    stroke-dasharray="565.48px">
                                                </circle>

                                                <text x="70px" y="105px" fill="#000000" font-size="32px"
                                                    font-weight="bold"
                                                    style="transform: rotate(90deg) translate(0px, -196px)">

                                                    {{ $percentage }}%

                                                </text>
                                            </svg>
                                        </div>

                                        <!-- Status -->
                                        <div class="ml-4">

                                            <p class="text-sm font-medium text-gray-600">
                                                Pending:
                                                {{ $group['pending'] ?? 0 }}/{{ $group['total'] ?? 0 }}
                                            </p>

                                            <p class="text-sm font-medium text-gray-600">
                                                In Progress:
                                                {{ $group['in_progress'] ?? 0 }}/{{ $group['total'] ?? 0 }}
                                            </p>

                                            <p class="text-sm font-medium text-gray-600">
                                                Completed:
                                                {{ $group['completed'] ?? 0 }}/{{ $group['total'] ?? 0 }}
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </a>
                        @endforeach
                    </div>
=======

                            </a>
                        @endforeach
>>>>>>> e99bb68ea7ec90cefd4dd2f011f1b8252bea283c
                </div>
        </main>
    </div>
</div>
