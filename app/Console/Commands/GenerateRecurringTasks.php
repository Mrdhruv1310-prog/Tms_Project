<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;
use App\Models\Reminder;
use App\Models\User;
use App\Jobs\SendReminderJob;
use App\Mail\TaskAssignedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateRecurringTasks extends Command
{
    protected $signature = 'tasks:recurring';
    protected $description = 'Generate next occurrence of recurring tasks and forward the due date and recurrence end date.';

    public function handle()
    {
        // Un parent tasks ko fetch karo jinki recurrence set hai
        $tasks = Task::whereIn('recurrence', ['daily', 'weekly', 'monthly'])
            ->whereColumn('id', 'parent_task_id')
            ->whereNotNull('due_date')
            ->get();

        foreach ($tasks as $task) {
            // Is task ka sabse latest generated instance dhundo
            $latestTask = Task::where(function ($query) use ($task) {
                $query->where('id', $task->id)
                    ->orWhere('parent_task_id', $task->id);
            })
            ->latest('due_date')
            ->first();

            if (!$latestTask) {
                continue;
            }

            // Agar latest task ki due date abhi aayi hi nahi hai (future mein hai), toh aur wait karo
            if (Carbon::parse($latestTask->due_date)->isFuture()) {
                continue;
            }

            // Naye task ke liye Due Date calculate karo
            $nextDueDate = $this->calculateNextDate($latestTask->due_date, $latestTask->recurrence);

            if (!$nextDueDate) {
                continue;
            }

            // SECURITY CHECK: Agar next due date, recurrence_end_date ko cross kar rahi hai, toh STOP.
            if (!empty($task->recurrence_end_date)) {
                $endDateLimit = Carbon::parse($task->recurrence_end_date)->startOfDay();
                if ($nextDueDate->copy()->startOfDay()->gt($endDateLimit)) {
                    continue; // End date aa chuki hai, aur repeat nahi karna
                }
            }

            // Check karo ki kahin is due date ka task pehle se toh nahi ban gaya
            $exists = Task::where('parent_task_id', $task->id)
                ->whereDate('due_date', $nextDueDate->toDateString())
                ->exists();

            if ($exists) {
                continue;
            }

            // Naye task ke liye Recurrence End Date calculate karo
            $nextRecurrenceEndDate = null;
            if (!empty($latestTask->recurrence_end_date)) {
                $nextRecurrenceEndDate = $this->calculateNextDate($latestTask->recurrence_end_date, $latestTask->recurrence);
            }

            // Naya task create karo aur mails bhejo
            $this->createRecurringTask($latestTask, $nextDueDate, $nextRecurrenceEndDate, $task);
        }

        $this->info('Recurring tasks generated successfully.');
    }

    private function createRecurringTask(Task $latestTask, Carbon $nextDueDate, ?Carbon $nextRecurrenceEndDate, Task $parentTask)
    {
        DB::transaction(function () use ($latestTask, $nextDueDate, $nextRecurrenceEndDate, $parentTask) {

            // Task duplicate karo
            $newTask = $latestTask->replicate();
            $newTask->parent_task_id = $parentTask->id;

            // Status humesha Pending rahega
            $newTask->status = 'pending';

            // Nayi due date aur recurrence end date set karo
            $newTask->due_date = $nextDueDate;
            $newTask->recurrence_end_date = $nextRecurrenceEndDate ? $nextRecurrenceEndDate->format('Y-m-d') : null;

            $newTask->created_at = now();
            $newTask->updated_at = now();
            $newTask->save();

            $userIds = [];

            // (FIXED) Purane task ke assignments naye mein copy karo (Direct DB Table use karke)
            $oldAssignments = DB::table('task_assignments')->where('task_id', $latestTask->id)->get();
            foreach ($oldAssignments as $assignment) {
                DB::table('task_assignments')->insert([
                    'task_id' => $newTask->id,
                    'user_id' => $assignment->user_id,
                    'assigned_at' => now()
                ]);
                $userIds[] = $assignment->user_id;
            }

            // (FIXED) Recurrence days copy karo (Direct DB Table use karke)
            $oldDays = DB::table('task_recurrence_days')->where('task_id', $latestTask->id)->get();
            foreach ($oldDays as $day) {
                DB::table('task_recurrence_days')->insert([
                    'task_id' => $newTask->id,
                    'day' => $day->day
                ]);
            }

            // 1. Send Task Assigned Emails
            if (!empty($userIds)) {
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    Mail::to($user->email)->queue(new TaskAssignedMail($newTask, $user));
                }
            }

            // 2. Schedule Due Date Reminders
            $this->scheduleDueReminders($newTask, $userIds, $nextDueDate);
        });
    }

    private function calculateNextDate($dateString, $recurrence)
    {
        $date = Carbon::parse($dateString);

        switch ($recurrence) {
            case 'daily': return $date->copy()->addDay();
            case 'weekly': return $date->copy()->addWeek();
            case 'monthly': return $date->copy()->addMonthNoOverflow();
            default: return null;
        }
    }

    private function scheduleDueReminders(Task $task, array $userIds, Carbon $dueDate)
    {
        if ($dueDate->isPast() || empty($userIds)) {
            return;
        }

        foreach ($userIds as $userId) {
            $reminder = Reminder::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'reminder_time' => $dueDate,
                'reminder_unit' => 'minutes',
                'reminder_value' => 0,
            ]);

            SendReminderJob::dispatch(
                $reminder->id,
                ['email', 'SMS'],
                "Task '{$task->title}' is due now."
            )->delay($dueDate);
        }
    }
}
