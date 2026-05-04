<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskRecurrenceDay;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendReminderJob;
use App\Jobs\SendDueDateNotificationJob;
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class RepeatTaskCron extends Command
{
    protected $signature = 'repeattask:cron';
    protected $description = 'Command description';

    public $reminderChannel = ['email', 'SMS'];
    public $dueDateChannel = ['email', 'SMS'];

    public function handle(): void
    {
        $lock = Cache::lock('repeattask:cron', 60);
        if (!$lock->get()) {
            info('RepeatTaskCron already running. Skipping.');
            return;
        }

        try {
            info('Running RepeatTaskCron...');

            $currentDayAbbreviation = substr(Carbon::now()->format('D'), 0, 2);
            $currentDate = Carbon::now()->toDateString();
            info('Current day: ' . $currentDate);

            $tasks = Task::whereIn('recurrence', ['daily', 'weekly', 'monthly'])
                ->where('recurrence_end_date', '>=', $currentDate)
                ->whereNotNull('recurrence_end_date')
                ->get();

            foreach ($tasks as $task) {
                info("Processing task {$task->id}: {$task->title}");
                DB::beginTransaction();
                try {
                    $shouldRepeat = false;

                    switch ($task->recurrence) {
                        case 'daily':
                            $shouldRepeat = true;
                            break;
                        case 'weekly':
                            $recurrenceDays = TaskRecurrenceDay::where('task_id', $task->id)->pluck('day')->toArray();
                            if (in_array($currentDayAbbreviation, $recurrenceDays)) {
                                $shouldRepeat = true;
                            }
                            break;
                        case 'monthly':
                            $taskDay = $task->created_at->day;
                            $currentMonthLastDay = Carbon::now()->endOfMonth()->day;
                            if ($taskDay > $currentMonthLastDay) {
                                $taskDay = $currentMonthLastDay;
                            }
                            if (Carbon::now()->day == $taskDay) {
                                $shouldRepeat = true;
                            }
                            break;
                    }

                    if ($shouldRepeat) {
                        $nextDueDate = $this->calculateNextDueDate($task);
                        if (!$nextDueDate) {
                            info("Skipping task {$task->id}: Invalid next due date.");
                            DB::commit();
                            continue;
                        }

                        $existingTask = Task::where('title', $task->title)
                            ->where('due_date', $nextDueDate)
                            ->where('user_id', $task->user_id)
                            ->exists();

                        if ($existingTask) {
                            info("Skipping task {$task->id}: Duplicate task already exists for due date {$nextDueDate}.");
                            DB::commit();
                            continue;
                        }

                        $this->repeatTask($task);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->logFailedTask($task, $e->getMessage());
                    info('Error in RepeatTaskCron: ' . $e->getMessage());
                }
            }
        } finally {
            $lock->release();
        }
    }

    private function repeatTask(Task $task): void
    {
        $nextDueDate = $this->calculateNextDueDate($task);
        if (!$nextDueDate) {
            info("Skipping task {$task->id}: Invalid due date.");
            return;
        }

        $hasActiveUsers = false;
        foreach ($task->taskAssignments as $assignment) {
            $user = $assignment->user;
            if ($user && $user->status === 1) {
                $hasActiveUsers = true;
                break;
            }
        }

        if (!$hasActiveUsers) {
            info("Skipping task {$task->id}: No active users assigned.");
            return;
        }

        $repeatedTask = Task::create([
            'title' => $task->title,
            'description' => $task->description,
            'category_id' => $task->category_id,
            'priority' => $task->priority,
            'label_id' => is_numeric($task->label_id) ? $task->label_id : null,
            'due_date' => $nextDueDate,
            'status' => 'pending',
            'user_id' => $task->user_id,
        ]);

        info("Created repeated task {$repeatedTask->id} for original task {$task->id}, due date {$nextDueDate}");

        $assignedUserIds = [];
        foreach ($task->taskAssignments as $assignment) {
            $user = $assignment->user;
            if ($user && $user->status === 1 && !in_array($assignment->user_id, $assignedUserIds)) {
                $repeatedTask->taskAssignments()->create(['user_id' => $assignment->user_id]);
                Mail::to($user->email)->send(new TaskAssignedMail($repeatedTask, $user));
                $assignedUserIds[] = $assignment->user_id;
            }
        }

        // Dispatch due date notification job once for the task
        if (!empty($assignedUserIds)) {
            $normalizedDueDate = $nextDueDate->format('Y-m-d H:i:s');
            $this->createTaskNotifications($repeatedTask, $normalizedDueDate);
        }

        foreach ($task->reminders as $reminder) {
            $selectedUsers = $task->taskAssignments->filter(function ($assignment) {
                return $assignment->user && $assignment->user->status === 1;
            })->pluck('user_id')->unique()->toArray();
            $this->createTaskReminders($repeatedTask, $reminder->reminder_value, $reminder->reminder_unit, $selectedUsers);
        }
    }

    private function calculateNextDueDate(Task $task): ?Carbon
    {
        $now = Carbon::now();

        switch ($task->recurrence) {
            case 'daily':
                return $now->copy()->addDay()->endOfDay();
            case 'weekly':
                return $now->copy()->addWeek()->endOfDay();
            case 'monthly':
                $nextDueDate = $now->copy()->addMonthNoOverflow();
                $taskDay = $task->created_at->day;
                $currentMonthLastDay = $nextDueDate->copy()->endOfMonth()->day;
                if ($taskDay > $currentMonthLastDay) {
                    $nextDueDate->day($currentMonthLastDay);
                } else {
                    $nextDueDate->day($taskDay);
                }
                if ($task->created_at->month == 2 && $task->created_at->day == 29 && !$nextDueDate->isLeapYear()) {
                    $nextDueDate->day(28);
                }
                return $nextDueDate->endOfDay();
            default:
                return null;
        }
    }

    private function createTaskNotifications(Task $task, string $dueDate): void
    {
        $normalizedDueDate = Carbon::parse($dueDate)->format('Y-m-d H:i:s');

        // Check if a job is already queued for this task and due date
        $existingJob = DB::table('jobs')
            ->where('payload', 'like', '%SendDueDateNotificationJob%task_id":"' . $task->id . '"%')
            ->where('payload', 'like', '%dueDate":"' . $normalizedDueDate . '"%')
            ->exists();

        if ($existingJob) {
            info("Skipping notification job for task {$task->id}, due date {$normalizedDueDate}: Already queued.");
            return;
        }

        // Create notifications for each assigned user
        $assignedUsers = $task->taskAssignments->filter(function ($assignment) {
            return $assignment->user && $assignment->user->status === 1;
        })->pluck('user_id')->unique()->toArray();

        foreach ($assignedUsers as $userId) {
            $existingNotification = Notification::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->where('type', 'due_date')
                ->where('sent_at', $normalizedDueDate)
                ->exists();

            if ($existingNotification) {
                info("Skipping notification for task {$task->id}, user {$userId}: Already exists.");
                continue;
            }

            Notification::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->where('type', 'due_date')
                ->delete();

            DB::table('notifications')->insert([
                'user_id' => $userId,
                'task_id' => $task->id,
                'type' => 'due_date',
                'message' => 'You have been assigned to task "' . $task->title . '".',
                'sent_at' => $normalizedDueDate,
            ]);
        }

        info("Dispatching SendDueDateNotificationJob for task {$task->id}, due date {$normalizedDueDate}");
        SendDueDateNotificationJob::dispatch($task, $this->dueDateChannel, $normalizedDueDate)->delay(Carbon::parse($dueDate));
    }

    public function createTaskReminders(Task $task, int $reminderTime, string $reminderUnit, array $selectedUsers): void
    {
        $dueDate = $task->due_date;
        $reminderValue = match ($reminderUnit) {
            'minutes' => Carbon::parse($dueDate)->subMinutes($reminderTime),
            'hours' => Carbon::parse($dueDate)->subHours($reminderTime),
            'days' => Carbon::parse($dueDate)->subDays($reminderTime),
            'last_30_minutes' => Carbon::parse($dueDate)->subMinutes(30),
            default => throw new \Exception('Invalid reminder unit provided.'),
        };

        if ($reminderValue->isPast()) {
            info("Skipping reminder for task {$task->id}: Reminder time {$reminderValue} is in the past.");
            return;
        }

        foreach ($selectedUsers as $userId) {
            $existingReminder = Reminder::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->where('reminder_time', $reminderValue)
                ->exists();

            if ($existingReminder) {
                info("Skipping reminder for task {$task->id}, user {$userId}: Already exists.");
                continue;
            }

            Reminder::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->delete();

            $reminder = Reminder::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'reminder_time' => $reminderValue,
                'reminder_unit' => $reminderUnit,
                'reminder_value' => $reminderTime,
            ]);

            info("Created reminder ID {$reminder->id} for task {$task->id}, user {$userId}, reminder_time {$reminderValue}");
            SendReminderJob::dispatch($reminder, $this->reminderChannel, $reminderValue)->delay($reminderValue);
        }
    }

    private function logFailedTask(?Task $task, string $e): void
    {
        try {
            DB::table('failed_crons')->insert([
                'cron_name' => 'RepeatTaskCron',
                'task_id' => $task?->id,
                'error_message' => $e,
            ]);
            info('Task logged successfully.');
        } catch (\Exception $ex) {
            info('Failed to log task: ' . $ex->getMessage());
        }
    }
}
