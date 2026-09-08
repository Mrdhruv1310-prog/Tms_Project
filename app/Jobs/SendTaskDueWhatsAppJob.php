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

class SendTaskDueWhatsAppJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public int $taskId,
        public int $userId,
        public string $expectedDueAt
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $task = Task::find($this->taskId);
        if (! $task) {
            Log::warning('SendTaskDueWhatsAppJob skipped: task missing.', ['task_id' => $this->taskId]);
            return;
        }

        // Delay queue execute hote waqt check karein ki task complete to nahi ho gaya
        if ($task->status === 'completed') {
            Log::info('SendTaskDueWhatsAppJob skipped: task already completed.', ['task_id' => $task->id]);
            return;
        }

        $user = User::find($this->userId);
        if (! $user) {
            Log::warning('SendTaskDueWhatsAppJob skipped: user missing.', ['user_id' => $this->userId]);
            return;
        }

        $phoneNumber = $user->phone_number ?? $user->mobile_number ?? null;
        if (empty($phoneNumber)) {
            Log::warning('SendTaskDueWhatsAppJob skipped: user phone missing.', [
                'task_id' => $task->id,
                'user_id' => $user->id,
            ]);
            return;
        }

        $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($userName)) {
            $userName = $user->name ?? 'User';
        }

        $dueDate = ! empty($this->expectedDueAt)
            ? Carbon::parse($this->expectedDueAt)->format('d/m/Y h:i A')
            : ($task->due_date ? Carbon::parse($task->due_date)->format('d/m/Y h:i A') : 'Due Now');

        $status = ucfirst(str_replace('_', ' ', (string) ($task->status ?? 'Pending')));

        $result = $whatsApp->sendTaskDueDate(
            $phoneNumber,
            $userName,
            $task->title ?? 'Task',
            ucfirst((string) ($task->priority ?? 'Normal')),
            $dueDate,
            $status
        );

        if (! ($result['success'] ?? false)) {
            throw new RuntimeException(
                'SendTaskDueWhatsAppJob failed: ' . ($result['message'] ?? 'Unknown Meta Error')
            );
        }

        Log::info('SendTaskDueWhatsAppJob Sent Successfully', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'message_id' => $result['message_id'] ?? null,
        ]);
    }
}
