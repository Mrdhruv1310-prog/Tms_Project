<?php
namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskStatusAndUserSummaryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Get overall task status
        $taskCounts = $this->getTaskCounts();

        // Create a collection to store both reports
        $combinedData = collect();

        // Add overall task counts
        $combinedData->push([
            $taskCounts['totalTasks'],
            $taskCounts['tasksPending'],
            $taskCounts['tasksInProgress'],
            $taskCounts['tasksCompleted'],
        ]);

        // Add empty row for spacing
        $combinedData->push(['', '', '', '']);

        // Add user task summaries
        $combinedData->push(['User Name', 'Total Tasks Assigned', 'Total Tasks Completed', 'Total Tasks Overdue']);
        
        // Populate user summaries below headings
        foreach ($this->getUserTaskSummaries() as $summary) {
            $combinedData->push($summary);
        }

        return $combinedData;
    }

    protected function getTaskCounts()
    {
        return [
            'totalTasks' => Task::count(),
            'tasksPending' => Task::where('status', 'pending')->count(),
            'tasksInProgress' => Task::where('status', 'in_progress')->count(),
            'tasksCompleted' => Task::where('status', 'completed')->count(),
        ];
    }

    protected function getUserTaskSummaries()
    {
        return User::withCount(['tasks' => function ($query) {
            $query->where('status', 'completed');
        }, 'tasks as overdue_tasks_count' => function ($query) {
            $query->where('due_date', '<', now())->where('status', '!=', 'completed');
        }])->get()->map(function ($user) {
            return [
                $user->first_name.' '.$user->last_name,  // Assuming the User model has a name attribute
                $user->tasks_count,
                $user->tasks()->where('status', 'completed')->count(),
                $user->overdue_tasks_count,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Total Tasks',
            'Tasks Pending',
            'Tasks In Progress',
            'Tasks Completed',
            // The headings for user task summaries will be added dynamically after the task counts
        ];
    }
}
