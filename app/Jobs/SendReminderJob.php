<?php

namespace App\Jobs;

use App\Mail\ReminderEmail;
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

    public $reminder;
    public $reminderTime;
    public $channel; // Channel preference (hardcoded in Livewire class)

    /**
     * Create a new job instance.
     *
     * @param Reminder $reminder
     * @param array $channel
     */
    public function __construct(Reminder $reminder, array $channel, $reminderTime)
    {
        $this->reminder = $reminder;
        $this->channel = $channel;
        $this->reminderTime = $reminderTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->reminder) {
            Log::error("SendReminderJob failed: Reminder object is null.");
            return;
        }

        $task = $this->reminder->task;

        if (!$task) {
            Log::error("SendReminderJob failed: Task not found for reminder ID {$this->reminder->id}.");
            return;
        }

        $user = User::find($this->reminder->user_id);

        if (!$user) {
            Log::error("SendReminderJob failed: User not found for ID {$this->reminder->user_id}.");
            return;
        }

        $message = $this->reminder->message
            ?? "Reminder: Task '{$task->title}' is due soon.";

        try {

            if (in_array('email', $this->channel)) {
                Mail::to($user->email)->send(new ReminderEmail($this->reminder, $task));
                Log::info("Reminder email sent to {$user->email} for task '{$task->title}'");
            }

            if (in_array('sms', $this->channel)) {
                // Example SMS logic placeholder
                // $this->sendSMS($user->phone_number, $message);

                Log::info("Reminder SMS sent to {$user->phone_number} for task '{$task->title}'");
            }
        } catch (\Exception $e) {
            Log::error("Reminder sending failed for task '{$task->title}': " . $e->getMessage());
        }
    }
    // public function handle()
    // {
    //     if (!$this->reminder) {
    //         Log::error("SendReminderJob failed: Reminder object is null.");
    //         return;
    //     }

    //     // Retrieve the associated task and user
    //     $task = $this->reminder->task;
    //     if (!$task) {
    //         Log::error("SendReminderJob failed: Task not found for reminder ID {$this->reminder->id}.");
    //         return;
    //     }

    //     $user = User::find($this->reminder->user_id);
    //     if (!$user) {
    //         Log::error("SendReminderJob failed: User not found for ID {$this->reminder->user_id}.");
    //         return;
    //     }

    //     $message = "Reminder: Task '{$task->title}' is due soon.";

    //     if (in_array('email', $this->channel)) {
    //         // Send reminder via email
    //         if ($this->reminder->reminder_time != $this->reminderTime) {
    //             Log::info("Reminder email is changed for this task '{$task->title}'");
    //         } else {
    //             Mail::to($user->email)->send(new ReminderEmail($this->reminder, $task));
    //             Log::info("Reminder email sent to {$user->email} for task '{$task->title}'");
    //         }
    //     }
    //     if (in_array('SMS', $this->channel)) {
    //         // Send reminder via SMS
    //         // $this->sendSMS($user->phone_number, $message);
    //         if ($this->reminder->reminder_time != $this->reminderTime) {
    //             Log::info("Reminder sms is changed for this task '{$task->title}'");
    //         } else {
    //             Log::info("Reminder SMS sent to {$user->phone_number} for task '{$task->title}'");
    //         }
    //     }
    // }


    // protected function sendSMS($phoneNumber, $message)
    // {
    //     // This is a placeholder for SMS sending logic
    //     Log::info("SMS to {$phoneNumber}: {$message}");
    // }

}
