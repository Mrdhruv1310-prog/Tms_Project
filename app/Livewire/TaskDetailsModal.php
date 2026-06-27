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
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Jobs\SendReminderJob;
use App\Jobs\SendDueDateNotificationJob;
use App\Mail\TaskAssignedMail;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendTaskUpdateJob;
use App\Services\WhatsAppService;

class TaskDetailsModal extends Component
{
    public string $route;
    public $isReminderEnabled = false;
    public bool $isOpen = false;
    public bool $isSaving = false;
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
    public $reminderUnit = '';
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
            'due_date' => 'nullable|date_format:d/m/Y H:i|after:now',
            'recurrence_end_date' => 'nullable|date_format:d/m/Y|after:today',
            'status' => 'required|in:pending,in_progress,completed',
            'selectedUsers' => 'required|array|min:1',
            'selectedUsers.*' => 'exists:users,id',
            'reminderTime' => 'nullable|required_with:reminderUnit|integer|min:1',
            'reminderUnit' => 'nullable|required_with:reminderTime|in:minutes,hours,days',
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
            'reminderTime.required_with' => 'Please enter reminder time.',
            'reminderTime.integer' => 'Reminder time must be a valid number.',
            'reminderTime.min' => 'Reminder time must be at least 1.',
            'reminderUnit.required_with' => 'Please select reminder unit.',
            'reminderUnit.in' => 'Reminder unit must be Minutes, Hours, or Days.',
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
            'reminderTime' => 'reminder time',
            'reminderUnit' => 'reminder unit',
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
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;
        $cacheKey = null;

