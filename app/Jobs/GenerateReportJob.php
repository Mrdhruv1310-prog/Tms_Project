<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{MyTasksSummaryExport, TaskStatusOverviewExport, UserTaskSummaryExport, OverdueTasksExport, CompletedTasksExport};
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $reportType;
    public $filePath;

    public function __construct($reportType, $filePath)
    {
        $this->reportType = $reportType;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $exportClass = $this->getExportClass($this->reportType);

        if ($exportClass) {
            Excel::store(new $exportClass, $this->filePath);
        }
    }

    protected function getExportClass($reportType)
    {
        return match($reportType) {
            'myTasksSummary' => MyTasksSummaryExport::class,
            'taskStatusOverview' => TaskStatusOverviewExport::class,
            'userTaskSummary' => UserTaskSummaryExport::class,
            'overdueTasks' => OverdueTasksExport::class,
            'completedTasks' => CompletedTasksExport::class,
            default => null
        };
    }
}
