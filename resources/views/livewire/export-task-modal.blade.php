<div x-data="{ show: @entangle('isOpen'), selectedReport: '' }" x-cloak x-show="show"
    class="fixed inset-0 flex items-center justify-center z-50 p-2.5 xs:p-3 sm:p-5 lg:p-8 overflow-y-auto antialiased"
    style="z-index: 99999;">

    {{-- Backdrop with blur --}}
    <div x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity" inert></div>

    {{-- Modal Container --}}
    <div x-show="show" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="relative bg-white dark:bg-gray-900 rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200/80 dark:border-gray-800 w-[95vw] xs:w-full max-w-[340px] xs:max-w-[380px] sm:max-w-md lg:max-w-lg xl:max-w-xl 2xl:max-w-2xl overflow-hidden z-10 my-auto p-3.5 xs:p-4 sm:p-6 lg:p-8 2xl:p-10 transition-all duration-150">

        {{-- Modal Header --}}
        <div
            class="flex items-center justify-between mb-3.5 sm:mb-6 pb-2.5 sm:pb-4 border-b border-slate-100 dark:border-gray-800 gap-2">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                <div
                    class="w-8 h-8 sm:w-11 sm:h-11 shrink-0 rounded-xl sm:rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 flex items-center justify-center text-sm sm:text-base shadow-2xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3
                        class="text-xs xs:text-sm sm:text-xl lg:text-2xl font-bold text-slate-900 dark:text-white tracking-tight truncate">
                        Export Task Reports
                    </h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-gray-400 font-medium truncate">
                        Select a format to download report data
                    </p>
                </div>
            </div>

            <button @click="show = false" wire:loading.remove
                class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 rounded-xl sm:rounded-2xl bg-slate-100 dark:bg-gray-800 text-slate-500 dark:text-gray-300 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white flex items-center justify-center transition-all duration-150 cursor-pointer active:scale-90 shadow-2xs">
                <svg inert class="w-4 h-4 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <!-- Display Export Options Based on Role -->
        <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-8">
            @if (Auth::check() && Auth::user()?->role === 'super-admin')
                <!-- Admin Report Options -->
                <div @click="selectedReport = 'myTasksSummary'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-xs': selectedReport === 'myTasksSummary', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'myTasksSummary' }"
                    class="p-3 sm:p-4 border rounded-xl sm:rounded-2xl cursor-pointer flex items-center transition-all duration-150 min-h-[48px]">
                    <input type="radio" x-model="selectedReport" value="myTasksSummary"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-2.5 sm:mr-3 shrink-0 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none leading-snug break-words flex-1">My
                        Tasks Summary</label>
                </div>

                <div @click="selectedReport = 'taskStatusOverview'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-xs': selectedReport === 'taskStatusOverview', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'taskStatusOverview' }"
                    class="p-3 sm:p-4 border rounded-xl sm:rounded-2xl cursor-pointer flex items-center transition-all duration-150 min-h-[48px]">
                    <input type="radio" x-model="selectedReport" value="taskStatusOverview"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-2.5 sm:mr-3 shrink-0 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none leading-snug break-words flex-1">Task
                        Status Overview and User Task Summary Report</label>
                </div>
            @else
                <!-- User Report Options -->
                <div @click="selectedReport = 'myTasksSummary'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-xs': selectedReport === 'myTasksSummary', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'myTasksSummary' }"
                    class="p-3 sm:p-4 border rounded-xl sm:rounded-2xl cursor-pointer flex items-center transition-all duration-150 min-h-[48px]">
                    <input type="radio" x-model="selectedReport" value="myTasksSummary"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-2.5 sm:mr-3 shrink-0 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none leading-snug break-words flex-1">My
                        Tasks Summary</label>
                </div>

                <div @click="selectedReport = 'overdueTasks'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-xs': selectedReport === 'overdueTasks', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'overdueTasks' }"
                    class="p-3 sm:p-4 border rounded-xl sm:rounded-2xl cursor-pointer flex items-center transition-all duration-150 min-h-[48px]">
                    <input type="radio" x-model="selectedReport" value="overdueTasks"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-2.5 sm:mr-3 shrink-0 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none leading-snug break-words flex-1">Overdue
                        Tasks Report</label>
                </div>

                <div @click="selectedReport = 'completedTasks'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-xs': selectedReport === 'completedTasks', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'completedTasks' }"
                    class="p-3 sm:p-4 border rounded-xl sm:rounded-2xl cursor-pointer flex items-center transition-all duration-150 min-h-[48px]">
                    <input type="radio" x-model="selectedReport" value="completedTasks"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-2.5 sm:mr-3 shrink-0 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm lg:text-base font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none leading-snug break-words flex-1">Completed
                        Tasks Report</label>
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 w-full">
            <!-- Export Button -->
            <button :disabled="!selectedReport"
                :class="{
                    'opacity-50 cursor-not-allowed bg-slate-300 dark:bg-gray-800': !selectedReport,
                    'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 active:scale-95': selectedReport
                }"
                class="flex-1 text-white font-bold text-xs lg:text-sm uppercase tracking-wider rounded-xl sm:rounded-2xl py-3 sm:py-3.5 transition-all duration-150 cursor-pointer flex items-center justify-center text-center min-h-[44px]"
                @click="
                        if (selectedReport === 'myTasksSummary') { $wire.exportMyTasksSummary() }
                        else if (selectedReport === 'taskStatusOverview') { $wire.exportTaskStatusOverview() }
                        else if (selectedReport === 'overdueTasks') { $wire.exportOverdueTasks() }
                        else if (selectedReport === 'completedTasks') { $wire.exportCompletedTasks() }
                    "
                wire:loading.remove>
                Export Report
            </button>

            <!-- Close Button -->
            <button @click="show = false"
                class="flex-1 text-slate-700 dark:text-slate-200 font-bold text-xs lg:text-sm uppercase tracking-wider rounded-xl sm:rounded-2xl py-3 sm:py-3.5 bg-slate-100 hover:bg-slate-200 dark:bg-gray-800 dark:hover:bg-gray-700 border border-slate-200 dark:border-gray-700 transition-all duration-150 cursor-pointer active:scale-95 shadow-2xs text-center min-h-[44px]"
                wire:loading.remove>
                Close
            </button>
        </div>

        <!-- Progress Bar (Visible During Export) -->
        <div wire:loading class="mt-3.5 sm:mt-4 w-full">
            <div class="flex mb-1.5 sm:mb-2 items-center justify-between">
                <span
                    class="text-[9px] sm:text-[10px] lg:text-xs font-black inline-block py-0.5 px-2.5 sm:py-1 sm:px-3 uppercase tracking-wider rounded-full text-blue-600 bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60">
                    Exporting Data...
                </span>
                <span
                    class="text-[9px] sm:text-[10px] lg:text-xs font-bold text-slate-400 uppercase tracking-wider">Please
                    wait</span>
            </div>
            <div class="flex rounded-full h-1.5 sm:h-2 bg-slate-100 dark:bg-gray-800 overflow-hidden">
                <div class="w-full rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 animate-pulse"></div>
            </div>
        </div>
    </div>
</div>
