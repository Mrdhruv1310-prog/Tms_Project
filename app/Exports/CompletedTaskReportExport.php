<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompletedTaskReportExport implements FromArray, WithHeadings
{
    protected $filter;
    protected $startDate;
    protected $endDate;

    public function __construct($filter = 'all', $startDate = null, $endDate = null)
    {
        $this->filter = $filter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            'Sr No.',
            'Task Title',
            'Description',
            'Priority',
            'User Name',
            'Category',
            'Due Date',
            'Completion Date',
            'Duration Taken (Days)'
        ];
    }

    public function array(): array
    {
        $query = Task::where('status', 'completed')->with(['user', 'category']);

        // Custom Date Range (jaise 10 Sept se 15 Sept)
        if ($this->filter === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('updated_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        } elseif ($this->filter === 'day') {
            $query->whereDate('updated_at', Carbon::today());
        } elseif ($this->filter === 'month') {
            $query->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year);
        } elseif ($this->filter === 'yearly') {
            $query->whereYear('updated_at', Carbon::now()->year);
        }

        $tasks = $query->get();

        $reportData = $tasks->map(function ($task, $index) {
            $completionDate = Carbon::parse($task->updated_at);
            $creationDate = Carbon::parse($task->created_at);

            $userName = 'N/A';
            if ($task->user) {
                $userName = trim($task->user->first_name . ' ' . $task->user->last_name);
                if (empty($userName)) {
                    $userName = $task->user->email ?? 'N/A';
                }
            }

            return [
                'sr_no' => $index + 1,
                'task_title' => $task->title,
                'description' => $task->description ?? '',
                'priority' => $task->priority ?? '',
                'user_name' => $userName,
                'category' => $task->category ? $task->category->name : 'N/A',
                'due_date' => $task->due_date ? Carbon::parse($task->due_date)->toDateString() : 'N/A',
                'completion_date' => $completionDate->toDateString(),
                'duration_taken' => (int) $creationDate->diffInDays($completionDate),
            ];
        });

        return $reportData->toArray();
    }
}
