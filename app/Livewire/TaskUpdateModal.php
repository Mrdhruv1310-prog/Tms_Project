<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskCompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class TaskUpdateModal extends Component
{
    public $task;
    public $status;
    public $remark; // Property for storing the remark
    public $taskUpdateModalOpen = false;

    #[On('status-updated')]
    public function open($payload)
    {
        // Unpack the task and status from the payload
        $this->task = (object) $payload['task']; // Store the task details
        $this->status = $payload['status']; // Store the status

        $this->remark = ''; // Initialize the remark

        // Set the modal visibility to true
        $this->taskUpdateModalOpen = true; // Make sure the modal is set to open
    }


    public function updateTaskRemark(User $user)
    {
        $this->validate([
            'remark' => 'required|string|max:255',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the task status in the task_updates table for the current user
            DB::table('task_updates')->insert([
                'task_id' => $this->task->id,
                'user_id' => Auth::user()->id,
                'status' => $this->status,
                'comment' => $this->remark,
            ]);

            // If the status is 'complete_intimation', add a record in task_completion_requests table
            if ($this->status == 'complete_intimation') {
                DB::table('task_completion_requests')->insert([
                    'task_id' => $this->task->id,
                    'user_id' => Auth::user()->id,
                    'request_status' => 'pending',
                    'requested_at' => now(),
                ]);
            }

            // Fetch the users assigned to the task
            $assignedUsers = DB::table('task_assignments')
                ->where('task_id', $this->task->id)
                ->pluck('user_id');

            // Initialize an array to store the statuses of all users
            $statuses = [];
            foreach ($assignedUsers as $userId) {
                $latestStatus = DB::table('task_updates')
                    ->where('task_id', $this->task->id)
                    ->where('user_id', $userId)
                    ->latest()
                    ->value('status');

                $statuses[$userId] = $latestStatus ?? 'pending';
            }
            dd($statuses);
            $taskStatus = 'pending';

            if (in_array('in_progress', $statuses)) {

                $taskStatus = 'in_progress';
            } elseif (
                count($statuses) > 0 &&
                count(array_unique($statuses)) === 1 &&
                in_array('complete_intimation', $statuses)
            ) {
                $taskStatus = 'completed';
            } elseif (in_array('complete_intimation', $statuses) && !in_array('in_progress', $statuses)) {
                $taskStatus = 'pending';
            } else {
                $taskStatus = 'pending';
            }

            Task::where('id', $this->task->id)->update(['status' => $taskStatus, 'updated_at' => now(),]);

            DB::commit();

            $this->remark = '';
            $this->taskUpdateModalOpen = false;
            $this->notify('Task Status Updated Successfully.', 'success');
            $this->dispatch('taskStatusUpdated');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Task Status Update Error: ' . $e->getMessage());
            $this->taskUpdateModalOpen = false;
            $this->notify(
                'Failed to update task status. Please try again.',
                'error'
            );
        }
    }


    public function render()
    {
        return view('livewire.task-update-modal');
    }
}
