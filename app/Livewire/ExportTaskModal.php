<?php

namespace App\Livewire;

use App\Exports\CompletedTaskReportExport;
use App\Exports\MyTasksSummaryExport;
use App\Exports\OverdueTasksExport;
use App\Exports\TaskStatusAndUserSummaryExport;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportTaskModal extends Component
{
    public bool $isOpen = false;
    public int $progress = 0;
    public bool $exporting = false;
    public bool $fileReady = false;
    public string $filePath = 'exports/report.xlsx';

    /** @var array<string, string> */
    protected $listeners = [
        'exportReport' => 'startExport',
        'openExportModal' => 'open',
        'closeModal' => 'close'
    ];

    public function open(): void
    {
        $this->isOpen = true;
        $this->dispatch('taskexportmodalopened');
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    /**
     * Placeholder handler for the 'exportReport' event listener.
     */
    public function startExport(): void
    {
        // Logic for handling background/queued exports if implemented later
        $this->exporting = true;
    }

    public function exportMyTasksSummary(string $filter = 'all', ?string $startDate = null, ?string $endDate = null): BinaryFileResponse
    {
        $this->authorizeUser();
        return Excel::download(new MyTasksSummaryExport($filter, $startDate, $endDate), 'my_tasks_summary.xlsx');
    }

    public function exportTaskStatusOverview(): BinaryFileResponse
    {
        $this->authorizeAdmin();
        return Excel::download(new TaskStatusAndUserSummaryExport, 'task_status_overview.xlsx');
    }

    public function exportOverdueTasks(): BinaryFileResponse
    {
        $this->authorizeUser();
        return Excel::download(new OverdueTasksExport, 'overdue_tasks.xlsx');
    }

    public function exportCompletedTasks(string $filter = 'all', ?string $startDate = null, ?string $endDate = null): BinaryFileResponse
    {
        $this->authorizeUser();
        return Excel::download(new CompletedTaskReportExport($filter, $startDate, $endDate), 'completed_tasks.xlsx');
    }
    /**
     * Ensure user is authenticated.
     */
    private function authorizeUser(): void
    {
        if (! Auth::check()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Ensure user is an admin or super-admin for sensitive reports.
     */
    private function authorizeAdmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! in_array($user->role, ['admin', 'super-admin'], true)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function render(): View
    {
        return view('livewire.export-task-modal');
    }
}
