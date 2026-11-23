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

    public int $tries = 3;
    public int $backoff = 60;

    public int $reminderId;
    /** @var array<int, string> */
    public array $channel;
    public string $message;

    /**
     * @param array<int, string> $channel
     */
    public function __construct(int $reminderId, array $channel, string $message)
    {
        $this->reminderId = $reminderId;
        $this->channel = $channel;
        $this->message = $message;
    }

    public function handle(): void
    {
        try {
            /** @var Reminder|null $reminder */
            $reminder = Reminder::with('task')->find($this->reminderId);

            if (! $reminder) {
                Log::info("Reminder skipped because reminder was deleted or not found. ID: {$this->reminderId}");
                return;
            }

            $task = $reminder->task;

            if (! $task || in_array($task->status, ['completed', 'canceled'], true)) {
                $reminder->delete();
                return;
            }

            /** @var User|null $user */
            $user = User::find($reminder->user_id);

            if (! $user || empty($user->email)) {
                Log::warning("Reminder email skipped: User not found or empty email for reminder ID: {$this->reminderId}");
                return;
            }

            if (in_array('email', $this->channel, true)) {
                Mail::to($user->email)->send(
                    new TimeWiseReminderMail(
                        $task,
                        $user,
                        $this->message
                    )
                );
                Log::info("Reminder mail successfully sent to {$user->email} for task: {$task->title}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to process SendReminderJob for ID {$this->reminderId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
