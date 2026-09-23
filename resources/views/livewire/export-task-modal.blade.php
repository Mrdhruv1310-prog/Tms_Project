<div x-data="{ show: @entangle('isOpen'), selectedReport: '' }" x-cloak x-show="show"
    class="fixed inset-0 flex items-center justify-center z-50 p-4 overflow-y-auto" style="z-index: 99999;">

    {{-- Backdrop with blur --}}
    <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity" inert></div>

    {{-- Modal Container --}}
    <div x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-6"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-6"
        class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-slate-200/80 dark:border-gray-800 w-full max-w-md overflow-hidden z-10 my-8 p-6 sm:p-8">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div
                    class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 flex items-center justify-center font-black text-sm shadow-2xs">
                    📊
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Export Task Reports
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-gray-400 font-medium">
                        Select a format to download report data
                    </p>
                </div>
            </div>

            <button @click="show = false" wire:loading.remove
                class="w-9 h-9 rounded-2xl bg-slate-100 dark:bg-gray-800 text-slate-500 dark:text-gray-300 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 dark:hover:text-white flex items-center justify-center transition-all duration-200 cursor-pointer shadow-2xs">
                <svg inert class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <!-- Display Export Options Based on Role -->
        <div class="space-y-3 mb-8">
            @if (Auth::user()->role === 'admin')
                <!-- Admin Report Options -->
                <div @click="selectedReport = 'myTasksSummary'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-sm': selectedReport === 'myTasksSummary', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'myTasksSummary' }"
                    class="p-4 border rounded-2xl cursor-pointer flex items-center transition-all duration-200">
                    <input type="radio" x-model="selectedReport" value="myTasksSummary"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-3 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none">My
                        Tasks Summary</label>
                </div>

                <div @click="selectedReport = 'taskStatusOverview'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-sm': selectedReport === 'taskStatusOverview', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'taskStatusOverview' }"
                    class="p-4 border rounded-2xl cursor-pointer flex items-center transition-all duration-200">
                    <input type="radio" x-model="selectedReport" value="taskStatusOverview"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-3 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none">Task
                        Status Overview and User Task Summary Report</label>
                </div>
            @else
                <!-- User Report Options -->
                <div @click="selectedReport = 'myTasksSummary'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-sm': selectedReport === 'myTasksSummary', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'myTasksSummary' }"
                    class="p-4 border rounded-2xl cursor-pointer flex items-center transition-all duration-200">
                    <input type="radio" x-model="selectedReport" value="myTasksSummary"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-3 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none">My
                        Tasks Summary</label>
                </div>

                <div @click="selectedReport = 'overdueTasks'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-sm': selectedReport === 'overdueTasks', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'overdueTasks' }"
                    class="p-4 border rounded-2xl cursor-pointer flex items-center transition-all duration-200">
                    <input type="radio" x-model="selectedReport" value="overdueTasks"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-3 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none">Overdue
                        Tasks Report</label>
                </div>
                <div @click="selectedReport = 'completedTasks'"
                    :class="{ 'bg-blue-50/80 dark:bg-blue-950/40 border-blue-500 ring-2 ring-blue-500/20 shadow-sm': selectedReport === 'completedTasks', 'border-slate-200 dark:border-gray-800 hover:border-slate-300 dark:hover:border-gray-700 bg-slate-50/50 dark:bg-gray-800/40': selectedReport !== 'completedTasks' }"
                    class="p-4 border rounded-2xl cursor-pointer flex items-center transition-all duration-200">
                    <input type="radio" x-model="selectedReport" value="completedTasks"
                        class="w-4 h-4 text-blue-600 focus:ring-blue-500 mr-3 cursor-pointer">
                    <label
                        class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 cursor-pointer select-none">Completed
                        Tasks Report</label>
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row sm:space-x-3 w-full gap-2 sm:gap-0">
            <!-- Export Button (Replaced by Progress Bar During Export) -->
            <button :disabled="!selectedReport"
                :class="{
                    'opacity-50 cursor-not-allowed bg-slate-300 dark:bg-gray-800': !
                        selectedReport,
                    'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/25': selectedReport
                }"
                class="btn flex-1 text-white font-bold text-xs uppercase tracking-wider rounded-2xl py-3.5 transition-all duration-300 cursor-pointer flex items-center justify-center"
                @click="
                        if (selectedReport === 'myTasksSummary') { $wire.exportMyTasksSummary() }
                        else if (selectedReport === 'taskStatusOverview') { $wire.exportTaskStatusOverview() }
                        else if (selectedReport === 'overdueTasks') { $wire.exportOverdueTasks() }
                        else if (selectedReport === 'completedTasks') { $wire.exportCompletedTasks() }
                    "
                wire:loading.remove>
                Export Report
            </button>

            <!-- Close Button, hidden during loading -->
            <button @click="show = false"
                class="btn flex-1 text-slate-700 dark:text-slate-200 font-bold text-xs uppercase tracking-wider rounded-2xl py-3.5 bg-slate-100 hover:bg-slate-200 dark:bg-gray-800 dark:hover:bg-gray-700 border border-slate-200 dark:border-gray-700 transition-all cursor-pointer shadow-2xs"
                wire:loading.remove>
                Close
            </button>
        </div>

        <!-- Progress Bar (Visible During Export) -->
        <div wire:loading class="mt-4 w-full">
            <div class="flex mb-2 items-center justify-between">
                <span
                    class="text-[10px] font-black inline-block py-1 px-3 uppercase tracking-wider rounded-full text-blue-600 bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60">
                    Exporting Data...
                </span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Please wait</span>
            </div>
            <div class="flex rounded-full h-2 bg-slate-100 dark:bg-gray-800 overflow-hidden">
                <div class="w-full rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 animate-pulse"></div>
            </div>
        </div>
    </div>
</div>
