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
use App\Jobs\SendTaskAssignedWhatsAppJob;
use App\Jobs\SendTaskDueWhatsAppJob;

class TaskDetailsModal extends Component
{
    public string $route = '';
    public bool $isReminderEnabled = false;
    public bool $isOpen = false;
    public bool $isSaving = false;
    public ?int $taskId = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?int $category_id = null;
    public string $priority = 'low';
    public string $recurrence = 'none';
    public bool $enableRepeatTask = false;
    public ?string $due_date = null;
    public ?string $recurrence_end_date = null;
    public string $status = 'pending';
    /** @var array<int, string> */
    public array $selectedDays = [];
    public string $reminderTime = '';
    public string $reminderUnit = '';
    /** @var array<int, int> */
    public array $selectedUsers = [];
    /** @var \Illuminate\Database\Eloquent\Collection<int, Category>|null */
    public $categories;
    /** @var \Illuminate\Database\Eloquent\Collection<int, Group>|null */
    public $labels;
    public string $remark = '';
    public bool $isEditMode = false;
    public ?int $label_id = null;
    /** @var mixed */
    public $label;
    /** @var array<int, array<int, int>> */
    public array $groupUserMap = [];
    /** @var \Illuminate\Database\Eloquent\Collection<int, User>|null */
    public $users;
    /** @var array<int, string> */
    private array $reminderChannel = ['email', 'SMS'];
    /** @var array<int, string> */
    public array $dueDateChannel = ['email', 'SMS'];

    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'label_id' => 'nullable|exists:groups,id',
            'recurrence' => 'required|in:none,daily,weekly,monthly',
            'due_date' => 'nullable|date_format:d/m/Y H:i',
            'recurrence_end_date' => 'nullable|date_format:d/m/Y',
            'status' => 'required|in:pending,in_progress,completed',
            'selectedUsers' => 'required|array|min:1',
            'selectedUsers.*' => 'exists:users,id',
            'reminderTime' => 'nullable|required_with:reminderUnit|integer|min:1',
            'reminderUnit' => 'nullable|required_with:reminderTime|in:minutes,hours,days',
        ];
    }

    /** @var array<string, string> */
    protected $listeners = ['openTaskModal' => 'open', 'closeTaskModal' => 'close', 'openTaskDetailsModal' => 'edit'];

    // For custom validation messages to ensure users understand the required formats and constraints/** @return array<string, string> */
    /** @return array<string, string> */
    public function messages(): array
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

    /** @return array<string, string> */
    public function validationAttributes(): array
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
    public function open(): void
    {
        $this->categories = Category::all(); // Refresh categories
        $this->labels = Group::all(); // Refresh labels
        $this->resetForm();
        $this->isOpen = true;
        $this->dispatch('addtaskmodalopened');
    }

    public function close(): void
    {
        $this->resetForm();
        $this->isOpen = false;
    }
    public function getRandomColor(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xffffff));
    }

    // Initialize component with task data when editing an existing task
    public function mount(?int $taskId = null): void
    {
        $this->route = Route::currentRouteName() ?? '';
        $this->categories = Category::all();
        $this->labels = Group::all();
        $this->label = Group::query()->select('id', 'label')->get();
        // $this->label = Group::select('id', 'label')->get();
        // Auth user ko user list se exclude karne ke liye ->where('id', '!=', Auth::id()) add kiya hai
        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $users */
        $users = User::query()->where('status', 1)
            ->whereIn('role', ['admin', 'user', 'employee'])
            ->get();

        $this->users = new \Illuminate\Database\Eloquent\Collection(
            $users->map(function ($user) {
                /** @var User $user */
                $user->randomcolor = $this->getRandomColor();
                return $user;
            })->all()
        );
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
            $this->priority = $task->priority ?? 'low';
            $this->recurrence = $task->recurrence ?? 'none';
            $this->enableRepeatTask = (bool) ($task->enableRepeatTask ?? false);
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
    public function saveTask(): void
    {
        if ($this->isSaving) {
            return;
        }

        $this->isSaving = true;

        $parsedDueDate = filled($this->due_date) ? Carbon::createFromFormat('d/m/Y H:i', $this->due_date) : null;
        $dueDate = ($parsedDueDate instanceof Carbon) ? $parsedDueDate->format('Y-m-d H:i:00') : null;

        $parsedRecurrenceEnd = filled($this->recurrence_end_date) ? Carbon::createFromFormat('d/m/Y', $this->recurrence_end_date) : null;
        $recurrenceEndDate = ($parsedRecurrenceEnd instanceof Carbon) ? $parsedRecurrenceEnd->format('Y-m-d') : null;

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

        try {
            $this->validate();

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

            $task->parent_task_id = $task->id;
            $task->save();

            $this->handleTaskRecurrence($task);
            $this->handleTaskAssignments($task);
            $this->createInstantNotifications($task);
            $this->scheduleTaskMailFlow($task, $this->selectedUsers);

            DB::commit();

            $this->dispatch('taskCreated');
            $this->close();

            // Insert Success Message
            $this->dispatch('notify', [
                'message' => 'Task created successfully.',
                'type' => 'success'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Cache::forget($cacheKey);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Cache::forget($cacheKey);

            Log::error('Task Save Error: ' . $e->getMessage());

            // Insert Error Message
            $this->dispatch('notify', [
                'message' => 'Task Save Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        } finally {
            $this->isSaving = false;
        }
    }


    // Manage weekly task recurrence days
    private function handleTaskRecurrence(Task $task): void
    {
        DB::table('task_recurrence_days')
            ->where('task_id', $task->id)
            ->delete();

        if (in_array($this->recurrence, ['daily', 'weekly', 'monthly']) && !empty($this->selectedDays)) {
            foreach ($this->selectedDays as $day) {
                DB::table('task_recurrence_days')->insert([
                    'task_id' => $task->id,
                    'day' => $day,
                ]);
            }
        }
    }


    private function UpdatehandleTaskRecurrence(Task $task): void
    {
        DB::table('task_recurrence_days')
            ->where('task_id', $task->id)
            ->delete();

        if ($this->recurrence === 'daily' && !empty($this->selectedDays)) {
            foreach ($this->selectedDays as $day) {
                DB::table('task_recurrence_days')->insert([
                    'task_id' => $task->id,
                    'day' => $day,
                ]);
            }
        }

        if ($this->recurrence === 'weekly' && !empty($this->selectedDays)) {
            foreach ($this->selectedDays as $day) {
                DB::table('task_recurrence_days')->insert([
                    'task_id' => $task->id,
                    'day' => $day,
                ]);
            }
        }

        if ($this->recurrence === 'monthly' && !empty($this->selectedDays)) {
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
    private function handleTaskAssignments(Task $task): void
    {
        $selectedUsers = array_values(
            array_unique(
                array_filter(
                    $this->selectedUsers
                )
            )
        );

        DB::table('task_assignments')
            ->where(
                'task_id',
                $task->id
            )
            ->delete();

        foreach (
            $selectedUsers as $userId
        ) {

            DB::table(
                'task_assignments'
            )->updateOrInsert(
                [
                    'task_id' =>
                    $task->id,

                    'user_id' =>
                    $userId,
                ],
                [
                    'assigned_at' =>
                    now(),
                ]
            );
        }

        DB::afterCommit(
            function () use (
                $task,
                $selectedUsers
            ) {

                $users =
                    User::query()->whereIn(
                        'id',
                        $selectedUsers
                    )->get();

                foreach (
                    $users as $user
                ) {

                    /*
                |--------------------------------------------------------------------------
                | TASK ASSIGNED EMAIL
                |--------------------------------------------------------------------------
                */

                    if (
                        filled(
                            $user->email
                        )
                    ) {

                        $mailCacheKey =
                            'task_assigned_mail_sent_'
                            . $task->id
                            . '_'
                            . $user->id;

                        if (
                            Cache::add(
                                $mailCacheKey,
                                true,
                                now()->addDays(7)
                            )
                        ) {

                            Mail::to(
                                $user->email
                            )->queue(
                                new TaskAssignedMail(
                                    $task,
                                    $user
                                )
                            );
                        }
                    }

                    /*
                |--------------------------------------------------------------------------
                | TASK ASSIGNED WHATSAPP
                |--------------------------------------------------------------------------
                */

                    $phoneNumber =
                        $user->phone_number
                        ?? $user->mobile_number
                        ?? null;

                    if (
                        filled(
                            $phoneNumber
                        )
                    ) {

                        $whatsappCacheKey =
                            'task_assigned_whatsapp_dispatch_'
                            . $task->id
                            . '_'
                            . $user->id;

                        if (
                            Cache::add(
                                $whatsappCacheKey,
                                true,
                                now()->addDays(7)
                            )
                        ) {

                            SendTaskAssignedWhatsAppJob::dispatch(
                                $task->id,
                                $user->id
                            );
                        }
                    } else {

                        Log::warning(
                            'Task WhatsApp not dispatched: phone number missing.',
                            [
                                'task_id' =>
                                $task->id,

                                'user_id' =>
                                $user->id,
                            ]
                        );
                    }
                }
            }
        );
    }

    private function updatehandleTaskAssignments(Task $task): void
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

    // private function scheduleTaskReminderEmails(Task $task, array $selectedUsers): void
    // {
    //     $dueDate = Carbon::parse($task->due_date);

    //     Reminder::query()->where('task_id', $task->id)->delete();

    //     $schedules = [
    //         [
    //             'type' => 'daily_reminder',
    //             'time' => now()->addDay()->startOfDay(),
    //             'message' => 'Reminder: You have a pending task "' . $task->title . '".',
    //         ],
    //         [
    //             'type' => 'before_24_hours',
    //             'time' => $dueDate->copy()->subHours(24),
    //             'message' => 'Only 24 hours left to complete task "' . $task->title . '".',
    //         ],
    //         [
    //             'type' => 'before_12_hours',
    //             'time' => $dueDate->copy()->subHours(12),
    //             'message' => 'Only 12 hours left to complete task "' . $task->title . '".',
    //         ],
    //         [
    //             'type' => 'before_6_hours',
    //             'time' => $dueDate->copy()->subHours(6),
    //             'message' => 'Only 6 hours left to complete task "' . $task->title . '".',
    //         ],
    //         [
    //             'type' => 'due_date',
    //             'time' => $dueDate,
    //             'message' => 'Task "' . $task->title . '" is due now.',
    //         ],
    //     ];

    //     foreach ($selectedUsers as $userId) {
    //         foreach ($schedules as $schedule) {
    //             if ($schedule['time']->isPast()) {
    //                 continue;
    //             }

    //             $reminder = Reminder::create([
    //                 'task_id' => $task->id,
    //                 'user_id' => $userId,
    //                 'reminder_time' => $schedule['time'],
    //                 'reminder_unit' => $schedule['type'],
    //                 'reminder_value' => 0,
    //             ]);

    //             // $reminder = Reminder::query()->create([
    //             //     'task_id' => $task->id,
    //             //     'user_id' => $userId,
    //             //     'reminder_time' => $schedule['time'],
    //             //     'reminder_unit' => $schedule['type'],
    //             //     'reminder_value' => 0,
    //             // ]);

    //             SendReminderJob::dispatch(
    //                 $reminder->id,
    //                 $this->reminderChannel,
    //                 $schedule['message']
    //             )->delay($schedule['time']);
    //         }

    //         $dailyDate = now()->addDay()->startOfDay();

    //         while ($dailyDate->lt($dueDate->copy()->startOfDay())) {
    //             $reminder = Reminder::create([
    //                 'task_id' => $task->id,
    //                 'user_id' => $userId,
    //                 'reminder_time' => $dailyDate,
    //                 'reminder_unit' => 'daily_reminder',
    //                 'reminder_value' => 0,
    //             ]);

    //             SendReminderJob::dispatch(
    //                 $reminder->id,
    //                 $this->reminderChannel,
    //                 'Reminder: Task "' . $task->title . '" is still pending.'
    //             )->delay($dailyDate);

    //             $dailyDate->addDay();
    //         }
    //     }
    // }

    private function fillReminderFields(Task $task): void
    {
        /** @var Reminder|null $reminder */
        $reminder = Reminder::query()
            ->where('task_id', $task->id)
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
    public function edit(Task $taskId): void
    {
        $task = Task::findOrFail($taskId->id);
        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->category_id = $task->category_id;
        $this->priority = $task->priority ?? 'low';
        $this->label_id = $task->label_id;
        $this->enableRepeatTask = $task->recurrence !== 'none';
        $this->recurrence = $task->recurrence ?? 'none';
        $this->due_date = $task->due_date
            ? Carbon::parse((string) $task->due_date)->format('d/m/Y H:i')
            : null;
        $this->recurrence_end_date = $task->recurrence_end_date
            ? Carbon::parse((string) $task->recurrence_end_date)->format('d/m/Y')
            : null;
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

    public function updateTask(): void
    {
        if (empty($this->selectedUsers) && $this->taskId) {
            $this->selectedUsers = DB::table('task_assignments')
                ->where('task_id', $this->taskId)
                ->pluck('user_id')
                ->toArray();
        }

        $this->validate();

        $parsedDueDate = filled($this->due_date) ? Carbon::createFromFormat('d/m/Y H:i', $this->due_date) : null;
        $this->due_date = ($parsedDueDate instanceof Carbon) ? $parsedDueDate->format('Y-m-d H:i:00') : null;

        if (filled($this->recurrence_end_date)) {
            $parsedRecurrenceEnd = Carbon::createFromFormat('d/m/Y', $this->recurrence_end_date);
            $this->recurrence_end_date = ($parsedRecurrenceEnd instanceof Carbon) ? $parsedRecurrenceEnd->format('Y-m-d') : null;
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

            $task->parent_task_id = $task->id;
            $task->save();

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

            // Update Success Message
            $this->dispatch('notify', [
                'message' => 'Task updated successfully.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Task Update Error: ' . $e->getMessage());

            // Update Error Message
            $this->dispatch('notify', [
                'message' => 'Update Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function resetForm(): void
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
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.task-details-modal');
    }

    // instant assignment notification
    public function createInstantNotifications(Task $task): void
    {
        $this->selectedUsers = array_unique($this->selectedUsers);
        foreach ($this->selectedUsers as $userId) {
            Notification::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->delete();
            DB::table('notifications')->insert([
                'user_id' => $userId,
                'task_id' => $task->id,
                'type' => 'due_date',
                'message' => 'You have been assigned to task "' . $task->title . '".',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function updateInstantNotifications(Task $task): void
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
    /**
     * @param Task $task
     * @param array<int, int> $selectedUsers
     */
    public function scheduleTaskMailFlow(Task $task, array $selectedUsers): void
    {
        $selectedUsers = array_values(array_unique(array_filter($selectedUsers)));

        if (empty($selectedUsers)) {
            $selectedUsers = DB::table('task_assignments')
                ->where('task_id', $task->id)
                ->pluck('user_id')
                ->map(fn($userId) => (int) $userId)
                ->toArray();
        }

        Reminder::query()->where('task_id', $task->id)->delete();

        if ($task->status === 'completed' || empty($selectedUsers)) {
            return;
        }

        /*
         * Process 1: Due Date Mail
         * Due date mail is independent. If due date is selected and reminder is not selected,
         * this mail will still be scheduled exactly on due date/time.
         * NOTE: reminder_unit column only allows minutes/hours/days, so due-date mail is
         * stored as minutes + value 0 and identified by its due-date message/channel.
         */
        if (! empty($task->due_date)) {
            $dueDateTime = Carbon::parse($task->due_date);

            foreach ($selectedUsers as $userId) {
                $this->createAndDispatchDueDateReminder(
                    $task,
                    (int) $userId,
                    $dueDateTime->copy()
                );
            }
        }

        /*
         * Process 2: Custom Reminder Mail
         * Reminder mail is independent from due date. If due date is not selected,
         * reminder will still be sent after selected reminder time from assigned_at/created_at.
         */
        if (empty($this->reminderTime) || empty($this->reminderUnit)) {
            return;
        }

        $reminderValue = (int) $this->reminderTime;
        $reminderUnit = (string) $this->reminderUnit;

        if ($reminderValue < 1 || ! in_array($reminderUnit, ['minutes', 'hours', 'days'], true)) {
            return;
        }

        foreach ($selectedUsers as $userId) {
            $assignedAt = DB::table('task_assignments')
                ->where('task_id', $task->id)
                ->where('user_id', $userId)
                ->value('assigned_at');

            $baseTime = $assignedAt
                ? Carbon::parse($assignedAt)
                : Carbon::parse($task->created_at ?? now());

            $sendAt = match ($reminderUnit) {
                'minutes' => $baseTime->copy()->addMinutes($reminderValue),
                'hours' => $baseTime->copy()->addHours($reminderValue),
                'days' => $baseTime->copy()->addDays($reminderValue),
            };

            $this->createAndDispatchReminder(
                $task,
                (int) $userId,
                $sendAt,
                "Reminder: Your task '{$task->title}' is still pending.",
                $reminderUnit,
                $reminderValue
            );
        }
    }

    private function createAndDispatchDueDateReminder(
        Task $task,
        int $userId,
        Carbon $sendAt
    ): void {

        if (
            $sendAt->isPast()
            ||
            $task->status === 'completed'
        ) {
            return;
        }

        $reminder =
            Reminder::create([
                'task_id' =>
                $task->id,

                'user_id' =>
                $userId,

                'reminder_time' =>
                $sendAt,

                'reminder_unit' =>
                'minutes',

                'reminder_value' =>
                0,
            ]);

        SendReminderJob::dispatch(
            $reminder->id,
            $this->dueDateChannel,
            "Task '{$task->title}' is due now."
        )
            ->delay($sendAt)
            ->afterCommit();

        Log::info('Dispatching WhatsApp job for task: ' . $task->id);
        // SendTaskDueWhatsAppJob called with all 3 required arguments
        SendTaskDueWhatsAppJob::dispatch(
            $task->id,
            $userId,
            $sendAt->toDateTimeString()
        )
            ->delay($sendAt)
            ->afterCommit();
    }

    private function createAndDispatchReminder(
        Task $task,
        int $userId,
        Carbon $sendAt,
        string $message,
        string $reminderUnit,
        int $reminderValue
    ): void {
        if ($sendAt->isPast() || $task->status === 'completed') {
            return;
        }

        $reminder = Reminder::create([
            'task_id' => $task->id,
            'user_id' => $userId,
            'reminder_time' => $sendAt,
            'reminder_unit' => $reminderUnit,
            'reminder_value' => $reminderValue,
        ]);

        SendReminderJob::dispatch(
            $reminder->id,
            $this->reminderChannel,
            $message
        )->delay($sendAt);
    }

    /**
     * @param Task $task
     * @param array<int, int> $selectedUsers
     */
    public function createTaskReminders(Task $task, array $selectedUsers): void
    {
        if (empty($task->due_date)) {
            return;
        }

        $dueDate = Carbon::parse((string) $task->due_date);

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
                Reminder::query()->where('task_id', $task->id)
                    ->where('user_id', $userId)
                    ->where('reminder_time', $reminderTime)
                    ->delete();

                $model = Reminder::query()->create([
                    'task_id'        => $task->id,
                    'user_id'        => $userId,
                    'reminder_time'  => $reminderTime,
                    'reminder_unit'  => $reminder['hours'] ? 'hours' : 'minutes',
                    'reminder_value' => $reminder['hours'] ?: $reminder['minutes'],
                ]);

                SendReminderJob::dispatch(
                    $model->id,
                    $this->reminderChannel,
                    $reminderTime->toDateTimeString()
                )->delay($reminderTime);
            }
        }
    }
}
