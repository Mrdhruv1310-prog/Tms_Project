<?php

namespace App\Jobs;

use App\Mail\TaskStatusUpdateMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Task $task;
    public User $user;
    public string $remark;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, User $user, string $remark)
    {
        $this->task = $task;
        $this->user = $user;
        $this->remark = $remark;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            Log::info('Task Update Job Started', [
                'email' => $this->user->email,
            ]);

            Mail::to($this->user->email)
                ->send(
                    new TaskStatusUpdateMail(
                        $this->task,
                        $this->user,
                        $this->remark
                    )
                );

            Log::info('Task Update Mail Sent Successfully', [
                'task_id' => $this->task->id,
                'email' => $this->user->email,
            ]);
        } catch (\Exception $e) {

            Log::error('Task Update Mail Failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }
}
