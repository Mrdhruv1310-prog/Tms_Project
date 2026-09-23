<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class OverdueTasksExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        return [
            // Completed tasks sheet ma filters pass karya che
            new CompletedTasksSheet($this->filter, $this->startDate, $this->endDate),
            new OverdueTasksSheet(),
        ];
    }
}

class CompletedTasksSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
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

    public function title(): string
    {
        return 'Completed Tasks';
    }

    public function query()
    {
        $query = Task::query()
            ->where('user_id', Auth::user()->id)
            ->where('status', 'completed');

        // Filter apply logic (updated_at/completion date par based)
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

    public function headings(): array
    {
        return [
            'Task Title',
            'Completion Date',
            'Duration Taken (Days)'
        ];
    }

    public function map($task): array
    {
        $completionDate = Carbon::parse($task->updated_at);
        $creationDate = Carbon::parse($task->created_at);

        return [
            $task->title,
            $completionDate->toDateString(),
            (int) $creationDate->diffInDays($completionDate),
        ];
    }
}

class OverdueTasksSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    public function title(): string
    {
        return 'Overdue Tasks';
    }

    public function query()
    {
        return Task::query()
            ->where('user_id', Auth::user()->id)
            ->where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'completed');
    }

    public function headings(): array
    {
        return [
            'Task Title',
            'Due Date',
            'Days Overdue'
        ];
    }

    public function map($task): array
    {
        $daysOverdue = Carbon::parse($task->due_date)->isToday()
            ? 0
            : Carbon::parse($task->due_date)->diffInDays(Carbon::now());

        return [
            $task->title,
            $task->due_date->format('Y-m-d'),
            (int) $daysOverdue
        ];
    }
}
