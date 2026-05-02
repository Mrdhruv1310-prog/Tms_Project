<div x-data="{ show: @entangle('isOpen'), selectedReport: '' }" x-cloak x-show="show"
    class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50" style="z-index: 99999;">
    <div class="bg-white rounded-lg p-6 w-96 m-3">
        <h3 class="text-lg font-semibold text-center mb-4">Export Task Reports</h3>

        <!-- Display Export Options Based on Role -->
        <div class="space-y-2">
            @if (Auth::user()->role === 'admin')
                <!-- Admin Report Options -->
                <div @click="selectedReport = 'myTasksSummary'"
                    :class="{ 'bg-blue-100': selectedReport === 'myTasksSummary' }"
                    class="p-3 border rounded-lg cursor-pointer flex items-center">
                    <input type="radio" x-model="selectedReport" value="myTasksSummary" class="mr-2">
                    <label>My Tasks Summary</label>
                </div>
                <div @click="selectedReport = 'taskStatusOverview'"
                    :class="{ 'bg-blue-100': selectedReport === 'taskStatusOverview' }"
                    class="p-3 border rounded-lg cursor-pointer flex items-center">
                    <input type="radio" x-model="selectedReport" value="taskStatusOverview" class="mr-2">
                    <label>Task Status Overview and User Task Summary Report</label>
                </div>
            @else
                <!-- User Report Options -->
                <div @click="selectedReport = 'myTasksSummary'"
                    :class="{ 'bg-blue-100': selectedReport === 'myTasksSummary' }"
                    class="p-3 border rounded-lg cursor-pointer flex items-center">
                    <input type="radio" x-model="selectedReport" value="myTasksSummary" class="mr-2">
                    <label>My Tasks Summary</label>
                </div>
                <div @click="selectedReport = 'overdueTasks'"
                    :class="{ 'bg-blue-100': selectedReport === 'overdueTasks' }"
                    class="p-3 border rounded-lg cursor-pointer flex items-center">
                    <input type="radio" x-model="selectedReport" value="overdueTasks" class="mr-2">
                    <label>Overdue Tasks Report</label>
                </div>
                <div @click="selectedReport = 'completedTasks'"
                    :class="{ 'bg-blue-100': selectedReport === 'completedTasks' }"
                    class="p-3 border rounded-lg cursor-pointer flex items-center">
                    <input type="radio" x-model="selectedReport" value="completedTasks" class="mr-2">
                    <label>Completed Tasks Report</label>
                </div>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row sm:space-x-4 w-full">
            <!-- Export Button (Replaced by Progress Bar During Export) -->
            <button :disabled="!selectedReport"
                    :class="{ 'bg-gray-400 cursor-not-allowed': !selectedReport, 'bg-blue-500': selectedReport }"
                    class="btn mt-4 flex-1 sm:w-auto text-white font-semibold rounded-lg py-2"
                    @click="
                        if (selectedReport === 'myTasksSummary') { $wire.exportMyTasksSummary() }
                        else if (selectedReport === 'taskStatusOverview') { $wire.exportTaskStatusOverview() }
                        else if (selectedReport === 'overdueTasks') { $wire.exportOverdueTasks() }
                        else if (selectedReport === 'completedTasks') { $wire.exportCompletedTasks() }
                    "
                    wire:loading.remove>
                Export
            </button>
        
            <!-- Close Button, hidden during loading -->
            <button @click="show = false" class="btn mt-4 flex-1 text-white font-semibold rounded-lg py-2 bg-red-600"
                    wire:loading.remove>
                Close
            </button>
        </div>
        
        <!-- Progress Bar (Visible During Export) -->
        <div wire:loading class="mt-6 w-full">
            <div class="flex mb-2 items-center justify-between">
                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                    Exporting...
                </span>
                <span class="text-xs font-semibold inline-block text-blue-600">In Progress</span>
            </div>
            <div class="flex rounded-full h-2 bg-gray-200">
                <div class="w-full rounded-full bg-blue-500 animate-pulse"></div>
            </div>
        </div>
        
    </div>
</div>
