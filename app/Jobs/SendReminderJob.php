<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reminderId;
    public $channel;
    public $message;

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

        if (! $user) {
            return;
        }
        if (in_array('email', $this->channel)) {
            Mail::raw($this->message, function ($mail) use ($user, $task) {
                $mail->to($user->email)
                    ->subject("Task Reminder: {$task->title}");
            });
        }

        Log::info("Reminder mail sent to {$user->email} for task {$task->title}");
    }
}