        try {
            $this->validate();

            $dueDate = filled($this->due_date)
                ? Carbon::createFromFormat('d/m/Y H:i', $this->due_date)->format('Y-m-d H:i:00')
                : null;

            $recurrenceEndDate = $this->recurrence_end_date
                ? Carbon::createFromFormat('d/m/Y', $this->recurrence_end_date)->format('Y-m-d')
                : null;

            $this->selectedUsers = array_values(array_unique(array_filter($this->selectedUsers)));

            $cacheKey = 'task-save-lock:' . Auth::id() . ':' . md5(
                $this->title . '|' .
                    $this->description . '|' .
                    $this->category_id . '|' .
                    $this->priority . '|' .
                    ($this->label_id ?: 'null') . '|' .
                    $this->recurrence . '|' .
                    $dueDate . '|' .
                    ($recurrenceEndDate ?: 'null') . '|' .
                    implode(',', $this->selectedUsers)
            );

            if (! Cache::add($cacheKey, true, now()->addSeconds(10))) {
                return;
            }

            DB::beginTransaction();

            $task = Task::create([
                'title' => $this->title,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'priority' => $this->priority,
                'label_id' => $this->label_id ?: null,
                'recurrence' => $this->recurrence,
                'due_date' => $dueDate,
                'recurrence_end_date' => $recurrenceEndDate,
                'status' => $this->status,
                'user_id' => Auth::id(),
            ]);

            $this->handleTaskRecurrence($task);
            $this->handleTaskAssignments($task);
            $this->createInstantNotifications($task);
            $this->scheduleTaskMailFlow($task, $this->selectedUsers);

            DB::commit();

            $this->dispatch('taskCreated');
            $this->close();

            $this->notify('Task created successfully.', 'success');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($cacheKey) {
                Cache::forget($cacheKey);
            }

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($cacheKey) {
                Cache::forget($cacheKey);
            }

            Log::error('Task Save Error: ' . $e->getMessage());

            $this->notify('Task Save Error: ' . $e->getMessage(), 'error');
        } finally {
            $this->isSaving = false;
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


    // private function sendTaskAssignedMails(Task $task): void
    // {
    //     foreach ($this->selectedUsers as $userId) {
    //         $user = User::find($userId);

    //         if (! $user) {
    //             continue;
    //         }

    //         try {
    //             Mail::to($user->email)->send(new TaskAssignedMail($task, $user));
    //         } catch (\Throwable $e) {
    //             Log::error('Task Assigned Mail Error: ' . $e->getMessage());
    //         }
    //     }
    // }

    // private function safeScheduleTaskMailFlow(Task $task): void
    // {
    //     try {
    //         $this->selectedUsers = array_unique($this->selectedUsers);
    //         $this->scheduleTaskMailFlow($task, $this->selectedUsers);
    //     } catch (\Throwable $e) {
    //         Log::error('Task Reminder Schedule Error: ' . $e->getMessage());
    //     }
    // }
    // Assign tasks and send emails to assigned users
    private function handleTaskAssignments(Task $task)
    {
        $selectedUsers = array_values(array_unique($this->selectedUsers));

        DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->delete();

        foreach ($selectedUsers as $userId) {
            DB::table('task_assignments')->updateOrInsert(
                [
                    'task_id' => $task->id,
                    'user_id' => $userId,
                ],
                [
                    'assigned_at' => now(),
                ]
            );
        }

        DB::afterCommit(function () use ($task, $selectedUsers) {
            $users = User::whereIn('id', $selectedUsers)->get();

            foreach ($users as $user) {
                $cacheKey = 'task_assigned_mail_sent_' . $task->id . '_' . $user->id;

                if (! Cache::add($cacheKey, true, now()->addDays(7))) {
                    continue;
                }

                Mail::to($user->email)->queue(new TaskAssignedMail($task, $user));
            }
        });
    }

    private function updatehandleTaskAssignments(Task $task)
    {
        try {
            DB::beginTransaction();

            if (!empty($this->selectedUsers)) {

                $this->selectedUsers = array_unique($this->selectedUsers);

                foreach ($this->selectedUsers as $userId) {

                    $exists = DB::table('task_assignments')
                        ->where('task_id', $task->id)
                        ->where('user_id', $userId)
                        ->exists();

                    if ($exists) {

                        DB::table('task_assignments')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->update([
                                'assigned_at' => now(),
                            ]);
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
                    $reminder->id,
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
                    $reminder->id,
                    $this->reminderChannel,
                    'Reminder: Task "' . $task->title . '" is still pending.'
                )->delay($dailyDate);

                $dailyDate->addDay();
            }
        }
    }


    private function fillReminderFields(Task $task): void
    {
        /*
         * Due date reminders are saved with reminder_value = 0.
         * Custom reminders are saved with minutes/hours/days + reminder_value > 0.
         * So while opening edit/update form, always load only custom reminder values.
         */
        $reminder = Reminder::where('task_id', $task->id)
            ->whereIn('reminder_unit', ['minutes', 'hours', 'days'])
            ->whereNotNull('reminder_value')
            ->where('reminder_value', '>', 0)
            ->orderByDesc('id')
            ->first();

        if (! $reminder) {
            $this->isReminderEnabled = false;
            $this->reminderTime = '';
            $this->reminderUnit = '';
            return;
        }

        $this->isReminderEnabled = true;
        $this->reminderTime = (string) (int) $reminder->reminder_value;
        $this->reminderUnit = (string) $reminder->reminder_unit;
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
        $this->due_date = $task->due_date
            ? Carbon::parse($task->due_date)->format('d/m/Y H:i')
            : null;
        $this->recurrence_end_date = $task->recurrence_end_date ? Carbon::parse($task->recurrence_end_date)->format('d/m/Y') : null;
        $this->status = $task->status;
        $this->isEditMode = true;

        $this->fillReminderFields($task);
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

        $this->due_date = filled($this->due_date)
            ? Carbon::createFromFormat(
                'd/m/Y H:i',
                $this->due_date
            )->format('Y-m-d H:i:00')
            : null;

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

            $this->label_id = filled($this->label_id)
                ? (int) $this->label_id
                : null;

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
                    $task->status,
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
        $this->reminderUnit = '';
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
        $selectedUsers = array_values(array_unique(array_filter($selectedUsers)));

        if (empty($selectedUsers)) {
            $selectedUsers = DB::table('task_assignments')
                ->where('task_id', $task->id)
                ->pluck('user_id')
                ->map(fn($userId) => (int) $userId)
                ->toArray();
        }

        Reminder::where('task_id', $task->id)->delete();

        if ($task->status === 'completed' || empty($selectedUsers)) {
            return;
        }

        $pendingJobs = [];
        $dueDateTime = filled($task->due_date)
            ? Carbon::parse($task->due_date, config('app.timezone'))->seconds(0)
            : null;

        /*
         * 1) Due-date mail: scheduled exactly on due date/time.
         * It is saved with reminder_value = 0 so edit form can ignore it and load only custom reminders.
         */
        if ($dueDateTime && $dueDateTime->isFuture()) {
            foreach ($selectedUsers as $userId) {
                $reminder = $this->createReminderRecord(
                    $task,
                    (int) $userId,
                    $dueDateTime->copy(),
                    'minutes',
                    0
                );

                $pendingJobs[] = [
                    'reminder_id' => $reminder->id,
                    'channels' => $this->dueDateChannel,
                    'message' => "Task '{$task->title}' is due now.",
                    'send_at' => $dueDateTime->copy(),
                ];
            }
        }

        /*
         * 2) Custom reminder mail: when due date exists, send BEFORE due date.
         * If due date is empty, send after task assigned/created time.
         */
        if (! empty($this->reminderTime) && ! empty($this->reminderUnit)) {
            $reminderValue = (int) $this->reminderTime;
            $reminderUnit = (string) $this->reminderUnit;

            if ($reminderValue >= 1 && in_array($reminderUnit, ['minutes', 'hours', 'days'], true)) {
                foreach ($selectedUsers as $userId) {
                    $sendAt = $this->resolveCustomReminderSendAt(
                        $task,
                        (int) $userId,
                        $reminderValue,
                        $reminderUnit,
                        $dueDateTime
                    );

                    if (! $sendAt || $sendAt->isPast()) {
                        continue;
                    }

                    $reminder = $this->createReminderRecord(
                        $task,
                        (int) $userId,
                        $sendAt->copy(),
                        $reminderUnit,
                        $reminderValue
                    );

                    $pendingJobs[] = [
                        'reminder_id' => $reminder->id,
                        'channels' => $this->reminderChannel,
                        'message' => "Reminder: {$reminderValue} {$reminderUnit} left for task '{$task->title}'.",
                        'send_at' => $sendAt->copy(),
                    ];
                }
            }
        }

        /*
         * Important for VPS/queue worker:
         * Dispatch jobs only after DB commit, otherwise a fast queue worker can run before
         * the reminder row is committed and mail will not be sent.
         */
        DB::afterCommit(function () use ($pendingJobs) {
            foreach ($pendingJobs as $job) {
                SendReminderJob::dispatch(
                    $job['reminder_id'],
                    $job['channels'],
                    $job['message']
                )->delay($job['send_at']);
            }
        });
    }

    private function resolveCustomReminderSendAt(
        Task $task,
        int $userId,
        int $reminderValue,
        string $reminderUnit,
        ?Carbon $dueDateTime
    ): ?Carbon {
        if ($dueDateTime) {
            return match ($reminderUnit) {
                'minutes' => $dueDateTime->copy()->subMinutes($reminderValue),
                'hours' => $dueDateTime->copy()->subHours($reminderValue),
                'days' => $dueDateTime->copy()->subDays($reminderValue),
                default => null,
            };
        }

        $assignedAt = DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->value('assigned_at');

        $baseTime = $assignedAt
            ? Carbon::parse($assignedAt, config('app.timezone'))
            : Carbon::parse($task->created_at ?? now(), config('app.timezone'));

        return match ($reminderUnit) {
            'minutes' => $baseTime->copy()->addMinutes($reminderValue),
            'hours' => $baseTime->copy()->addHours($reminderValue),
            'days' => $baseTime->copy()->addDays($reminderValue),
            default => null,
        };
    }

    private function createReminderRecord(
        Task $task,
        int $userId,
        Carbon $sendAt,
        string $reminderUnit,
        int $reminderValue
    ): Reminder {
        return Reminder::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'reminder_time' => $sendAt->format('Y-m-d H:i:s'),
            'reminder_unit' => $reminderUnit,
            'reminder_value' => $reminderValue,
        ]);
    }

    public function createTaskReminders(Task $task, $selectedUsers)
    {
        $dueDate = Carbon::parse($task->due_date);

        $reminders = [
            ['hours' => 24, 'minutes' => 0, 'label' => 'Only 24 hours left to complete your task'],
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
