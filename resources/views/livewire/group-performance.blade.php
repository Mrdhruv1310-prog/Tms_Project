<div class="antialiased bg-gray-50 dark:bg-gray-900">
    <main class="scrollcontainer p-4 md:ml-16 h-auto pt-20 pb-16">
        <div class="relative flex items-center mb-6">
            <!-- Back Button (Left-Aligned) -->
            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @click="window.history.back()">
                <svg class="w-5 h-5 transform rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                </svg>
                <span class="sr-only">Go Back</span>
            </button>
        
            <!-- Title (Centered) -->
            <h3 class="absolute left-1/2 -translate-x-1/2 text-2xl font-semibold text-center">
                {{$groupName}} Group Performance
            </h3>
        </div>
        

        <!-- Group Performance Section -->
        <div class="mb-6">
            @if (count($users) === 0)
                <div class="flex justify-center items-center" style="min-height: 50vh;">
                    <div class="text-center text-lg font-semibold text-gray-600">
                        No users found in this group.
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                {{-- if no user shows title --}}
                @foreach ($users as $user)
                    <div class="group bg-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 hover:text-white">
                        <!-- User Name -->
                        <div class="text-lg font-semibold group-hover:text-white">
                            {{ ucwords($user['name']) }}
                        </div>

                        <!-- Progress bars for Pending, In Progress, Completed -->
                        <div class="space-y-4 mt-4">
                            @foreach (['pending' => '#f87171', 'in_progress' => '#fbbf24', 'completed' => '#67d697'] as $status => $color)
                                @php
                                    $statusCount = $user[$status] ?? 0;
                                    $total = $user['total'] ?? 0;
                                    $percentage = $total > 0 ? ($statusCount / $total) * 100 : 0;
                                    $circumference = 565.48;
                                    $offset = $circumference - ($circumference * $percentage) / 100;
                                @endphp

                                <div class="flex items-center justify-between">
                                    <!-- SVG Progress Bar -->
                                    <div class="w-11 h-11">
                                        <svg width="100%" height="100%" viewBox="-25 -25 250 250" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                            <circle r="90" cx="100" cy="100" fill="transparent" stroke="#e0e0e0" stroke-width="16px" stroke-dasharray="565.48px" stroke-dashoffset="0"></circle>
                                            <circle r="90" cx="100" cy="100" stroke="{{ $color }}" stroke-width="16px" stroke-linecap="round" stroke-dashoffset="{{ $offset }}" fill="transparent" stroke-dasharray="565.48px"></circle>
                                            <text class="fill-current text-black group-hover:text-white" x="45px" y="110px" font-size="50px" font-weight="bold" style="transform: rotate(90deg) translate(0px, -196px)">
                                                {{ (int)$percentage }}%
                                            </text>
                                        </svg>
                                    </div>

                                    <!-- Task Completion Status -->
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 group-hover:text-white">
                                            {{ ucwords($status) }} - {{ $statusCount }}/{{ $total }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>
