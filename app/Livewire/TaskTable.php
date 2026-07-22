<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskCompletionRequest;
use App\Models\TaskConversation;
use Carbon\Carbon;
use App\Models\Reminder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class TaskTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $taskView;
    protected $listeners = ['taskCreated' => '$refresh', 'taskStatusUpdated' => '$refresh'];
    private $taskQuery;

    public function mount()
    {
        $this->taskView = request()->get('task_view', 'all_tasks');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getTaskQuery())
            ->columns([
                TextColumn::make('title')
                    ->label('Task Title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-clipboard-document-list')
                    ->action(function (Task $record): void {
                        $this->dispatch('openTaskViewModal', $record->id);
                    })
                    ->extraAttributes([
                        'class' => 'cursor-pointer text-primary-600 hover:underline dark:text-primary-400',
                    ]),

                TextColumn::make('creator.first_name')
                    ->label('Assigned By')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->formatStateUsing(fn($record) => $record->creator ? $record->creator->first_name . ' ' . $record->creator->last_name : 'N/A'),

                TextColumn::make('assignedUsers')
                    ->label('Assigned To')
                    ->icon('heroicon-o-users')
                    ->formatStateUsing(fn($state, $record) => $record->assignedUsers->map(function ($user) {
                        return ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
                    })->join(', ')),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return 'Non';
                        }
                        return Carbon::parse($state)->format('d-m-Y H:i');
                    }),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->icon('heroicon-o-tag')
                    ->sortable()
                    ->placeholder('No Category'),

                TextColumn::make('recurrence')
                    ->label('Repeat')
                    ->icon('heroicon-o-arrow-path')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'none' => 'No Repeat',
                        default => ucfirst(strtolower($state)),
                    }),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->icon('heroicon-o-exclamation-circle')
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'info',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => Str::ucfirst($state) . ' Priority'),

                TextColumn::make('group.label')
                    ->label('Group')
                    ->badge()
                    ->icon('heroicon-o-folder')
                    ->color('info')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ? 'Group: ' . $state : 'No Group'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon('heroicon-o-check-badge')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'primary',
                        'in_progress' => 'warning',
                        'complete_intimation' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(function ($state, $record) {
                        $loggedInUserId = Auth::id();

                        if ($this->taskView === 'assigned_to_others') {
                            if (optional($record->creator)->id === $loggedInUserId) {
                                return $state === 'complete_intimation' ? 'Request Complete' : Str::headline($state);
                            }
                        } elseif ($this->taskView === 'my_tasks') {
                            $userStatus = DB::table('task_updates')
                                ->where('task_id', $record->id)
                                ->where('user_id', $loggedInUserId)
                                ->orderByDesc('updated_at')
                                ->value('status');

                            $displayStatus = $userStatus ?: $state;

                            return $displayStatus === 'complete_intimation'
                                ? 'Request Complete'
                                : Str::headline($displayStatus);
                        }

                        return $state === 'complete_intimation' ? 'Request Complete' : Str::headline($state);
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'complete_intimation' => 'Request Complete',
                        'completed' => 'Completed',
                    ]),

                SelectFilter::make('assigned_by')
                    ->relationship('creator', 'first_name')
                    ->searchable()
                    ->label('Assigned By')
                    ->visible($this->taskView === 'my_tasks'),

                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Category'),

                SelectFilter::make('assigned_to')
                    ->relationship('assignedUsers', 'first_name')
                    ->searchable()
                    ->label('Assigned To')
                    ->visible($this->taskView === 'assigned_to_others'),

                SelectFilter::make('recurrence')
                    ->options([
                        'none' => 'None',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ])
                    ->label('Recurrence'),

                SelectFilter::make('priority')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ]),

                Filter::make('recurrence_end_date')
                    ->query(fn(Builder $query) => $query->where('recurrence_end_date', '>', now())->where('status', '!=', 'completed'))
                    ->label('Recurrence End Date'),

                Filter::make('overdue')
                    ->query(fn(Builder $query) => $query->where('due_date', '<', now())->where('status', '!=', 'completed'))
                    ->label('Overdue Tasks'),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('approve')
                    ->action(fn(Task $task) => $this->approveCompletionRequest($task))
                    ->label('Approve')
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve completion request')
                    ->modalDescription('Are you sure you want to approve this task completion request?')
                    ->modalSubmitActionLabel('Yes, approve')
                    ->visible(function (Task $task) {
                        return $this->taskView === 'assigned_to_others'
                            && $task->creator?->id === Auth::id()
                            && TaskCompletionRequest::where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();
                    }),

                Action::make('reject')
                    ->action(fn(Task $task) => $this->rejectCompletionRequest($task))
                    ->label('Reject')
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Reject completion request')
                    ->modalDescription('Are you sure you want to reject this completion request? Task will go back to In Progress.')
                    ->modalSubmitActionLabel('Yes, reject')
                    ->visible(function (Task $task) {
                        return $this->taskView === 'assigned_to_others'
                            && $task->creator?->id === Auth::id()
                            && TaskCompletionRequest::where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();
                    }),

                Action::make('status_update')
                    ->label(fn(Task $task) => $this->getStatusActionLabel($task))
                    ->button()
                    ->size(ActionSize::Small)
                    ->color(fn(Task $task) => $this->getStatusActionColor($task))
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->modalHeading(fn(Task $task) => 'Update Status - ' . $task->title)
                    ->modalSubmitActionLabel('Update Status')
                    ->form(function (Task $task): array {
                        return [
                            Textarea::make('comment')
                                ->label('Comment')
                                ->placeholder('Enter task update comment...')
                                ->required()
                                ->maxLength(1000)
                                ->rows(3),

                            Select::make('status')
                                ->label('Select Option')
                                ->options($this->getStatusOptions($task))
                                ->default($this->getDefaultNextStatus($task))
                                ->required()
                                ->native(false),
                        ];
                    })
                    ->action(function (Task $task, array $data): void {
                        $this->updateTaskStatusFromTable(
                            $task,
                            $data['status'],
                            $data['comment'] ?? null,
                        );
                    })
                    ->visible(fn(Task $task) => $this->canUpdateTaskStatusFromTable($task)),

                Action::make('task_chat')
                    ->label('Comment')
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('gray')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading(fn(Task $task) => 'Task Chat - ' . $task->title)
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Send')
                    ->form([
                        Textarea::make('message')
                            ->label('Message')
                            ->placeholder('Type short task related message...')
                            ->required()
                            ->maxLength(1000)
                            ->rows(3),
                    ])
                    ->modalContent(function (Task $task) {
                        $messages = TaskConversation::with('user')
                            ->where('task_id', $task->id)
                            ->latest()
                            ->limit(20)
                            ->get()
                            ->reverse();

                        return view('livewire.task-chat-messages', [
                            'messages' => $messages,
                        ]);
                    })
                    ->action(function (Task $task, array $data) {
                        $this->sendTaskMessage($task, $data['message']);
                    })
                    ->visible(fn(Task $task) => $this->canChatOnTask($task)),

                Action::make('edit')
                    ->badge()
                    ->badgeColor('info')
                    ->size(ActionSize::Large)
                    ->label('')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function (Task $task): void {
                        DB::transaction(function () use ($task) {
                            $task->update([
                                'status' => 'pending',
                            ]);

                            DB::table('task_updates')
                                ->where('task_id', $task->id)
                                ->delete();

                            TaskCompletionRequest::where('task_id', $task->id)
                                ->where('request_status', 'pending')
                                ->update([
                                    'request_status' => 'rejected',
                                ]);

                            foreach ($task->taskAssignments as $assignment) {
                                DB::table('task_updates')->insert([
                                    'task_id' => $task->id,
                                    'user_id' => $assignment->user_id,
                                    'status' => 'pending',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        });

                        $this->dispatch('openTaskDetailsModal', taskId: $task->id);
                    })
                    ->visible(
                        fn(Task $task) =>
                        Auth::user()->role === 'admin' || Auth::user()->role === 'user'
                            || Auth::id() === $task->user_id
                    ),

                Action::make('delete')
                    ->action(fn(Task $task) => $this->delete($task))
                    ->badge()
                    ->size(ActionSize::Large)
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->label('')
                    ->modalIcon('heroicon-o-trash')
                    ->modalIconColor('danger')
                    ->modalHeading('Delete task')
                    ->modalDescription('Are you sure you\'d like to delete this task? This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it')
                    ->modalAlignment(Alignment::Center)
                    ->visible(fn(Task $task) => Auth::user()->role === 'admin' ||
                        Auth::user()->role === 'user' || Auth::user()->id === $task->user_id),
            ], position: ActionsPosition::AfterColumns)
            ->bulkActions([
                BulkAction::make('delete_all_tasks')
                    ->label('Delete All Tasks')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-trash')
                    ->modalIconColor('danger')
                    ->modalHeading('Delete selected tasks')
                    ->modalDescription('Are you sure you want to delete the selected tasks? This will also delete related task updates, completion requests, chats, reminders, and assignments. This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete')
                    ->modalAlignment(Alignment::Center)
                    ->action(fn(Collection $records) => $this->deleteSelectedTasks($records))
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('id', 'desc')
            ->striped();
    }

    public function updateStatus(Task $task, $status)
    {
        $this->dispatch('status-updated', [
            'task' => $task->toArray(),
            'status' => $status,
        ])->to(TaskUpdateModal::class);
    }

    public function updateTaskStatusFromTable(Task $task, string $status, ?string $comment = null): void
    {
        try {
            if (! $this->canUpdateTaskStatusFromTable($task)) {
                Notification::make()
                    ->title('Not allowed')
                    ->body('You are not allowed to update this task status.')
                    ->danger()
                    ->send();

                return;
            }

            if (! in_array($status, ['in_progress', 'completed'], true)) {
                Notification::make()
                    ->title('Invalid status')
                    ->danger()
                    ->send();

                return;
            }

            $userId = Auth::id();
            $comment = trim((string) $comment);

            // Checking creator safely
            $creatorId = $task->creator ? $task->creator->id : $task->user_id;

            if ($status === 'completed' && (int)$userId !== (int)$creatorId) {
                $this->requestCompletion($task, $comment);
                return;
            }

            DB::transaction(function () use ($task, $status, $comment, $userId) {
                if ($status === 'in_progress') {
                    TaskCompletionRequest::where('task_id', $task->id)
                        ->where('user_id', $userId)
                        ->where('request_status', 'pending')
                        ->update([
                            'request_status' => 'rejected',
                            'reviewed_at' => now(),
                        ]);
                }

                $task->update([
                    'status' => $status,
                ]);

                DB::table('task_updates')->insert([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'status' => $status,
                    'comment' => $comment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($status === 'completed') {
                    $this->stopTaskMailFlowIfCompleted($task);
                }
            });

            Notification::make()
                ->title('Task status updated')
                ->body('Task status has been updated successfully.')
                ->success()
                ->send();

            $this->dispatch('$refresh');
            $this->dispatch('taskStatusUpdated');

        } catch (\Throwable $e) {
            Log::error('Task status update error: ' . $e->getMessage());

            Notification::make()
                ->title('Error Updating Status')
                ->body('Something went wrong while updating status: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function canUpdateTaskStatusFromTable(Task $task): bool
    {
        $userId = Auth::id();

        if (! $userId) {
            return false;
        }

        $isCreator = optional($task->creator)->id === $userId || (int) $task->user_id === (int) $userId;

        $isAssigned = DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->exists();

        if (! $isCreator && ! $isAssigned && Auth::user()?->role !== 'admin') {
            return false;
        }

        return true;
    }

    private function getCurrentTaskStatusForUser(Task $task): string
    {
        if ($this->taskView === 'my_tasks') {
            $userStatus = DB::table('task_updates')
                ->where('task_id', $task->id)
                ->where('user_id', Auth::id())
                ->orderByDesc('updated_at')
                ->value('status');

            return $userStatus ?: $task->status;
        }

        return $task->status;
    }

    private function getStatusOptions(Task $task): array
    {
        return [
            'in_progress' => 'In Progress',
            'completed' => 'Complete',
        ];
    }

    private function getDefaultNextStatus(Task $task): string
    {
        return 'in_progress';
    }

    private function getStatusActionLabel(Task $task): string
    {
        return 'In Progress';
    }

    private function getStatusActionColor(Task $task): string
    {
        return 'info';
    }

    public function resetTaskWorkflowAfterEdit(Task $task): void
    {
        DB::transaction(function () use ($task) {

            $task->update([
                'status' => 'pending',
            ]);

            DB::table('task_updates')
                ->where('task_id', $task->id)
                ->delete();

            TaskCompletionRequest::where('task_id', $task->id)
                ->where('request_status', 'pending')
                ->update([
                    'request_status' => 'rejected',
                ]);

            foreach ($task->taskAssignments as $assignment) {
                DB::table('task_updates')->insert([
                    'task_id' => $task->id,
                    'user_id' => $assignment->user_id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function requestCompletion(Task $task, ?string $comment = null): void
    {
        $userId = Auth::id();
        $comment = trim((string) $comment);

        DB::transaction(function () use ($task, $userId, $comment) {
            $alreadyPending = TaskCompletionRequest::where('task_id', $task->id)
                ->where('user_id', $userId)
                ->where('request_status', 'pending')
                ->exists();

            if ($alreadyPending) {
                return;
            }

            TaskCompletionRequest::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'request_status' => 'pending',
                'requested_at' => now(),
            ]);

            $task->update([
                'status' => 'complete_intimation',
            ]);

            DB::table('task_updates')->insert([
                'task_id' => $task->id,
                'user_id' => $userId,
                'status' => 'complete_intimation',
                'comment' => $comment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Notification::make()
            ->title('Completion request sent')
            ->body('Your completion request has been sent for approval.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    public function approveCompletionRequest(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $pendingRequest = TaskCompletionRequest::where('task_id', $task->id)
                ->where('request_status', 'pending')
                ->orderByDesc('requested_at')
                ->first();

            if (! $pendingRequest) {
                return;
            }

            $pendingRequest->update([
                'request_status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);

            $task->update([
                'status' => 'completed',
            ]);

            DB::table('task_updates')->insert([
                'task_id' => $task->id,
                'user_id' => $pendingRequest->user_id,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->stopTaskMailFlowIfCompleted($task);
        });

        Notification::make()
            ->title('Task Approved')
            ->body('The completion request has been approved and the task is now completed.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    public function rejectCompletionRequest(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $pendingRequest = TaskCompletionRequest::where('task_id', $task->id)
                ->where('request_status', 'pending')
                ->orderByDesc('requested_at')
                ->first();

            if (! $pendingRequest) {
                return;
            }

            $pendingRequest->update([
                'request_status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
            ]);

            $task->update([
                'status' => 'in_progress',
            ]);

            DB::table('task_updates')->insert([
                'task_id' => $task->id,
                'user_id' => $pendingRequest->user_id,
                'status' => 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Notification::make()
            ->title('Task Rejected')
            ->body('The completion request has been rejected and the task moved back to In Progress.')
            ->warning()
            ->send();

        $this->dispatch('$refresh');
    }

    public function sendTaskMessage(Task $task, string $message): void
    {
        if (! $this->canChatOnTask($task)) {
            Notification::make()
                ->title('Not allowed')
                ->body('You can only chat on tasks assigned by you or assigned to you.')
                ->danger()
                ->send();

            return;
        }

        TaskConversation::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'message' => trim($message),
        ]);

        Notification::make()
            ->title('Message sent')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    private function canChatOnTask(Task $task): bool
    {
        $userId = Auth::id();

        return $task->user_id === $userId
            || DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->exists();
    }

    private function getTaskQuery(): Builder
    {
        if ($this->taskQuery) {
            return $this->taskQuery;
        }

        $user = Auth::user();
        $query = Task::query();

        if ($this->taskView === 'my_tasks') {
            $Auth_id = $user->id;

            $query->whereHas('taskAssignments', function (Builder $query) use ($Auth_id) {
                $query->where('user_id', $Auth_id);
            });
        } elseif ($this->taskView === 'assigned_to_others') {
            $Auth_id = $user->id;

            $query->where('user_id', $Auth_id)->whereHas('assignedUsers', function (Builder $query) use ($Auth_id) {
                $query->where('user_id', '!=', $Auth_id);
            });
        } elseif ($this->taskView === 'tasks' && $user->role !== 'admin') {
            $Auth_id = $user->id;

            $query->where('user_id', $Auth_id)
                ->orWhereHas('taskAssignments', function (Builder $query) use ($Auth_id) {
                    $query->where('user_id', $Auth_id);
                });
        }

        $this->taskQuery = $query;

        return $query;
    }

    public function render()
    {
        $title = match ($this->taskView) {
            'my_tasks' => 'My Tasks',
            'assigned_to_others' => 'Tasks Assigned to Others',
            default => 'All Tasks',
        };

        return view('livewire.task-table')->layout('components.layouts.app', ['title' => $title]);
    }

    public function delete(Task $task): void
    {
        if (! $this->canDeleteTask($task)) {
            Notification::make()
                ->title('Not allowed')
                ->body('You are not allowed to delete this task.')
                ->danger()
                ->send();

            return;
        }

        $this->deleteTaskWithRelatedData($task);

        Notification::make()
            ->title('Task deleted')
            ->success()
            ->send();
    }

    public function deleteSelectedTasks(Collection $records): void
    {
        $deletedCount = 0;

        DB::transaction(function () use ($records, &$deletedCount) {
            foreach ($records as $task) {
                if (! $this->canDeleteTask($task)) {
                    continue;
                }

                $this->deleteTaskWithRelatedData($task, false);
                $deletedCount++;
            }
        });

        if ($deletedCount === 0) {
            Notification::make()
                ->title('No task deleted')
                ->body('You are not allowed to delete the selected tasks.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Tasks deleted')
            ->body($deletedCount . ' selected task(s) deleted successfully.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    private function canDeleteTask(Task $task): bool
    {
        return Auth::check()
            && in_array(Auth::user()->role, ['admin', 'user'], true);
    }

    private function deleteTaskWithRelatedData(Task $task, bool $useTransaction = true): void
    {
        $callback = function () use ($task) {
            DB::table('task_updates')
                ->where('task_id', $task->id)
                ->delete();

            TaskCompletionRequest::where('task_id', $task->id)
                ->delete();

            DB::table('task_conversations')
                ->where('task_id', $task->id)
                ->delete();

            Reminder::where('task_id', $task->id)
                ->delete();

            DB::table('task_assignments')
                ->where('task_id', $task->id)
                ->delete();

            $task->delete();
        };

        if ($useTransaction) {
            DB::transaction($callback);
            return;
        }

        $callback();
    }

    private function stopTaskMailFlowIfCompleted(Task $task): void
    {
        try {
            $hasCompletedUpdate = DB::table('task_updates')
                ->where('task_id', $task->id)
                ->where('status', 'completed')
                ->exists();

            if (! $hasCompletedUpdate) {
                return;
            }

            Reminder::where('task_id', $task->id)->delete();
        } catch (\Throwable $e) {
            Log::warning('Could not clear reminders for completed task: ' . $e->getMessage());
        }
    }
}
