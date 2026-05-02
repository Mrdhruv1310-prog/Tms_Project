<?php

namespace App\Jobs;

use App\Mail\DueDateEmail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDueDateNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $task;
    public $dueDate;
    public $channel; // Channel preference (hardcoded in Livewire class)

    /**
     * Create a new job instance.
     *
     * @param Task $task
     * @param array $channel
     */
    public function __construct(Task $task, array $channel, $dueDate)
    {
        $this->task = $task;
        $this->channel = $channel;
        $this->dueDate = $dueDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assignedUsers = $this->task->assignedUsers->unique('id'); // Assuming task has a relation 'assignedUsers'

        $message = "Notification: Task '{$this->task->title}' is due on {$this->task->due_date}.";

        foreach ($assignedUsers as $user) {
            if (in_array('email', $this->channel)) {
                // Normalize dates to seconds precision for comparison
                $taskDueDate = $this->task->due_date instanceof \Carbon\Carbon
                ? $this->task->due_date->format('Y-m-d H:i:s')
                : $this->task->due_date;
                $dispatchedDueDate = $this->dueDate instanceof \Carbon\Carbon
                ? $this->dueDate->format('Y-m-d H:i:s')
                : $this->dueDate;


                if ($taskDueDate !== $dispatchedDueDate) {
                    Log::info("email : Due date of task '{$this->task->title}' has been updated so email not sent (DB: {$taskDueDate}, Dispatched: {$dispatchedDueDate}).");
                    return;
                }
                else {
    
                    Mail::to($user->email)->send(new DueDateEmail($this->task, $user));

                    Log::info("Due date email sent to {$user->email} for task '{$this->task->title}'");
                }
            }
            if (in_array('SMS', $this->channel)) {

                $taskDueDate = $this->task->due_date instanceof \Carbon\Carbon
                ? $this->task->due_date->format('Y-m-d H:i:s')
                : $this->task->due_date;
                $dispatchedDueDate = $this->dueDate instanceof \Carbon\Carbon
                ? $this->dueDate->format('Y-m-d H:i:s')
                : $this->dueDate;


                if ($taskDueDate !== $dispatchedDueDate) {
                    Log::info('SMS : Due date of this task has been updated.');
                    return;
                } else {
                    Log::info("SMS : Due date SMS sent to {$user->phone_number} for task '{$this->task->title}'");
                }
            }
        }
    }

    // protected function sendSMS($phoneNumber, $message)
    // {
    //     // Placeholder for SMS sending logic
    //     Log::info("SMS to {$phoneNumber}: {$message}");
    // }
}
