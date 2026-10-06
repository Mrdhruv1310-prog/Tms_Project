<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompletedTaskReportExport implements FromArray, WithHeadings
{
    protected string $filter;
    protected ?string $startDate;
    protected ?string $endDate;

    /**
     * @param string $filter
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function __construct(string $filter = 'all', ?string $startDate = null, ?string $endDate = null)
    {
        $this->filter = $filter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return array<int, string>
     */
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function array(): array
    {
        $query = Task::query()->where('status', 'completed')->with(['user', 'category']);

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

        $reportData = $tasks->map(function ($task, int $index) {
            /** @var Task $task */
            $completionDate = Carbon::parse($task->updated_at);
            $creationDate = Carbon::parse($task->created_at);

            $userName = 'N/A';
            if ($task->user) {
                $userName = trim(($task->user->first_name ?? '') . ' ' . ($task->user->last_name ?? ''));
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
