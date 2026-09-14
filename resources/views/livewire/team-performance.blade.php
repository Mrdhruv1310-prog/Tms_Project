<div class="antialiased bg-gray-50 dark:bg-gray-900">
    <main class="scrollcontainer p-4 md:ml-16 h-auto pt-20 pb-16">
        <div class="relative flex items-center mb-6">
            <button type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                @click="window.history.back()">
                <svg class="w-5 h-5 transform rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                </svg>
                <span class="sr-only">Go Back</span>
            </button>

            <!-- Dynamic Title based on User Role -->
            <h3 class="absolute left-1/2 -translate-x-1/2 text-2xl font-semibold text-center">
                @php
                    $role = Auth::user()->role ?? 'user';
                @endphp

                @if ($role === 'super-admin')
                    All Company Tasks Performance
                @elseif($role === 'admin')
                    Team Performance
                @else
                    Assigned Tasks Performance
                @endif
            </h3>
        </div>

        <!-- Team Performance Section -->
        <div class="mb-6">
            @if (!empty($team) && count($team) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                    @foreach ($team as $member)
                        <div
                            class="group bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 hover:text-white">
                            <!-- Member Name -->
                            <div class="text-lg font-semibold text-gray-800 group-hover:text-white">
                                {{ ucwords($member['name']) }}
                            </div>

                            <!-- Progress bars for Pending, In Progress, Completed -->
                            <div class="space-y-4 mt-4">
                                @foreach (['pending' => '#f87171', 'in_progress' => '#fbbf24', 'completed' => '#67d697'] as $status => $color)
                                    @php
                                        $statusCount = $member[$status] ?? 0;
                                        $total = $member['total'] ?? 0;
                                        $percentage = $total > 0 ? ($statusCount / $total) * 100 : 0;
                                        $circumference = 565.48;
                                        $offset = $circumference - ($circumference * $percentage) / 100;
                                    @endphp

                                    <div class="flex items-center justify-between">
                                        <!-- SVG Progress Bar -->
                                        <div class="w-11 h-11">
                                            <svg width="100%" height="100%" viewBox="-25 -25 250 250"
                                                xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg)">
                                                <circle r="90" cx="100" cy="100" fill="transparent"
                                                    stroke="#e0e0e0" stroke-width="16px" stroke-dasharray="565.48px"
                                                    stroke-dashoffset="0"></circle>
                                                <circle r="90" cx="100" cy="100"
                                                    stroke="{{ $color }}" stroke-width="16px"
                                                    stroke-linecap="round" stroke-dashoffset="{{ $offset }}"
                                                    fill="transparent" stroke-dasharray="565.48px"></circle>
                                                <text class="fill-current text-black group-hover:text-white" x="45px"
                                                    y="110px" font-size="50px" font-weight="bold"
                                                    style="transform: rotate(90deg) translate(0px, -196px)">
                                                    {{ (int) $percentage }}%
                                                </text>
                                            </svg>
                                        </div>

                                        <!-- Task Completion Status -->
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-600 group-hover:text-white">
                                                {{ ucwords(str_replace('_', ' ', $status)) }} -
                                                {{ $statusCount }}/{{ $total }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State Message when no performance records found -->
                <div
                    class="flex flex-col items-center justify-center p-12 bg-white dark:bg-gray-800 rounded-lg shadow-md mt-4">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">No Performance Data Found.</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">There are no tasks or members available to
                        display performance right now.</p>
                </div>
            @endif
        </div>
    </main>
</div>
