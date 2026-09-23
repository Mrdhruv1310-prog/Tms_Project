<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MyTasksSummaryExport implements FromQuery, WithHeadings, WithMapping
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

    public function query()
    {
        $query = Task::query()
            ->with(['user', 'category'])
            ->where('user_id', Auth::user()->id);

        // Filter apply karva mate logic
        if ($this->filter === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        } elseif ($this->filter === 'day') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->filter === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($this->filter === 'yearly') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Sr No.',
            'Title',
            'Status',
            'Due Date',
            'Priority',
            'Description',
            'User Name',
            'Category Name',
            'Created At',
            'Updated At'
        ];
    }

    public function map($task): array
    {
        $userName = 'N/A';
        if ($task->user) {
            $userName = trim($task->user->first_name . ' ' . $task->user->last_name);
            if (empty($userName)) {
                $userName = $task->user->email ?? 'N/A';
            }
        }

        return [
            $task->id,
            $task->title,
            $task->status,
            $task->due_date,
            $task->priority ?? '',
            $task->description ?? '',
            $userName,
            $task->category ? $task->category->name : 'N/A',
            $task->created_at,
            $task->updated_at,
        ];
    }
}
