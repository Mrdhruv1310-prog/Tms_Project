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

    public $task;
    public $user;

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

        $message =
            "📌 New Task Assigned\n\n" .
            "Task : {$this->task->title}\n\n" .
            "Description : {$this->task->description}\n\n" .
            "Priority : {$this->task->priority}\n\n" .
            "Status : {$this->task->status}\n\n" .
            "Due Date : " .
            $this->task->due_date;

        $whatsAppService->sendTaskMessage(
            $this->user->phone_number,
            $message
        );
    }
}
