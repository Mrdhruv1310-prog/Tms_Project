<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskCompletionRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class TaskUpdateModal extends Component
{
    public $task;
    public $status;
    public $remark;
    public $repeat = false;
    public $recurrence = 'none';
    public $recurrence_end_date;
    public $due_date;
    public $taskUpdateModalOpen = false;

    #[On('status-updated')]
    public function open($payload)
    {
        $taskData = $payload['task'] ?? [];
        $this->task = (object) $taskData;
        $this->status = $payload['status'] ?? ($taskData['status'] ?? 'in_progress');

        // Format dates properly for HTML inputs
        $this->due_date = isset($taskData['due_date']) ? Carbon::parse($taskData['due_date'])->format('Y-m-d\TH:i') : '';
        $this->repeat = isset($taskData['recurrence']) && $taskData['recurrence'] !== 'none';
        $this->recurrence = $taskData['recurrence'] ?? 'none';
        $this->recurrence_end_date = $taskData['recurrence_end_date'] ?? '';
        $this->remark = '';

        $this->taskUpdateModalOpen = true;
    }

    public function updateTaskRemark()
    {
        $this->validate([
            'status' => 'required|string',
            'due_date' => 'required|date',
            'remark' => 'nullable|string|max:1000',
            'recurrence' => 'required|string',
        ]);

        if (!$this->task || !isset($this->task->id)) {
            Notification::make()->title('Task not found.')->danger()->send();
            return;
        }

        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $taskModel = Task::findOrFail($this->task->id);

            $recurrenceVal = $this->repeat ? $this->recurrence : 'none';
            $recurrenceEndDateVal = ($this->repeat && $recurrenceVal !== 'none') ? $this->recurrence_end_date : null;

            // Update Task main fields
            $taskModel->update([
                'status' => $this->status,
                'due_date' => $this->due_date,
                'recurrence' => $recurrenceVal,
                'recurrence_end_date' => $recurrenceEndDateVal,
                'updated_at' => now(),
            ]);

            // Insert into task_updates log
            DB::table('task_updates')->insert([
                'user_id' => $userId,
                'task_id' => $taskModel->id,
                'status' => $this->status,
                'comment' => $this->remark ? trim($this->remark) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Save conversation message if remark is added
            if (!empty($this->remark)) {
                DB::table('task_conversations')->insert([
                    'task_id' => $taskModel->id,
                    'user_id' => $userId,
                    'message' => trim($this->remark),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Handle completion request if status is complete intimation
            if ($this->status === 'complete_intimation') {
                DB::table('task_completion_requests')->insert([
                    'task_id' => $taskModel->id,
                    'user_id' => $userId,
                    'request_status' => 'pending',
                    'requested_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $this->taskUpdateModalOpen = false;

            Notification::make()
                ->title('Task Updated Successfully.')
                ->success()
                ->send();

            $this->dispatch('taskStatusUpdated');
            $this->dispatch('refreshTable');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Task update modal error: ' . $e->getMessage());

            Notification::make()
                ->title('Failed to update task. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.task-update-modal');
    }
}
