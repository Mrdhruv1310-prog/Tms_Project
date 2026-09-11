<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskCompletionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class TaskUpdateModal extends Component
{
    public $task;
    public string $status = '';
    public string $remark = '';
    public bool $taskUpdateModalOpen = false;

    #[On('status-updated')]
    public function open(array $payload): void
    {
        $this->task = (object) ($payload['task'] ?? []);
        $this->status = (string) ($payload['status'] ?? '');
        $this->remark = '';
        $this->taskUpdateModalOpen = true;
    }

    public function updateTaskRemark()
    {
        $this->validate([
            'remark' => 'required|string|max:255',
        ]);

        $authUserId = Auth::id();
        if (! $authUserId || ! isset($this->task->id)) {
            $this->dispatch('notify', ['message' => 'Unauthorized or invalid task context.', 'type' => 'error']);
            return;
        }

        DB::beginTransaction();

        try {
            DB::table('task_updates')->insert([
                'user_id' => $authUserId,
                'task_id' => $this->task->id,
                'status' => $this->status,
                'comment' => $this->remark,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($this->status === 'complete_intimation') {
                DB::table('task_completion_requests')->insert([
                    'task_id' => $this->task->id,
                    'user_id' => $authUserId,
                    'request_status' => 'pending',
                    'requested_at' => now(),
                ]);
            }

            $assignedUsers = DB::table('task_assignments')
                ->where('task_id', $this->task->id)
                ->pluck('user_id');

            $statuses = [];
            foreach ($assignedUsers as $userId) {
                $latestStatus = DB::table('task_updates')
                    ->where('task_id', $this->task->id)
                    ->where('user_id', $userId)
                    ->orderBy('updated_at', 'desc')
                    ->value('status');

                $statuses[$userId] = $latestStatus ?? 'pending';
            }

            if (in_array('in_progress', $statuses, true) || in_array('complete_intimation', $statuses, true)) {
                $taskStatus = 'in_progress';
            } elseif (count(array_unique($statuses)) === 1 && in_array('completed', $statuses, true)) {
                $taskStatus = 'completed';
            } else {
                $taskStatus = 'in_progress';
            }

            Task::where('id', $this->task->id)->update([
                'status' => $taskStatus,
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->remark = '';
            $this->taskUpdateModalOpen = false;

            $this->dispatch('notify', ['message' => 'Task Status Updated Successfully.', 'type' => 'success']);
            $this->dispatch('taskStatusUpdated');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Task Update Remark Error: ' . $e->getMessage());
            $this->taskUpdateModalOpen = false;
            $this->dispatch('notify', ['message' => 'Failed to update task status. Please try again.', 'type' => 'error']);
        }
    }

    public function statusUpdated(array $data)
    {
        $taskId = $data['task']['id'] ?? null;
        if (! $taskId) {
            return;
        }

        $task = Task::findOrFail($taskId);
        $status = $data['status'] ?? '';
        $userId = Auth::id();

        if (! $userId) {
            return;
        }

        DB::table('task_updates')->updateOrInsert(
            [
                'task_id' => $task->id,
                'user_id' => $userId,
            ],
            [
                'status' => $status,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        if (in_array($status, ['in_progress', 'complete_intimation'], true)) {
            TaskCompletionRequest::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'user_id' => $userId,
                ],
                [
                    'request_status' => $status,
                    'updated_at' => now(),
                ]
            );
        }

        $this->dispatch('refreshTable');
    }

    public function render()
    {
        return view('livewire.task-update-modal');
    }
}
