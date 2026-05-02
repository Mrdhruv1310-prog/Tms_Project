<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompletedTaskReportExport implements FromArray, WithHeadings
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
