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

/**
 * @implements WithMapping<Task>
 */
class MyTasksSummaryExport implements FromQuery, WithHeadings, WithMapping
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
     * @return Builder<Task>
     */
    public function query(): Builder
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();
        $authUserId = $authUser ? (int) $authUser->id : 0;

        $query = Task::query()
            ->with(['user', 'category'])
            ->where('user_id', $authUserId);

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

    /**
     * @return array<int, string>
     */
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

    /**
     * @param mixed $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        /** @var Task $task */
        $task = $row;

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
