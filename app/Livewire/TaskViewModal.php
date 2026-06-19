<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskViewModal extends Component
{
    public $taskId = null;
    public bool $isOpen = false;
    public $taskList = null;
    public $taskUpdates = [];

    protected $listeners = [
        'openTaskViewModal' => 'open',
        'taskStatusUpdated' => 'refreshTaskData',
        'taskUpdated' => 'refreshTaskData',
        'refreshTaskViewModal' => 'refreshTaskData',
    ];

    public function open($taskId): void
    {
        $this->taskId = is_array($taskId) ? ($taskId['taskId'] ?? null) : $taskId;

        if (! $this->taskId) {
            return;
        }

        $this->loadTaskData();
        $this->isOpen = true;
    }

    public function refreshTaskData(): void
    {
        if (! $this->taskId) {
            return;
        }

        $this->loadTaskData();
    }

    private function loadTaskData(): void
    {
        try {
            $this->taskList = Task::query()
                ->with([
                    'assignedUsers',
                    'assignedBy',
                    'category',
                    'updates.user',
                    'reminders.user',
                ])
                ->findOrFail($this->taskId);

            $this->taskUpdates = $this->taskList->updates()
                ->with('user')
                ->latest()
                ->get();
        } catch (\Throwable $e) {
            Log::error('Task View Modal Load Error: ' . $e->getMessage(), [
                'task_id' => $this->taskId,
            ]);

            $this->taskList = null;
            $this->taskUpdates = collect();
        }
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->reset(['taskId', 'taskList', 'taskUpdates']);
    }

    public function render()
    {
        return view('livewire.task-view-modal');
    }
}
