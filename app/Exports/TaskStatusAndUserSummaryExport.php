<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class TaskStatusAndUserSummaryExport implements WithMultipleSheets
{
    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new TaskStatusOverviewSheet(),
            new UserTaskSummarySheet(),
        ];
    }
}

class TaskStatusOverviewSheet implements FromArray, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Task Status Overview';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Total Tasks',
            'Tasks Pending',
            'Tasks In Progress',
            'Tasks Completed',
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return [
            [
                Task::query()->count(),
                Task::query()->where('status', 'pending')->count(),
                Task::query()->where('status', 'in_progress')->count(),
                Task::query()->where('status', 'completed')->count(),
            ]
        ];
    }
}

class UserTaskSummarySheet implements FromArray, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'User Task Summary';
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'User Name',
            'Total Tasks Assigned',
            'Total Tasks Completed',
            'Total Tasks Overdue'
        ];
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        return User::query()->withCount([
            'tasks', // Total tasks assigned
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            },
            'tasks as overdue_tasks_count' => function ($query) {
                $query->where('due_date', '<', now())->where('status', '!=', 'completed');
            }
        ])->get()->map(function ($user) {
            /** @var User $user */
            $userName = trim($user->first_name . ' ' . $user->last_name);
            if (empty($userName)) {
                $userName = $user->email ?? 'N/A';
            }

            return [
                $userName,
                $user->tasks_count ?? 0,
                $user->completed_tasks_count ?? 0,
                $user->overdue_tasks_count ?? 0,
            ];
        })->toArray();
    }
}
