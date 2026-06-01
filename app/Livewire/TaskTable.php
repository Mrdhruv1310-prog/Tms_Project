<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskCompletionRequest;
use App\Models\TaskConversation;
use Carbon\Carbon;
use App\Models\Reminder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                Split::make([
                    Stack::make([
                        TextColumn::make('title')
                            ->action(function (Task $record): void {
                                $this->dispatch('openTaskViewModal', $record->id);
                            })
                            ->searchable()
                            ->label('Task Title')
                            ->tooltip(fn($record, $column) => $column->getLabel())
                            ->weight(FontWeight::Bold)
                            ->icon('heroicon-o-clipboard-document-list')
                            ->extraAttributes([
                                'class' => 'text-base font-semibold text-gray-950 dark:text-white cursor-pointer hover:text-primary-600 transition',
                            ]),

                        TextColumn::make('creator.first_name')
                            ->label('Assigned By')
                            ->formatStateUsing(fn($record) => 'Assigned By: ' . $record->creator->first_name . ' ' . $record->creator->last_name)
                            ->tooltip(fn($record, $column) => $column->getLabel())
                            ->icon('heroicon-o-user-circle')
                            ->extraAttributes([
                                'class' => 'text-sm text-gray-600 dark:text-gray-300',
                            ]),

                        Split::make([
                            TextColumn::make('due_date')
                                ->sortable()
                                ->searchable()
                                ->label('Due Date')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-calendar-days')
                                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:i'))
                                ->color(fn(string $state): string => Carbon::parse($state)->isPast() ? 'danger' : 'info')
                                ->extraAttributes(['class' => 'whitespace-nowrap rounded-xl bg-gray-50 px-3 py-2 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700']),

                            TextColumn::make('assignedUsers')
                                ->label('Assigned To')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-users')
                                ->formatStateUsing(fn($state, $record) => $record->assignedUsers->map(function ($user) {
                                    return ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
                                })->join(', '))
                                ->extraAttributes(['class' => 'whitespace-nowrap rounded-xl bg-gray-50 px-3 py-2 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700']),

                            TextColumn::make('category.name')
                                ->label('Category')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-tag')
                                ->extraAttributes(['class' => 'whitespace-nowrap rounded-xl bg-gray-50 px-3 py-2 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700']),

                            TextColumn::make('recurrence')
                                ->label('Repeat')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-arrow-path')
                                ->formatStateUsing(fn($state) => match ($state) {
                                    'daily' => 'Daily',
                                    'weekly' => 'Weekly',
                                    'monthly' => 'Monthly',
                                    'none' => 'No Repeat',
                                    default => ucfirst(strtolower($state)),
                                })
                                ->extraAttributes(['class' => 'whitespace-nowrap rounded-xl bg-gray-50 px-3 py-2 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700']),

                            TextColumn::make('priority')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'low' => 'success',
                                    'medium' => 'info',
                                    'high' => 'danger',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn($state) => Str::ucfirst($state) . ' Priority')
                                ->label('Priority')
                                ->tooltip(fn($record, $column) => $column->getLabel()),

                            TextColumn::make('group.label')
                                ->badge()
                                ->color('info')
                                ->label('Group')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->sortable()
                                ->searchable()
                                ->formatStateUsing(fn($state) => $state ? 'Group: ' . $state : 'No Group')
                                ->extraAttributes(['class' => 'whitespace-nowrap']),

                            TextColumn::make('status')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'pending' => 'primary',
                                    'in_progress' => 'warning',
                                    'complete_intimation' => 'info',
                                    'completed' => 'success',
                                    default => 'gray',
                                })
                                ->label('Status')
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
                                })
                                ->tooltip(fn($record, $column) => $column->getLabel()),
                        ])->from('md'),
                    ])->space(2),
                ])->from('md'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
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

                Action::make('in_progress')
                    ->action(function (Task $task) {
                        $this->updateStatus($task, 'in_progress');
                    })
                    ->label('In Progress')
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('warning')
                    ->icon('heroicon-o-forward')
                    ->visible(function (Task $task) {
                        $userId = Auth::id();

                        $isAssigned = DB::table('task_assignments')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->exists();

                        $userStatus = DB::table('task_updates')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->latest('updated_at')
                            ->value('status');

                        return $this->taskView === 'my_tasks'
                            && $isAssigned
                            && ($userStatus === 'pending' || is_null($userStatus));
                    }),

                Action::make('complete')
                    ->action(function (Task $task) {
                        $this->updateStatus($task, 'completed');
                    })
                    ->label('Complete')
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(function (Task $task) {
                        $userId = Auth::id();

                        $userStatus = DB::table('task_updates')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->orderBy('updated_at', 'desc')
                            ->limit(1)
                            ->value('status');

                        return $this->taskView === 'my_tasks'
                            && $userStatus === 'in_progress'
                            && $userId === optional($task->creator)->id
                            && $task->taskAssignments->contains('user_id', $userId);
                    }),

                Action::make('completeintimation')
                    ->action(fn(Task $task) => $this->requestCompletion($task))
                    ->label('Request Complete')
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('info')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(function (Task $task) {
                        $userId = Auth::id();

                        $isAssigned = DB::table('task_assignments')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->exists();

                        $userStatus = DB::table('task_updates')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->orderBy('updated_at', 'desc')
                            ->limit(1)
                            ->value('status');

                        $hasPendingRequest = TaskCompletionRequest::where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();

                        return $this->taskView === 'my_tasks'
                            && $isAssigned
                            && $userStatus === 'in_progress'
                            && $userId !== optional($task->creator)->id
                            && ! $hasPendingRequest;
                    }),

                Action::make('task_chat')
                    ->label('Chat')
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
                        Auth::user()->role === 'admin'
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
                    ->visible(fn(Task $task) => Auth::user()->role === 'admin' || Auth::user()->id === $task->user_id),
            ], position: ActionsPosition::AfterColumns)
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
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

    public function requestCompletion(Task $task): void
    {
        $userId = Auth::id();

        DB::transaction(function () use ($task, $userId) {
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

            DB::table('task_updates')->insert([
                'task_id' => $task->id,
                'user_id' => $userId,
                'status' => 'complete_intimation',
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
        DB::transaction(function () use ($task) {

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
        });
    }

    // Stop Task Mail flow if task is completed and has a pending reminder
    private function stopTaskMailFlowIfCompleted(Task $task): void
    {
        $hasCompletedUpdate = DB::table('task_updates')
            ->where('task_id', $task->id)
            ->where('status', 'completed')
            ->exists();

        if (! $hasCompletedUpdate) {
            return;
        }

        Reminder::where('task_id', $task->id)->delete();
    }
}
