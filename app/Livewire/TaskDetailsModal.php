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
    public string $remark = '';
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
            'due_date' => 'nullable',
            'recurrence_end_date' => 'nullable',
            'status' => 'required|in:pending,in_progress,completed',
            'selectedUsers' => 'required|array|min:1',
            'selectedUsers.*' => 'exists:users,id',
            'reminderTime' => 'nullable|required_with:reminderUnit|integer|min:1',
            'reminderUnit' => 'nullable|required_with:reminderTime|in:minutes,hours,days',
        ];
    }

    protected $listeners = [
        'openTaskModal' => 'open',
        'closeTaskModal' => 'close',
        'openTaskDetailsModal' => 'edit'
    ];

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

    public function open()
    {
        $this->categories = Category::all();
        $this->labels = Group::all();
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

    public function mount($taskId = null)
    {
        $this->route = Route::currentRouteName() ?? '';
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
            $this->edit(Task::findOrFail($taskId));
        }
    }

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

            $task->parent_task_id = $task->id;
            $task->save();

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

    private function handleTaskRecurrence(Task $task)
    {
        DB::table('task_recurrence_days')->where('task_id', $task->id)->delete();
        if (in_array($this->recurrence, ['daily', 'weekly', 'monthly']) && !empty($this->selectedDays)) {
            foreach ($this->selectedDays as $day) {
                DB::table('task_recurrence_days')->insert(['task_id' => $task->id, 'day' => $day]);
            }
        }
    }

    private function UpdatehandleTaskRecurrence(Task $task)
    {
        $this->handleTaskRecurrence($task);
    }

    private function handleTaskAssignments(Task $task)
    {
        $selectedUsers = array_values(array_unique(array_filter($this->selectedUsers)));

        DB::table('task_assignments')->where('task_id', $task->id)->delete();

        foreach ($selectedUsers as $userId) {
            DB::table('task_assignments')->updateOrInsert(
                ['task_id' => $task->id, 'user_id' => $userId],
                ['assigned_at' => now()]
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
        $this->handleTaskAssignments($task);
    }

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

    private function fillReminderFields(Task $task): void
    {
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
        $this->recurrence = $task->recurrence ?? 'none';
        $this->due_date = $task->due_date
            ? Carbon::parse($task->due_date)->format('d/m/Y H:i')
            : null;
        $this->recurrence_end_date = $task->recurrence_end_date
            ? Carbon::parse($task->recurrence_end_date)->format('d/m/Y')
            : null;
        $this->status = $task->status;
        $this->isEditMode = true;

        $this->fillReminderFields($task);

        $this->selectedUsers = DB::table('task_assignments')
            ->where('task_id', $this->taskId)
            ->pluck('user_id')
            ->map(fn($id) => (int)$id)
            ->toArray();

        $this->selectedDays = DB::table('task_recurrence_days')
            ->where('task_id', $this->taskId)
            ->pluck('day')
            ->toArray();

        $this->isOpen = true;

        // Dispatch JS Event for Live Server Syncing
        $this->dispatch('task-edit-form-filled');
    }

    public function updateTask()
    {
        if (empty($this->selectedUsers) && $this->taskId) {
            $this->selectedUsers = DB::table('task_assignments')
                ->where('task_id', $this->taskId)
                ->pluck('user_id')
                ->map(fn($id) => (int)$id)
                ->toArray();
        }

        $this->validate();

        $this->due_date = filled($this->due_date)
            ? Carbon::createFromFormat('d/m/Y H:i', $this->due_date)->format('Y-m-d H:i:00')
            : null;

        if ($this->recurrence_end_date) {
            $this->recurrence_end_date = Carbon::createFromFormat('d/m/Y', $this->recurrence_end_date)->format('Y-m-d');
        } else {
            $this->recurrence_end_date = null;
        }

        DB::beginTransaction();

        try {
            $task = Task::findOrFail($this->taskId);

            $this->label_id = filled($this->label_id) ? (int) $this->label_id : null;

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
            $this->notify('Task updated successfully.', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Task Update Error: ' . $e->getMessage());
            $this->notify('Update Error: ' . $e->getMessage(), 'error');
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

    public function createInstantNotifications(Task $task)
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

    public function updateInstantNotifications(Task $task)
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
                'sent_at' => $task->due_date ?? now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

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

        Reminder::where('task_id', $task->id)->delete();

        if ($task->status === 'completed' || empty($selectedUsers)) {
            return;
        }

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

    private function createAndDispatchDueDateReminder(Task $task, int $userId, Carbon $sendAt): void
    {
        if ($sendAt->isPast() || $task->status === 'completed') {
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
            $this->dueDateChannel,
            "Task '{$task->title}' is due now."
        )->delay($sendAt);
    }

    private function createAndDispatchReminder(Task $task, int $userId, Carbon $sendAt, string $message, string $reminderUnit, int $reminderValue): void
    {
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
}
