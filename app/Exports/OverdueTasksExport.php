<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;

class OverdueTasksExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new CompletedTasksSheet(),
            new OverdueTasksSheet(),
        ];
    }

}

class OverdueTasksSheet implements FromCollection, WithHeadings
{
    /**
     * Define the headers for the export.
     */
    public function headings(): array
    {
        return [
            'Task Title',
            'Due Date',
            'Days Overdue'
        ];
    }

    /**
     * Collect the data for export.
     */
    public function collection()
    {
        // Retrieve only overdue tasks
        $tasks = Task::where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'completed')
            ->get(['title', 'due_date']);

        // Add "Days Overdue" to each task record
        $tasks = $tasks->map(function ($task) {
            // Calculate days overdue using Carbon and convert to integer
            $task->days_overdue = Carbon::parse($task->due_date)->isToday()
            ? 0
            : Carbon::parse($task->due_date)->diffInDays(Carbon::now());
            return [
                'Task Title' => $task->title,
                'Due Date' => $task->due_date->format('Y-m-d'),
                'Days Overdue' => $task->days_overdue // Ensure it's an integer
            ];
        });

        return collect($tasks);
    }
}

class CompletedTasksSheet implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Task Title',
            'Completion Date',
            'Duration Taken (Days)'
        ];
    }

    public function array(): array
    {
        // Fetch tasks that are marked as completed
        $tasks = Task::where('status', 'completed')->get();

        // Map tasks data with required fields
        $reportData = $tasks->map(function ($task) {
            $completionDate = Carbon::parse($task->updated_at); // Assuming `updated_at` is the completion date
            $creationDate = Carbon::parse($task->created_at);

            return [
                'task_title' => $task->title,
                'completion_date' => $completionDate->toDateString(),
                'duration_taken' => (int) $creationDate->diffInDays($completionDate),
            ];
        });

        return $reportData->toArray();
    }
}
