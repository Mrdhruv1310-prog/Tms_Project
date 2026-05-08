<?php

namespace App\Livewire;

use App\Mail\TaskStatusUpdateMail;
use App\Models\Task;
use App\Models\TaskCompletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\Attributes\On;

class TaskUpdateModal extends Component
{
    public $task;
    public $status;
    public $remark;
    public $taskUpdateModalOpen = false;

    #[On('status-updated')]
    public function open($payload)
    {
        $this->task = (object) $payload['task'];
        $this->status = $payload['status'];
        $this->remark = '';

        $this->taskUpdateModalOpen = true;
    }

    public function updateTaskRemark(User $user)
    {
        $this->validate([
            'remark' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            // Insert task update
            DB::table('task_updates')->insert([
                'task_id' => $this->task->id,
                'user_id' => Auth::user()->id,
                'status' => $this->status,
                'comment' => $this->remark,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert completion request if status is complete_intimation
            if ($this->status === 'complete_intimation') {

                DB::table('task_completion_requests')->insert([
                    'task_id' => $this->task->id,
                    'user_id' => Auth::user()->id,
                    'request_status' => 'pending',
                    'requested_at' => now(),
                ]);
            }

            // Fetch assigned users
            $assignedUsers = DB::table('task_assignments')
                ->where('task_id', $this->task->id)
                ->pluck('user_id');

            // Get latest status of each assigned user
            $statuses = [];

            foreach ($assignedUsers as $userId) {

                $latestStatus = DB::table('task_updates')
                    ->where('task_id', $this->task->id)
                    ->where('user_id', $userId)
                    ->orderBy('updated_at', 'desc')
                    ->value('status');

                $statuses[$userId] = $latestStatus ?? 'pending';
            }

            // Determine final task status
            if (
                in_array('in_progress', $statuses) ||
                in_array('complete_intimation', $statuses)
            ) {

                $taskStatus = 'in_progress';

            } elseif (
                count(array_unique($statuses)) === 1 &&
                in_array('completed', $statuses)
            ) {

                $taskStatus = 'completed';

            } else {

                $taskStatus = 'in_progress';
            }

            // Update task table
            Task::where('id', $this->task->id)->update([
                'status' => $taskStatus,
                'updated_at' => now(),
            ]);

            // Fetch updated task
            $task = Task::find($this->task->id);

            // Send mail to all assigned users
            foreach ($assignedUsers as $assignedUserId) {

                $assignedUser = User::find($assignedUserId);

                if ($assignedUser && $assignedUser->email) {

                    Mail::to($assignedUser->email)
                        ->queue(new TaskStatusUpdateMail(
                            $task,
                            $assignedUser,
                            $this->status,
                            $this->remark
                        ));

                    Log::info('Task Status Update Mail Sent', [
                        'task_id' => $task->id,
                        'email' => $assignedUser->email,
                        'status' => $this->status,
                    ]);
                }
            }

            DB::commit();

            $this->remark = '';
            $this->taskUpdateModalOpen = false;

            $this->notify(
                'Task Status Updated Successfully.',
                'success'
            );

            $this->dispatch('taskStatusUpdated');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Task Update Error: ' . $e->getMessage());

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
