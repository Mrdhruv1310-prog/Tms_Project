<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SendTaskAssignedWhatsAppJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $taskId,
        public int $userId
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $task = Task::find($this->taskId);
        if (! $task) {
            Log::warning('Task Assigned WhatsApp skipped: task missing.', ['task_id' => $this->taskId]);
            return;
        }

        $user = User::find($this->userId);
        if (! $user) {
            Log::warning('Task Assigned WhatsApp skipped: user missing.', ['user_id' => $this->userId]);
            return;
        }

        $phoneNumber = $user->phone_number ?? $user->mobile_number ?? null;
        if (empty($phoneNumber)) {
            Log::warning('Task Assigned WhatsApp skipped: user phone missing.', [
                'task_id' => $task->id,
                'user_id' => $user->id,
            ]);
            return;
        }

        $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($userName)) {
            $userName = $user->name ?? 'User';
        }

        $dueDate = $task->due_date
            ? Carbon::parse($task->due_date)->format('d/m/Y h:i A')
            : 'Not Set';

        $status = ucfirst(str_replace('_', ' ', (string) ($task->status ?? 'Pending')));

        $result = $whatsApp->sendTaskAssigned(
            $phoneNumber,
            $userName,
            $task->title ?? 'Task',
            ucfirst((string) ($task->priority ?? 'Normal')),
            $dueDate,
            $status
        );

        if (! ($result['success'] ?? false)) {
            throw new RuntimeException(
                'Task Assigned WhatsApp failed: ' . ($result['message'] ?? 'Unknown Meta Error')
            );
        }

        Log::info('Task Assigned WhatsApp Sent', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'message_id' => $result['message_id'] ?? null,
        ]);
    }
}
