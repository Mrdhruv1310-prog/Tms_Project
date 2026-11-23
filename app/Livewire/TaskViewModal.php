<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Task View | TMS')]
class TaskViewModal extends Component
{
    public $taskId = null;
    public bool $isOpen = false;
    public ?Task $taskList = null;
    public $taskUpdates;
    public array $taskTransfers = [];

    protected $listeners = [
        'openTaskViewModal' => 'open',
        'taskStatusUpdated' => 'refreshTaskData',
        'taskUpdated' => 'refreshTaskData',
        'refreshTaskViewModal' => 'refreshTaskData',
    ];

    public function mount(): void
    {
        $this->taskUpdates = collect();
        $this->taskTransfers = [];
    }

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
                    'creator',
                    'category',
                    'updates.user',
                    'reminders.user',
                ])
                ->findOrFail($this->taskId);

            $this->taskUpdates = $this->taskList->updates()
                ->with('user')
                ->latest()
                ->get();

            $this->loadTaskTransfers($this->taskId);
        } catch (\Throwable $e) {
            Log::error('Task View Modal Load Error: ' . $e->getMessage(), [
                'task_id' => $this->taskId,
            ]);

            $this->taskList = null;
            $this->taskUpdates = collect();
            $this->taskTransfers = [];
        }
    }

    private function loadTaskTransfers(int $taskId): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('task_transfers')) {
                $transfers = DB::table('task_transfers')
                    ->where('task_id', $taskId)
                    ->orderBy('id', 'asc')
                    ->get();

                $this->taskTransfers = $transfers->map(function ($transfer) {
                    $fromUser = User::find($transfer->from_user_id);
                    $toUser = User::find($transfer->to_user_id);

                    return [
                        'from_name' => $fromUser ? trim($fromUser->first_name . ' ' . $fromUser->last_name) : 'Unknown User',
                        'to_name' => $toUser ? trim($toUser->first_name . ' ' . $toUser->last_name) : 'Unknown User',
                        'transfer_notes' => $transfer->transfer_notes ?? 'No notes provided',
                        'transferred_at' => isset($transfer->created_at) ? \Carbon\Carbon::parse($transfer->created_at)->format('d-m-Y H:i') : null,
                    ];
                })->toArray();
            } else {
                $this->taskTransfers = [];
            }
        } catch (\Throwable $e) {
            $this->taskTransfers = [];
            Log::warning('Task transfers load warning: ' . $e->getMessage());
        }
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->reset(['taskId', 'taskList']);
        $this->taskUpdates = collect();
        $this->taskTransfers = [];
    }

    public function render()
    {
        return view('livewire.task-view-modal');
    }
}
