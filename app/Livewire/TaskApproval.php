<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskCompletionRequest;
use App\Models\TaskUpdate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskApproval extends Component
{
    public $task;
    public string $status = '';
    public string $remark = '';
    public bool $taskUpdateModalOpen = false;
    public $users = [];
    public array $selectedUsers = [];
    public ?int $taskId = null;

    protected $rules = [
        'remark' => 'required|string',
        'selectedUsers' => 'required|array|min:1',
    ];

    #[On('status-updated')]
    public function open(array $payload): void
    {
        $this->task = (object) ($payload['task'] ?? []);
        $this->taskId = $this->task->id ?? null;
        $this->status = (string) ($payload['status'] ?? '');
        $this->remark = '';

        if ($this->taskId) {
            $this->users = TaskCompletionRequest::where('task_id', $this->taskId)
                ->where('request_status', 'pending')
                ->with('user')
                ->get()
                ->pluck('user');
        }

        $this->selectedUsers = [];
        $this->taskUpdateModalOpen = true;
    }

    public function updateTaskRemark()
    {
        $this->validate();

        $authUserId = Auth::id();
        if (! $authUserId) {
            abort(403, 'Unauthorized action.');
        }

        if ($this->status === 'approved') {
            foreach ($this->selectedUsers as $userId) {
                TaskCompletionRequest::where('task_id', $this->taskId)
                    ->where('user_id', $userId)
                    ->update([
                        'request_status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by' => $authUserId,
                        'review_comment' => $this->remark,
                    ]);

                TaskUpdate::create([
                    'task_id' => $this->taskId,
                    'user_id' => $userId,
                    'status' => 'completed',
                    'comment' => $this->remark,
                ]);
            }

            $totalUsers = TaskAssignment::where('task_id', $this->taskId)->count();
            $completedUsers = TaskUpdate::where('task_id', $this->taskId)
                ->where('status', 'completed')
                ->distinct('user_id')
                ->count('user_id');

            if ($totalUsers === $completedUsers && $totalUsers > 0) {
                Task::where('id', $this->taskId)->update([
                    'status' => 'completed',
                ]);
            }
        } else {
            foreach ($this->selectedUsers as $userId) {
                TaskCompletionRequest::where('task_id', $this->taskId)
                    ->where('user_id', $userId)
                    ->update([
                        'request_status' => 'rejected',
                        'reviewed_at' => now(),
                        'reviewed_by' => $authUserId,
                        'review_comment' => $this->remark,
                    ]);
            }
        }

        $this->taskUpdateModalOpen = false;
        $this->reset(['remark', 'selectedUsers']);

        $this->dispatch('taskStatusUpdated');
        $this->dispatch('notify', ['message' => 'Task Status Updated Successfully.', 'type' => 'success']);
    }

    public function statusUpdated(array $data)
    {
        $taskId = $data['task']['id'] ?? null;
        $status = $data['status'] ?? '';

        if (! $taskId) {
            return;
        }

        $request = TaskCompletionRequest::where('task_id', $taskId)
            ->where('request_status', 'complete_intimation')
            ->latest('updated_at')
            ->first();

        if (! $request) {
            return;
        }

        if ($status === 'approved') {
            $request->update([
                'request_status' => 'approved',
                'updated_at' => now(),
            ]);

            DB::table('task_updates')
                ->where('task_id', $taskId)
                ->where('user_id', $request->user_id)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);
        }

        if ($status === 'rejected') {
            $request->update([
                'request_status' => 'rejected',
                'updated_at' => now(),
            ]);

            DB::table('task_updates')
                ->where('task_id', $taskId)
                ->where('user_id', $request->user_id)
                ->update([
                    'status' => 'in_progress',
                    'updated_at' => now(),
                ]);
        }

        $this->dispatch('refreshTable');
    }

    public function render()
    {
        return view('livewire.task-approval');
    }
}
