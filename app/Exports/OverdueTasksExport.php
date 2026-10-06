<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class OverdueTasksExport implements WithMultipleSheets
{
    protected string $filter;
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(string $filter = 'all', ?string $startDate = null, ?string $endDate = null)
    {
        $this->filter = $filter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new CompletedTasksSheet($this->filter, $this->startDate, $this->endDate),
            new OverdueTasksSheet(),
        ];
    }
}

/**
 * @implements WithMapping<Task>
 */
class CompletedTasksSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected string $filter;
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(string $filter = 'all', ?string $startDate = null, ?string $endDate = null)
    {
        $this->filter = $filter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Completed Tasks';
    }

    /**
     * @return Builder<Task>
     */
    public function query(): Builder
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        $authUserId = $authUser ? (int) $authUser->id : 0;

        $query = Task::query()
            ->where('user_id', $authUserId)
            ->where('status', 'completed');

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

        return $query;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Task Title',
            'Completion Date',
            'Duration Taken (Days)'
        ];
    }

    /**
     * @param mixed $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        /** @var Task $task */
        $task = $row;

        $completionDate = Carbon::parse($task->updated_at);
        $creationDate = Carbon::parse($task->created_at);

        return [
            $task->title,
            $completionDate->toDateString(),
            (int) $creationDate->diffInDays($completionDate),
        ];
    }
}

/**
 * @implements WithMapping<Task>
 */
class OverdueTasksSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function title(): string
    {
        return 'Overdue Tasks';
    }

    /**
     * @return Builder<Task>
     */
    public function query(): Builder
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        $authUserId = $authUser ? (int) $authUser->id : 0;

        return Task::query()
            ->where('user_id', $authUserId)
            ->where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'completed');
    }

    /**
     * @return array<int, string>
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
     * @param mixed $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        /** @var Task $task */
        $task = $row;

        $daysOverdue = Carbon::parse($task->due_date)->isToday()
            ? 0
            : Carbon::parse($task->due_date)->diffInDays(Carbon::now());

        return [
            $task->title,
            Carbon::parse($task->due_date)->format('Y-m-d'),
            (int) $daysOverdue
        ];
    }
}
