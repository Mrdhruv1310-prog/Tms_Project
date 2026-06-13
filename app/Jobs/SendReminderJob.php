<?php

namespace App\Jobs;

use App\Mail\TimeWiseReminderMail;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reminderId;
    public array $channel;
    public string $message;

    public function __construct(int $reminderId, array $channel, string $message)
    {
        $this->reminderId = $reminderId;
        $this->channel = $channel;
        $this->message = $message;
    }

    public function handle(): void
    {
        $reminder = Reminder::find($this->reminderId);

        if (! $reminder) {
            Log::info("Reminder skipped because reminder was deleted. ID: {$this->reminderId}");
            return;
        }

        $task = $reminder->task;

        if (! $task || $task->status === 'completed') {
            return;
        }

        $user = User::find($reminder->user_id);

        if (! $user || empty($user->email)) {
            return;
        }

        if (in_array('email', $this->channel)) {
            Mail::to($user->email)->send(
                new TimeWiseReminderMail(
                    $task,
                    $user,
                    $this->message
                )
            );
        }

        Log::info("Reminder mail sent to {$user->email} for task {$task->title}");
    }
}
