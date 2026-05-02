<?php

namespace App\Livewire;

use App\Exports\CompletedTaskReportExport;
use App\Exports\MyTasksSummaryExport;
use App\Exports\OverdueTasksExport;
use App\Exports\TaskStatusAndUserSummaryExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportTaskModal extends Component
{
    public $isOpen = false;
    public $progress = 0;
    public $exporting = false;
    public $fileReady = false;
    public $filePath = 'exports/report.xlsx';

    protected $listeners = ['exportReport' => 'startExport', 'openExportModal' => 'open', 'closeModal' => 'close'];

    public function open()
    {
        $this->isOpen = true;
        $this->dispatch('taskexportmodalopened'); // Dispatch event to reset the loading state
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function exportMyTasksSummary()
    {
        $this->dispatch('progressUpdated', 20); // Initial progress

        // Initiate the download directly when the button is clicked
        return Excel::download(new MyTasksSummaryExport, 'my_tasks_summary.xlsx');
    }

    public function exportTaskStatusOverview()
    {
        // Example method for Admin's Task Status Overview
        return Excel::download(new TaskStatusAndUserSummaryExport, 'task_status_overview.xlsx');
    }

    public function exportOverdueTasks()
    {
        // Example method for User's Overdue Tasks Report
        return Excel::download(new OverdueTasksExport, 'overdue_tasks.xlsx');
    }

    public function exportCompletedTasks()
    {
        // Example method for User's Completed Tasks Report
        return Excel::download(new CompletedTaskReportExport, 'completed_tasks.xlsx');
    }

    public function render()
    {
        //Open Export Modal page for create Excel file
        return view('livewire.export-task-modal');
    }
}
