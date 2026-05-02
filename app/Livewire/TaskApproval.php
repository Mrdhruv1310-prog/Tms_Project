<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskCompletionRequest;
use App\Models\TaskUpdate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskApproval extends Component
{
    public $task;
    public $status;
    public $remark; // Property for storing the remark
    public $taskUpdateModalOpen = false;
    public $users = []; // Store the list of users
    public $selectedUsers = []; // Array for storing selected users
    protected $rules = [
        'remark' => 'required|string',
        'selectedUsers' => 'required|array|min:1',
    ];
    public $taskId;

    #[On('status-updated')]
    public function open($payload)
    {
        // Unpack the task and status from the payload
        $this->task = (object) $payload['task']; // Store the task details
        $this->taskId = $this->task->id;
        $this->status = $payload['status']; // Store the status

        $this->remark = ''; // Initialize the remark

        // Fetch users who have made completion requests for this task
        $this->users = TaskCompletionRequest::where('task_id', $this->task->id)->where('request_status', 'pending')
            ->with('user') // Assuming the relationship is defined in the model
            ->get()
            ->pluck('user'); // Get only the user details

        $this->selectedUsers = []; // Clear selected users

        // Set the modal visibility to true
        $this->taskUpdateModalOpen = true; // Make sure the modal is set to open
    }

    public function updateTaskRemark()
    {
        if ($this->status === 'approved') {
            foreach ($this->selectedUsers as $userId) {
                // 1. Update the task_completion_requests table
                TaskCompletionRequest::where('task_id', $this->taskId)
                    ->where('user_id', $userId)
                    ->update([
                        'request_status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::user()->id,
                        'review_comment' => $this->remark,
                    ]);
    
                // 2. Add a record to the task_updates table
                TaskUpdate::create([
                    'task_id' => $this->taskId,
                    'user_id' => $userId,
                    'status' => 'completed',
                    'comment' => $this->remark,
                ]);
            }
    
            // 3. Check if all users for the task are marked as 'completed'
            $totalUsers = TaskAssignment::where('task_id', $this->taskId)->count();
            $completedUsers = TaskUpdate::where('task_id', $this->taskId)
                ->where('status', 'completed')
                ->distinct('user_id')
                ->count('user_id');
    
            // If all users have completed the task, update the tasks table
            if ($totalUsers === $completedUsers) {
                Task::where('id', $this->taskId)->update([
                    'status' => 'completed',
                ]);
            }
        } else {
            // Handle other statuses like 'rejected', if needed
            foreach ($this->selectedUsers as $userId) {
                TaskCompletionRequest::where('task_id', $this->taskId)
                    ->where('user_id', $userId)
                    ->update([
                        'request_status' => 'rejected',
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::user()->id,
                        'review_comment' => $this->remark,
                    ]);
            }
        }
    
        // Close the modal after saving
        $this->taskUpdateModalOpen = false;
    
        // Optionally, reset the form
        $this->reset(['remark', 'selectedUsers']);
    
        $this->dispatch('taskStatusUpdated');
        
        // Emit an event for UI updates if necessary
        $this->notify('Task Status Updated Successfully.', 'success');
    }

    public function render()
    {
        return view('livewire.task-approval');
    }
}
