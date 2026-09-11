<?php

namespace App\Livewire;

use App\Exports\CompletedTaskReportExport;
use App\Exports\MyTasksSummaryExport;
use App\Exports\OverdueTasksExport;
use App\Exports\TaskStatusAndUserSummaryExport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportTaskModal extends Component
{
    public bool $isOpen = false;
    public int $progress = 0;
    public bool $exporting = false;
    public bool $fileReady = false;
    public string $filePath = 'exports/report.xlsx';

    protected $listeners = [
        'exportReport' => 'startExport',
        'openExportModal' => 'open',
        'closeModal' => 'close'
    ];

    public function open()
    {
        $this->isOpen = true;
        $this->dispatch('taskexportmodalopened');
    }

    public function close()
    {
        $this->isOpen = false;
    }

    /**
     * Placeholder handler for the 'exportReport' event listener.
     */
    public function startExport()
    {
        // Logic for handling background/queued exports if implemented later
        $this->exporting = true;
    }

    public function exportMyTasksSummary()
    {
        $this->authorizeUser();
        return Excel::download(new MyTasksSummaryExport, 'my_tasks_summary.xlsx');
    }

    public function exportTaskStatusOverview()
    {
        $this->authorizeAdmin();
        return Excel::download(new TaskStatusAndUserSummaryExport, 'task_status_overview.xlsx');
    }

    public function exportOverdueTasks()
    {
        $this->authorizeUser();
        return Excel::download(new OverdueTasksExport, 'overdue_tasks.xlsx');
    }

    public function exportCompletedTasks()
    {
        $this->authorizeUser();
        return Excel::download(new CompletedTaskReportExport, 'completed_tasks.xlsx');
    }

    /**
     * Ensure user is authenticated.
     */
    private function authorizeUser()
    {
        if (! Auth::check()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Ensure user is an admin or super-admin for sensitive reports.
     */
    private function authorizeAdmin()
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function render()
    {
        return view('livewire.export-task-modal');
    }
}
