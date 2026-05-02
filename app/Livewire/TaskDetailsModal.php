<?php

namespace App\Livewire;

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
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'label_id' => 'nullable|exists:groups,id',
            'recurrence' => 'required|in:none,daily,weekly,monthly',
            'due_date' => 'required|date_format:d/m/Y H:i|after:now',
            'recurrence_end_date' => 'nullable|date_format:d/m/Y|after:today',
            'status' => 'required|in:pending,in_progress,completed',
            'selectedUsers' => 'required|array|min:1',
        ];
    }
    protected $listeners = ['openTaskModal' => 'open', 'closeTaskModal' => 'close', 'openTaskDetailsModal' => 'edit'];

    // For custom validation messages to ensure users understand the required formats and constraints
    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'label_id.required' => 'The label field is required.',
            'label_id.exists' => 'The selected label does not exist.',
            'priority.required' => 'The priority field is required.',
            'priority.in' => 'The priority must be one of the following: low, medium, high.',
            'recurrence.required' => 'The recurrence field is required.',
            'recurrence.in' => 'The recurrence must be one of the following: none, daily, weekly, monthly.',
            'due_date.required' => 'The due date field is required.',
            'due_date.date_format' => 'The due date must be in the format: day/month/year hour:minute am/pm. For example, 31/12/2024 10:30 pm.',
            'recurrence_end_date.date_format' => 'The recurrence end date must be in the format: day/month/year.',
            'recurrence_end_date.after' => 'The recurrence end date must be after the current date.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of the following: pending, in_progress, completed.',
            'selectedUsers.required' => 'You must select at least one user.',
            'selectedUsers.min' => 'You must select at least one user.',
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
    public function saveTask()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $dueDateFormatted = Carbon::createFromFormat('d/m/Y H:i', $this->due_date)
                ->format('Y-m-d H:i:00');

            $recurrenceEnd = $this->recurrence_end_date
                ? Carbon::createFromFormat('d/m/Y', $this->recurrence_end_date)->format('Y-m-d')
                : null;

            $oldTask = Task::find($this->taskId);
            $oldDueDate = $oldTask?->due_date;

            $task = Task::updateOrCreate(
                ['id' => $this->taskId],
                [
                    'title' => $this->title,
                    'description' => $this->description,
                    'category_id' => $this->category_id,
                    'priority' => $this->priority,
                    'label_id' => $this->label_id ?: null,
                    'recurrence' => $this->recurrence,
                    'due_date' => $dueDateFormatted,
                    'recurrence_end_date' => $recurrenceEnd,
                    'status' => $this->status,
                    'user_id' => Auth::id(),
                ]
            );

            $this->handleTaskRecurrence($task);
            $this->handleTaskAssignments($task);
            $this->handleNotificationsAndReminders($task);

            DB::commit();

            if ($oldDueDate !== $dueDateFormatted) {
                SendDueDateNotificationJob::dispatch($task, $this->dueDateChannel, $dueDateFormatted)
                    ->delay(Carbon::parse($dueDateFormatted));
            }

            $this->dispatch('taskCreated');
            $this->close();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving task: ' . $e->getMessage());
            $this->notify('Something went wrong.', 'error');
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

    // Handle task notifications and reminders
    private function handleNotificationsAndReminders($task)
    {
        $this->createInstantNotifications($task);
        if (!empty($this->reminderTime) && !empty($this->reminderUnit)) {
            $this->selectedUsers = array_unique($this->selectedUsers);
            $this->createTaskReminders($task, $this->reminderTime, $this->reminderUnit, $this->selectedUsers);
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

    // Send Reminder Email Notification
    public function createTaskReminders($task, $reminderTime, $reminderUnit, $selectedUsers)
    {
        $dueDate = Carbon::parse($task->due_date);

        $reminderValue = match ($reminderUnit) {
            'minutes' => $dueDate->copy()->subMinutes($reminderTime),
            'hours' => $dueDate->copy()->subHours($reminderTime),
            'days' => $dueDate->copy()->subDays($reminderTime),
            default => throw new \Exception('Invalid reminder unit'),
        };


        if ($reminderValue->isPast()) {
            throw new \Exception('Reminder time cannot be in the past.');
        }

        foreach ($selectedUsers as $userId) {
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

            SendReminderJob::dispatch($reminder, $this->reminderChannel, $reminderValue)
                ->delay($reminderValue);
        }
    }
    // public function createTaskReminders($task, $selectedUsers)
    // {
    //     $dueDate = Carbon::parse($task->due_date);

    //     $reminders = [
    //         ['hours' => 24, 'minutes' => 0, 'label' => 'Only 24 hours left to complete your task'],
    //         ['hours' => 6,  'minutes' => 0, 'label' => 'Only 6 hours left to complete your task'],
    //         ['hours' => 0,  'minutes' => 30, 'label' => 'Only 30 minutes left to complete your task'],
    //         ['hours' => 0,  'minutes' => 0,  'label' => 'Task is due now'],
    //     ];

    //     foreach ($reminders as $reminder) {

    //         $reminderTime = $dueDate->copy()
    //             ->subHours($reminder['hours'])
    //             ->subMinutes($reminder['minutes']);

    //         if ($reminderTime->isPast()) {
    //             continue;
    //         }

    //         foreach ($selectedUsers as $userId) {

    //             Reminder::where('task_id', $task->id)
    //                 ->where('user_id', $userId)
    //                 ->where('reminder_time', $reminderTime)
    //                 ->delete();

    //             $model = Reminder::create([
    //                 'task_id'        => $task->id,
    //                 'user_id'        => $userId,
    //                 'reminder_time'  => $reminderTime,
    //                 'reminder_unit'  => $reminder['hours'] ? 'hours' : 'minutes',
    //                 'reminder_value' => $reminder['hours'] ?: $reminder['minutes'],
    //             ]);

    //             SendReminderJob::dispatch(
    //                 $model,
    //                 $this->reminderChannel,
    //                 $reminderTime
    //             )->delay($reminderTime);
    //         }
    //     }
    // }
}
