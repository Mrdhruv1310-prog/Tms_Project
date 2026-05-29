<?php

namespace App\Livewire;

use App\Mail\TaskStatusUpdateMail;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Reminder;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Jobs\SendReminderJob;
use App\Jobs\SendDueDateNotificationJob;
use App\Mail\TaskAssignedMail;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendTaskUpdateJob;

class TaskDetailsModal extends Component
{
    public string $route;
    public $isReminderEnabled = false;
    public bool $isOpen = false;
    public $taskId;
    public $title;
    public $description;
    public $category_id;
    public $priority = 'low';
    public $recurrence = 'none';
    public $enableRepeatTask = false;
    public $due_date;
    public $recurrence_end_date;
    public $status = 'pending';
    public $selectedDays = [];
    public $reminderTime = '';
    public $reminderUnit;
    public $selectedUsers = [];
    public $categories;
    public $labels;
    public string $remark;
    public $isEditMode = false;
    public $label_id;
    public $label, $groupUserMap = [];
    public $users;
    private $reminderChannel = ['email', 'SMS'];
    public $dueDateChannel = ['email', 'SMS'];
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'label_id' => 'nullable|exists:groups,id',
            'recurrence' => 'required|in:none,daily,weekly,monthly',
            'due_date' => 'required|date_format:d/m/Y H:i|after:now',
            'recurrence_end_date' => 'nullable|date_format:d/m/Y|after:today',
            'status' => 'required|in:pending,in_progress,completed',
            'selectedUsers' => 'required|array|min:1',
            'selectedUsers.*' => 'exists:users,id',
        ];
    }
    protected $listeners = ['openTaskModal' => 'open', 'closeTaskModal' => 'close', 'openTaskDetailsModal' => 'edit'];

    // For custom validation messages to ensure users understand the required formats and constraints
    public function messages()
    {
        return [
            'title.required' => 'Please enter task title.',
            'title.string' => 'Task title must be valid text.',
            'title.max' => 'Task title may not be greater than 255 characters.',

            'description.required' => 'Please enter task description.',
            'description.string' => 'Task description must be valid text.',
            'description.max' => 'Task description may not be greater than 1000 characters.',

            'category_id.required' => 'Please select category.',
            'category_id.exists' => 'Selected category is invalid.',

            'label_id.exists' => 'Selected group is invalid.',

            'priority.required' => 'Please select priority.',
            'priority.in' => 'Priority must be Low, Medium, or High.',

            'recurrence.required' => 'Please select recurrence.',
            'recurrence.in' => 'Recurrence must be None, Daily, Weekly, or Monthly.',

            'due_date.required' => 'Please select due date and time.',
            'due_date.date_format' => 'Due date must be in this format: dd/mm/yyyy hh:mm.',
            'due_date.after' => 'Due date must be a future date and time.',

            'recurrence_end_date.date_format' => 'Recurrence end date must be in this format: dd/mm/yyyy.',
            'recurrence_end_date.after' => 'Recurrence end date must be after today.',

            'status.required' => 'Task status is required.',
            'status.in' => 'Task status is invalid.',

            'selectedUsers.required' => 'Please select at least one user.',
            'selectedUsers.array' => 'Selected users must be valid.',
            'selectedUsers.min' => 'Please select at least one user.',
            'selectedUsers.*.exists' => 'One selected user is invalid.',
        ];
    }

    public function validationAttributes()
    {
        return [
            'title' => 'task title',
            'description' => 'description',
            'category_id' => 'category',
            'priority' => 'priority',
            'label_id' => 'group',
            'recurrence' => 'recurrence',
            'due_date' => 'due date',
            'recurrence_end_date' => 'recurrence end date',
            'selectedUsers' => 'users',
        ];
    }

    // Opens modal and resets form when creating a new task
    public function open()
    {
        $this->categories = Category::all(); // Refresh categories
        $this->labels = Group::all(); // Refresh labels
        $this->resetForm();
        $this->isOpen = true;
        $this->dispatch('addtaskmodalopened');
    }

    public function close()
    {
        $this->resetForm();
        $this->isOpen = false;
    }
    public function getRandomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xffffff));
    }

    // Initialize component with task data when editing an existing task
    public function mount($taskId = null)
    {
        $this->route = Route::currentRouteName();
        $this->categories = Category::all();
        $this->labels = Group::all();
        $this->label = Group::select('id', 'label')->get();

        $this->users = User::where('status', 1)
            ->get()
            ->map(function ($user) {
                $user->randomcolor = $this->getRandomColor();
                return $user;
            });
        $this->groupUserMap = GroupUser::all()
            ->groupBy('group_id')
            ->map(fn($items) => $items->pluck('user_id')->toArray())
            ->toArray();
        if ($taskId) {
            $task = Task::findOrFail($taskId);
            $this->taskId = $task->id;
            $this->title = $task->title;
            $this->description = $task->description;
            $this->category_id = $task->category_id;
            $this->label_id = $task->label_id;
            $this->priority = $task->priority;
            $this->recurrence = $task->recurrence;
            $this->enableRepeatTask = $task->enableRepeatTask;
            $this->recurrence_end_date = $task->recurrence_end_date;
            $this->due_date = $task->due_date;
            $this->status = $task->status;
        }
    }

    // Create or update task workflow with notifications and reminders
    // public function saveTask()
    // {
    //     $this->validate();

    //     $this->due_date = Carbon::createFromFormat('d/m/Y H:i',$this->due_date)->format('Y-m-d H:i:00');

    //     if ($this->recurrence_end_date) {
    //         $this->recurrence_end_date = Carbon::createFromFormat('d/m/Y',$this->recurrence_end_date)->format('Y-m-d');
    //     } else {
    //         $this->recurrence_end_date = null;
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $isUpdate = !empty($this->taskId);
    //         $oldTask = Task::find($this->taskId);
    //         $oldDueDate = $oldTask ? $oldTask->due_date : null;

    //         $task = Task::updateOrCreate(
    //             ['id' => $this->taskId],
    //             [
    //                 'title' => $this->title,
    //                 'description' => $this->description,
    //                 'category_id' => $this->category_id,
    //                 'priority' => $this->priority,
    //                 'label_id' => $this->label_id,
    //                 'recurrence' => $this->recurrence,
    //                 'due_date' => $this->due_date,
    //                 'recurrence_end_date' => $this->recurrence_end_date,
    //                 'status' => $this->status,
    //                 'user_id' => Auth::id(),
    //             ]
    //         );

    //         $this->handleTaskRecurrence($task);
    //         $this->handleTaskAssignments($task);
    //         $this->handleNotificationsAndReminders($task);

    //         DB::commit();

    //         if ($oldDueDate !== $this->due_date) {
    //             SendDueDateNotificationJob::dispatch(
    //                 $task,
    //                 $this->dueDateChannel,
    //                 $this->due_date
    //             )->delay(Carbon::parse($task->due_date));
    //         }

    //         if ($isUpdate) {
    //             $assignedUsers = $task->assignedUsers;
    //             foreach ($assignedUsers as $user) {
    //                 SendTaskUpdateJob::dispatch(
    //                     $task,
    //                     $user,
    //                     $task->status,
    //                     'Task details updated successfully.'
    //                 );
    //             }
    //         }

    //         $this->dispatch('taskCreated');
    //         $this->close();

    //         $message = $isUpdate
    //             ? 'Task updated successfully.'
    //             : 'Task created successfully.';
    //         $this->notify($message, 'success');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Task Save Error: ' . $e->getMessage());
    //         $this->notify(
    //             'An error occurred while saving task.',
    //             'error'
    //         );
    //     }
    // }
    public function saveTask()
    {
        $this->validate();

        $this->due_date = Carbon::createFromFormat('d/m/Y H:i', $this->due_date)->format('Y-m-d H:i:00');

        if ($this->recurrence_end_date) {
            $this->recurrence_end_date = Carbon::createFromFormat('d/m/Y', $this->recurrence_end_date)->format('Y-m-d');
        } else {
            $this->recurrence_end_date = null;
        }

        DB::beginTransaction();

        try {
            $task = Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'priority' => $this->priority,
                'label_id' => $this->label_id,
                'recurrence' => $this->recurrence,
                'due_date' => $this->due_date,
                'recurrence_end_date' => $this->recurrence_end_date,
                'status' => $this->status,
                'user_id' => Auth::id(),
            ]);

            $this->handleTaskRecurrence($task);
            $this->handleTaskAssignments($task);
            $this->handleNotificationsAndReminders($task);

            DB::commit();

            $this->dispatch('taskCreated');
            $this->close();

            $this->notify(
                'Task created successfully.',
                'success'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Task Save Error: ' . $e->getMessage());
            $this->notify(
                'An error occurred while saving task.',
                'error'
            );
        }
    }

    // Manage weekly task recurrence days
    private function handleTaskRecurrence(Task $task)
    {
        if ($this->recurrence === 'weekly' && !empty($this->selectedDays)) {
            DB::table('task_recurrence_days')
                ->where('task_id', $task->id)
                ->delete();
            foreach ($this->selectedDays as $day) {
                DB::table('task_recurrence_days')->insert(['task_id' => $task->id, 'day' => $day]);
            }
        } else {
            DB::table('task_recurrence_days')
                ->where('task_id', $task->id)
                ->delete();
        }
    }


    private function UpdatehandleTaskRecurrence(Task $task)
    {
        DB::table('task_recurrence_days')
            ->where('task_id', $task->id)
            ->delete();

        if ($this->recurrence === 'weekly' && !empty($this->selectedDays)) {
            foreach ($this->selectedDays as $day) {
                DB::table('task_recurrence_days')->insert([
                    'task_id' => $task->id,
                    'day' => $day,
                ]);
            }
        }
    }


    // Assign tasks and send emails to assigned users
    private function handleTaskAssignments(Task $task)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Delete existing task assignments
            DB::table('task_assignments')
                ->where('task_id', $task->id)
                ->delete();

            // Process assignments if there are selected users
            if (!empty($this->selectedUsers)) {
                // Ensure unique user IDs
                $this->selectedUsers = array_unique($this->selectedUsers);

                foreach ($this->selectedUsers as $userId) {
                    // Insert new assignment
                    DB::table('task_assignments')->insert([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                        'assigned_at' => now(),
                    ]);

                    // Send email to the assigned user
                    $user = User::find($userId);
                    if ($user) {
                        Mail::to($user->email)->queue(new TaskAssignedMail($task, $user));
                    } else {
                        // Log or handle invalid user ID
                        Log::warning("User with ID {$userId} not found for task assignment.");
                    }
                }
            }

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Roll back the transaction on error
            DB::rollBack();

            // Log the error for debugging
            Log::error("Failed to handle task assignments for task ID {$task->id}: {$e->getMessage()}");

            // Optionally, rethrow or handle the error based on your needs
            throw new \Exception("Unable to process task assignments. Please try again later.");
        }
    }

    private function updatehandleTaskAssignments(Task $task)
    {
        try {

            DB::beginTransaction();

            if (!empty($this->selectedUsers)) {

                $this->selectedUsers = array_unique($this->selectedUsers);

                foreach ($this->selectedUsers as $userId) {

                    // CHECK EXISTING ASSIGNMENT
                    $exists = DB::table('task_assignments')
                        ->where('task_id', $task->id)
                        ->where('user_id', $userId)
                        ->exists();

                    // UPDATE ONLY EXISTING
                    if ($exists) {

                        DB::table('task_assignments')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->update([
                                'assigned_at' => now(),
                            ]);
                    }

                    // FIND USER
                    $user = User::find($userId);

                    if ($user) {

                        $status = $user->status ?? 'pending';

                        $remark = $user->remark ?? '';

                        // SEND MAIL
                        Mail::to($user->email)
                            ->queue(
                                new TaskStatusUpdateMail(
                                    $task,
                                    $user,
                                    $status,
                                    $remark
                                )
                            );
                    } else {

                        Log::warning(
                            "User with ID {$userId} not found."
                        );
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                "Failed to update task assignments for task ID {$task->id}: {$e->getMessage()}"
            );

            throw new \Exception(
                "Unable to update task assignments."
            );
        }
    }

    // Handle task notifications and reminders
    private function handleNotificationsAndReminders(Task $task): void
    {
        $this->createInstantNotifications($task);
        $this->selectedUsers = array_unique($this->selectedUsers);
        $this->scheduleTaskMailFlow($task, $this->selectedUsers);
    }

    private function UpdatehandleNotificationsAndReminders(Task $task): void
    {
        $this->updateInstantNotifications($task);
        $this->selectedUsers = array_unique($this->selectedUsers);
        $this->scheduleTaskMailFlow($task, $this->selectedUsers);
    }

    private function scheduleTaskReminderEmails(Task $task, array $selectedUsers): void
    {
        $dueDate = Carbon::parse($task->due_date);

        Reminder::where('task_id', $task->id)->delete();

        $schedules = [
            [
                'type' => 'daily_reminder',
                'time' => now()->addDay()->startOfDay(),
                'message' => 'Reminder: You have a pending task "' . $task->title . '".',
            ],
            [
                'type' => 'before_24_hours',
                'time' => $dueDate->copy()->subHours(24),
                'message' => 'Only 24 hours left to complete task "' . $task->title . '".',
            ],
            [
                'type' => 'before_12_hours',
                'time' => $dueDate->copy()->subHours(12),
                'message' => 'Only 12 hours left to complete task "' . $task->title . '".',
            ],
            [
                'type' => 'before_6_hours',
                'time' => $dueDate->copy()->subHours(6),
                'message' => 'Only 6 hours left to complete task "' . $task->title . '".',
            ],
            [
                'type' => 'due_date',
                'time' => $dueDate,
                'message' => 'Task "' . $task->title . '" is due now.',
            ],
        ];

        foreach ($selectedUsers as $userId) {
            foreach ($schedules as $schedule) {
                if ($schedule['time']->isPast()) {
                    continue;
                }

                $reminder = Reminder::create([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'reminder_time' => $schedule['time'],
                    'reminder_unit' => $schedule['type'],
                    'reminder_value' => 0,
                ]);

                SendReminderJob::dispatch(
                    $reminder,
                    $this->reminderChannel,
                    $schedule['message']
                )->delay($schedule['time']);
            }

            $dailyDate = now()->addDay()->startOfDay();

            while ($dailyDate->lt($dueDate->copy()->startOfDay())) {
                $reminder = Reminder::create([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'reminder_time' => $dailyDate,
                    'reminder_unit' => 'daily_reminder',
                    'reminder_value' => 0,
                ]);

                SendReminderJob::dispatch(
                    $reminder,
                    $this->reminderChannel,
                    'Reminder: Task "' . $task->title . '" is still pending.'
                )->delay($dailyDate);

                $dailyDate->addDay();
            }
        }
    }

    // Handle Task Edit and Update the form with existing task details
    public function edit(Task $taskId)
    {
        $task = Task::findOrFail($taskId->id);
        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->category_id = $task->category_id;
        $this->priority = $task->priority;
        $this->label_id = $task->label_id;
        $this->enableRepeatTask = $task->recurrence !== 'none';
        $this->recurrence = $task->recurrence;
        $this->due_date = Carbon::parse($task->due_date)->format('d/m/Y H:i');
        $this->recurrence_end_date = $task->recurrence_end_date ? Carbon::parse($task->recurrence_end_date)->format('d/m/Y') : null;
        $this->status = $task->status;
        $this->isEditMode = true;

        $reminder = $task->reminder;
        if ($reminder) {
            $this->reminderTime = $reminder->reminder_value;
            $this->reminderUnit = $reminder->reminder_unit;
        }
        $this->selectedUsers = DB::table('task_assignments')
            ->where('task_id', $this->taskId)
            ->pluck('user_id')
            ->toArray();
        $this->selectedDays = DB::table('task_recurrence_days')
            ->where('task_id', $this->taskId)
            ->pluck('day')
            ->toArray();
        $this->isOpen = true;
    }

    public function updateTask()
    {
        if (empty($this->selectedUsers) && $this->taskId) {
            $this->selectedUsers = DB::table('task_assignments')
                ->where('task_id', $this->taskId)
                ->pluck('user_id')
                ->toArray();
        }

        $this->validate();

        $this->due_date = Carbon::createFromFormat(
            'd/m/Y H:i',
            $this->due_date
        )->format('Y-m-d H:i:00');

        if ($this->recurrence_end_date) {
            $this->recurrence_end_date = Carbon::createFromFormat(
                'd/m/Y',
                $this->recurrence_end_date
            )->format('Y-m-d');
        } else {
            $this->recurrence_end_date = null;
        }

        DB::beginTransaction();

        try {
            $task = Task::findOrFail($this->taskId);

            $task->update([
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'priority' => $this->priority,
                'label_id' => $this->label_id,
                'recurrence' => $this->recurrence,
                'due_date' => $this->due_date,
                'recurrence_end_date' => $this->recurrence_end_date,
                'status' => $this->status,
            ]);

            $this->updatehandleTaskAssignments($task);
            $this->UpdatehandleTaskRecurrence($task);
            $this->UpdatehandleNotificationsAndReminders($task);

            DB::commit();

            $task->refresh();

            foreach ($task->assignedUsers as $user) {
                SendTaskUpdateJob::dispatch(
                    $task,
                    $user,
                    'Task details updated successfully.'
                );
            }

            $this->dispatch('taskUpdated');

            $this->close();

            $this->notify(
                'Task updated successfully.',
                'success'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Task Update Error: ' . $e->getMessage());

            $this->notify(
                'Update Error: ' . $e->getMessage(),
                'error'
            );
        }
    }

    public function resetForm()
    {
        $this->taskId = null;
        $this->title = '';
        $this->description = '';
        $this->category_id = null;
        $this->label_id = null;
        $this->priority = 'low';
        $this->recurrence = 'none';
        $this->due_date = null;
        $this->recurrence_end_date = null;
        $this->status = 'pending';
        $this->selectedUsers = [];
        $this->selectedDays = [];
        $this->reminderTime = '';
        $this->reminderUnit = null;
        $this->isEditMode = false;
        $this->isReminderEnabled = false;
        $this->enableRepeatTask = false;
        $this->groupUserMap = GroupUser::all()
            ->groupBy('group_id')
            ->map(fn($items) => $items->pluck('user_id')->toArray())
            ->toArray();
    }
    public function render()
    {
        return view('livewire.task-details-modal');
    }

    // instant assignment notification
    public function createInstantNotifications(Task $task)
    {
        $this->selectedUsers = array_unique($this->selectedUsers);
        foreach ($this->selectedUsers as $userId) {
            Notification::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->delete();
            DB::table('notifications')->insert(['user_id' => $userId, 'task_id' => $task->id, 'type' => 'due_date', 'message' => 'You have been assigned to task "' . $task->title . '".', 'sent_at' => $task->due_date]);
        }
    }

    public function updateInstantNotifications(Task $task)
    {
        $this->selectedUsers = array_unique($this->selectedUsers);
        foreach ($this->selectedUsers as $userId) {
            Notification::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->delete();
            DB::table('notifications')->update(['user_id' => $userId, 'task_id' => $task->id, 'type' => 'due_date', 'message' => 'You have been assigned to task "' . $task->title . '".', 'sent_at' => $task->due_date]);
        }
    }
    // Send Reminder Email Notification
    // public function createTaskReminders($task, $reminderTime, $reminderUnit, $selectedUsers)
    // {
    //     $dueDate = $task->due_date;
    //     switch ($reminderUnit) {
    //         case 'minutes':
    //             $reminderValue = Carbon::parse($dueDate)->subMinutes($reminderTime);
    //             break;
    //         case 'hours':
    //             $reminderValue = Carbon::parse($dueDate)->subHours($reminderTime);
    //             break;
    //         case 'days':
    //             $reminderValue = Carbon::parse($dueDate)->subDays($reminderTime);
    //             break;
    //         default:
    //             throw new \Exception('Invalid reminder unit provided.');
    //     }
    //     if ($reminderValue->isPast()) {
    //         throw new \Exception('Reminder time cannot be in the past.');
    //     }

    //     foreach ($selectedUsers as $userId) {
    //         Reminder::where('task_id', $task->id)
    //             ->where('user_id', $userId)
    //             ->delete();

    //         $reminder = Reminder::create([
    //             'task_id' => $task->id,
    //             'user_id' => $userId,
    //             'reminder_time' => $reminderValue,
    //             'reminder_unit' => $reminderUnit,
    //             'reminder_value' => $reminderTime,
    //         ]);

    //         SendReminderJob::dispatch($reminder, $this->reminderChannel, $reminderValue)
    //             ->delay($reminderValue);
    //     }
    // }
    private function scheduleTaskMailFlow(Task $task, array $selectedUsers): void
    {
        $dueDate = Carbon::parse($task->due_date);

        Reminder::where('task_id', $task->id)->delete();

        foreach ($selectedUsers as $userId) {

            $dailyDate = now()->copy()->addDay();

            while ($dailyDate->lt($dueDate->copy()->startOfDay())) {
                $this->createAndDispatchReminder(
                    $task,
                    $userId,
                    $dailyDate,
                    "Reminder: Your task '{$task->title}' is still pending. Due date is {$dueDate->format('d-m-Y H:i')}."
                );

                $dailyDate->addDay();
            }

            $this->createAndDispatchReminder(
                $task,
                $userId,
                $dueDate->copy()->subHours(24),
                "Only 24 hours left to complete your task '{$task->title}'."
            );

            $this->createAndDispatchReminder(
                $task,
                $userId,
                $dueDate->copy()->subHours(12),
                "Only 12 hours left to complete your task '{$task->title}'."
            );

            $this->createAndDispatchReminder(
                $task,
                $userId,
                $dueDate->copy()->subHours(6),
                "Only 6 hours left to complete your task '{$task->title}'."
            );

            $this->createAndDispatchReminder(
                $task,
                $userId,
                $dueDate,
                "Due Date Alert: Your task '{$task->title}' is due now."
            );
        }
    }

    private function createAndDispatchReminder(Task $task, int $userId, Carbon $sendAt, string $message): void
    {
        if ($sendAt->isPast()) {
            return;
        }

        $reminder = Reminder::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'reminder_time' => $sendAt,
            'reminder_unit' => 'minutes',
            'reminder_value' => 0,
        ]);

        SendReminderJob::dispatch(
            $reminder->id,
            $this->reminderChannel,
            $message
        )->delay($sendAt);
    }

    public function createTaskReminders(Task $task, $selectedUsers)
    {
        $dueDate = Carbon::parse($task->due_date);

        $reminders = [
            ['hours' => 24, 'minutes' => 0, 'label' => 'Only 24 hours left to complete your task'],
            ['hours' => 6,  'minutes' => 0, 'label' => 'Only 6 hours left to complete your task'],
            ['hours' => 0,  'minutes' => 30, 'label' => 'Only 30 minutes left to complete your task'],
            ['hours' => 0,  'minutes' => 0,  'label' => 'Task is due now'],
        ];

        foreach ($reminders as $reminder) {

            $reminderTime = $dueDate->copy()
                ->subHours($reminder['hours'])
                ->subMinutes($reminder['minutes']);

            if ($reminderTime->isPast()) {
                continue;
            }

            foreach ($selectedUsers as $userId) {

                Reminder::where('task_id', $task->id)
                    ->where('user_id', $userId)
                    ->where('reminder_time', $reminderTime)
                    ->delete();

                $model = Reminder::create([
                    'task_id'        => $task->id,
                    'user_id'        => $userId,
                    'reminder_time'  => $reminderTime,
                    'reminder_unit'  => $reminder['hours'] ? 'hours' : 'minutes',
                    'reminder_value' => $reminder['hours'] ?: $reminder['minutes'],
                ]);

                SendReminderJob::dispatch(
                    $model,
                    $this->reminderChannel,
                    $reminderTime
                )->delay($reminderTime);
            }
        }
    }
}
