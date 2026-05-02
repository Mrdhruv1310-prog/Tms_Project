<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskViewModal extends Component
{
    public $taskId;
    public $isOpen = false;
    public $taskList;
    public $taskUpdates = [];

    protected $listeners = ['openTaskViewModal' => 'open'];

    public function open($taskId)
    {
        $this->taskId = $taskId;

        // Fetch task with related models including task updates and the users who made the updates
        $this->taskList = Task::with(['assignedUsers', 'assignedBy', 'category', 'updates.user']) // 'updates.user' will load the user who made the update
            ->findOrFail($this->taskId);

        // Also load the task updates separately if needed
        $this->taskUpdates = $this->taskList->updates;
        
        $this->isOpen = true;
    }

    public function render()
    {
        return view('livewire.task-view-modal');
    }
}
