<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Task;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendTaskWhatsAppJob implements ShouldQueue
{
    use Queueable;

    public Task $task;
    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, User $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(
        WhatsAppService $whatsAppService
    ): void {

        if (empty($this->user->phone_number)) {
            return;
        }

        $userName = trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''));
        if (empty($userName)) {
            $userName = $this->user->name ?? 'User';
        }

        // WhatsAppService ke official template method ko call kiya gaya hai
        $whatsAppService->sendTaskAssigned(
            (string) $userName,
            (string) $this->user->phone_number,
            (string) $this->task->title,
            (string) $this->task->priority,
            (string) ($this->task->due_date ?? 'N/A'),
            (string) ($this->task->status ?? 'Pending')
        );
    }
}
