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
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Panel;
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
use Filament\Forms\Get;
use App\Jobs\SendReminderJob;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;

class TaskTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $taskView;
    protected $layout = 'components.layouts.app';
    protected $listeners = ['taskCreated' => '$refresh', 'taskStatusUpdated' => '$refresh'];
    private $taskQuery;

    public function mount()
    {
        $this->taskView = request()->get('task_view', 'all_tasks');
    }

    protected function getDbFieldLabel(string $fieldName, string $defaultLabel): string
    {
        try {
            $dbLabel = DB::table('field_labels')
                ->where('field_name', $fieldName)
                ->value('label');

            return $dbLabel ?: $defaultLabel;
        } catch (\Throwable $e) {
            return $defaultLabel;
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => $this->getTaskQuery())
            ->columns([
                TextColumn::make('title')
                    ->label($this->getDbFieldLabel('title', 'Task Title'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-clipboard-document-list')
                    ->action(function (Task $record): void {
                        $this->dispatch('openTaskViewModal', $record->id);
                    })
                    ->extraAttributes([
                        'class' => 'cursor-pointer text-primary-600 hover:underline dark:text-primary-400',
                    ])
                    ->visibleFrom('md'),

                TextColumn::make('creator.first_name')
                    ->label($this->getDbFieldLabel('creator_first_name', 'Assigned By'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->formatStateUsing(fn($record) => $record->creator ? $record->creator->first_name . ' ' . $record->creator->last_name : 'N/A')
                    ->visibleFrom('md'),

                TextColumn::make('assignedUsers')
                    ->label($this->getDbFieldLabel('assigned_users', 'Assigned To'))
                    ->icon('heroicon-o-users')
                    ->formatStateUsing(fn($state, $record) => $record->assignedUsers->map(function ($user) {
                        return ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
                    })->join(', '))
                    ->visibleFrom('md'),

                TextColumn::make('due_date')
                    ->label($this->getDbFieldLabel('due_date', 'Due Date'))
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return 'Non';
                        }
                        return Carbon::parse($state)->format('d-m-Y H:i');
                    })
                    ->visibleFrom('md'),

                TextColumn::make('category.name')
                    ->label($this->getDbFieldLabel('category_name', 'Category'))
                    ->icon('heroicon-o-tag')
                    ->sortable()
                    ->placeholder('No Category')
                    ->visibleFrom('md'),

                TextColumn::make('recurrence')
                    ->label($this->getDbFieldLabel('recurrence', 'Repeat'))
                    ->icon('heroicon-o-arrow-path')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'none' => 'No Repeat',
                        default => ucfirst(strtolower($state)),
                    })
                    ->visibleFrom('md'),

                TextColumn::make('priority')
                    ->label($this->getDbFieldLabel('priority', 'Priority'))
                    ->badge()
                    ->icon('heroicon-o-exclamation-circle')
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'info',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => Str::ucfirst($state) . ' Priority')
                    ->visibleFrom('md'),

                TextColumn::make('group.label')
                    ->label($this->getDbFieldLabel('group_label', 'Group'))
                    ->badge()
                    ->icon('heroicon-o-folder')
                    ->color('info')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ? 'Group: ' . $state : 'No Group')
                    ->visibleFrom('md'),

                TextColumn::make('status')
                    ->label($this->getDbFieldLabel('status', 'Status'))
                    ->badge()
                    ->icon('heroicon-o-check-badge')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'primary',
                        'in_progress' => 'warning',
                        'complete_intimation' => 'info',
                        'completed' => 'success',
                        'reassignment_pending' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(function ($state, $record) {
                        $loggedInUserId = Auth::id();

                        if ($this->taskView === 'assigned_to_others') {
                            if (optional($record->creator)->id === $loggedInUserId) {
                                return $state === 'complete_intimation' ? 'Request Complete' : ($state === 'reassignment_pending' ? 'Re-assignment Approval' : Str::headline($state));
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
                                : ($displayStatus === 'reassignment_pending' ? 'Re-assignment Pending' : Str::headline($displayStatus));
                        }

                        return $state === 'complete_intimation' ? 'Request Complete' : ($state === 'reassignment_pending' ? 'Re-assignment Pending' : Str::headline($state));
                    })
                    ->visibleFrom('md'),

                Split::make([
                    TextColumn::make('title')
                        ->label($this->getDbFieldLabel('title', 'Task Title'))
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                ])->hiddenFrom('md'),

                Panel::make([
                    Stack::make([
                        TextColumn::make('creator.first_name')
                            ->label($this->getDbFieldLabel('creator_first_name', 'Assigned By'))
                            ->formatStateUsing(fn($record) => 'Assigned By: ' . ($record->creator ? $record->creator->first_name . ' ' . $record->creator->last_name : 'N/A')),

                        TextColumn::make('assignedUsers')
                            ->label($this->getDbFieldLabel('assigned_users', 'Assigned To'))
                            ->formatStateUsing(fn($state, $record) => 'Assigned To: ' . $record->assignedUsers->map(function ($user) {
                                return ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
                            })->join(', ')),

                        TextColumn::make('due_date')
                            ->label($this->getDbFieldLabel('due_date', 'Due Date'))
                            ->formatStateUsing(fn($state) => 'Due Date: ' . (blank($state) ? 'Non' : Carbon::parse($state)->format('d-m-Y H:i'))),

                        TextColumn::make('category.name')
                            ->label($this->getDbFieldLabel('category_name', 'Category'))
                            ->formatStateUsing(fn($state) => 'Category: ' . ($state ?: 'No Category')),

                        TextColumn::make('recurrence')
                            ->label($this->getDbFieldLabel('recurrence', 'Repeat'))
                            ->formatStateUsing(fn($state) => 'Repeat: ' . match ($state) {
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'none' => 'No Repeat',
                                default => ucfirst(strtolower($state)),
                            }),

                        TextColumn::make('priority')
                            ->label($this->getDbFieldLabel('priority', 'Priority'))
                            ->formatStateUsing(fn($state) => 'Priority: ' . Str::ucfirst($state)),

                        TextColumn::make('group.label')
                            ->label($this->getDbFieldLabel('group_label', 'Group'))
                            ->formatStateUsing(fn($state) => 'Group: ' . ($state ?: 'No Group')),

                        TextColumn::make('status')
                            ->label($this->getDbFieldLabel('status', 'Status'))
                            ->formatStateUsing(fn($state) => 'Status: ' . Str::headline($state)),
                    ])->space(2),
                ])
                    ->collapsible()
                    ->hiddenFrom('md'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'complete_intimation' => 'Request Complete',
                        'completed' => 'Completed',
                        'reassignment_pending' => 'Re-assignment Pending',
                    ]),

                SelectFilter::make('assigned_by')
                    ->relationship('creator', 'first_name')
                    ->searchable()
                    ->label($this->getDbFieldLabel('assigned_by', 'Assigned By'))
                    ->visible($this->taskView === 'my_tasks'),

                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label($this->getDbFieldLabel('category', 'Category')),

                SelectFilter::make('assigned_to')
                    ->relationship('assignedUsers', 'first_name')
                    ->searchable()
                    ->label($this->getDbFieldLabel('assigned_to', 'Assigned To'))
                    ->visible($this->taskView === 'assigned_to_others'),

                SelectFilter::make('recurrence')
                    ->options([
                        'none' => 'None',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                    ])
                    ->label($this->getDbFieldLabel('recurrence', 'Recurrence')),

                SelectFilter::make('priority')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ]),

                Filter::make('recurrence_end_date')
                    ->query(fn(Builder $query) => $query->where('recurrence_end_date', '>', now())->where('status', '!=', 'completed'))
                    ->label($this->getDbFieldLabel('recurrence_end_date', 'Recurrence End Date')),

                Filter::make('overdue')
                    ->query(fn(Builder $query) => $query->where('due_date', '<', now())->where('status', '!=', 'completed'))
                    ->label($this->getDbFieldLabel('overdue_tasks', 'Overdue Tasks')),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('task_verification')
                    ->label($this->getDbFieldLabel('btn_verify', 'Verify'))
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('warning')
                    ->icon('heroicon-o-shield-check')
                    ->modalHeading(fn(Task $task) => 'Task Verification & Reason - ' . $task->title)
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Save Verification')
                    ->form(function (Task $task): array {
                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        return [
                            Textarea::make('change_user_reason')
                                ->label('Reason (change_user_reason)')
                                ->placeholder('Enter reason for change or verification...')
                                ->default($existing?->change_user_reason ?? '')
                                ->required()
                                ->maxLength(1000)
                                ->rows(3),

                            Select::make('status')
                                ->label('Verification Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'verified' => 'Verified',
                                    'hold' => 'Hold',
                                    'cancel' => 'Cancel',
                                ])
                                ->default($existing?->status ?? 'pending')
                                ->required()
                                ->native(false),
                        ];
                    })
                    ->action(function (Task $task, array $data): void {
                        DB::transaction(function () use ($task, $data) {
                            DB::table('task_verifications')->updateOrInsert(
                                ['task_id' => $task->id],
                                [
                                    'change_user_reason' => trim($data['change_user_reason']),
                                    'status' => $data['status'],
                                    'created_by' => Auth::id(),
                                    'updated_at' => now(),
                                    'created_at' => now(),
                                ]
                            );

                            TaskConversation::create([
                                'task_id' => $task->id,
                                'user_id' => Auth::id(),
                                'message' => 'Verification status updated to [' . strtoupper($data['status']) . ']. Reason: ' . trim($data['change_user_reason']),
                            ]);
                        });

                        Notification::make()
                            ->title('Task Verification Updated')
                            ->body('Verification details saved successfully.')
                            ->success()
                            ->send();

                        $this->dispatch('$refresh');
                    })
                    ->visible(function (Task $task) {
                        $userId = Auth::id();
                        $isCreator = optional($task->creator)->id === $userId || (int) $task->user_id === (int) $userId;
                        $isAssigned = DB::table('task_assignments')->where('task_id', $task->id)->where('user_id', $userId)->exists();
                        return $isCreator || $isAssigned || Auth::user()?->role === 'super-admin';
                    }),

                Action::make('approve')
                    ->action(fn(Task $task) => $this->approveCompletionRequest($task))
                    ->label($this->getDbFieldLabel('btn_approve', 'Approve'))
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

                Action::make('approve_reassignment')
                    ->action(fn(Task $task, array $data) => $this->approveReassignmentRequest($task, $data))
                    ->label($this->getDbFieldLabel('btn_approve_reassignment', 'Approve Re-assignment'))
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('success')
                    ->icon('heroicon-o-user-plus')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Task Re-assignment')
                    ->modalDescription('Approve passing this task to the requested user? Conversation continuity will be maintained.')
                    ->modalSubmitActionLabel('Yes, Approve Re-assignment')
                    ->form([
                        Textarea::make('approval_comment')
                            ->label('Comment (Optional)')
                            ->placeholder('Add a note regarding re-assignment approval...')
                            ->rows(2)
                    ])
                    ->visible(function (Task $task) {
                        return $this->taskView === 'assigned_to_others'
                            && $task->creator?->id === Auth::id()
                            && $task->status === 'reassignment_pending';
                    }),

                Action::make('reject_reassignment')
                    ->action(fn(Task $task) => $this->rejectReassignmentRequest($task))
                    ->label($this->getDbFieldLabel('btn_reject_reassignment', 'Reject Re-assignment'))
                    ->button()
                    ->size(ActionSize::Small)
                    ->color('danger')
                    ->icon('heroicon-o-user-minus')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Task Re-assignment')
                    ->modalDescription('Are you sure you want to reject this re-assignment request?')
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->visible(function (Task $task) {
                        return $this->taskView === 'assigned_to_others'
                            && $task->creator?->id === Auth::id()
                            && $task->status === 'reassignment_pending';
                    }),

                Action::make('reject')
                    ->action(fn(Task $task) => $this->rejectCompletionRequest($task))
                    ->label($this->getDbFieldLabel('btn_reject', 'Reject'))
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
                    ->modalHeading(fn(Task $task) => 'Update Status & Conversation - ' . $task->title)
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Update')
                    ->modalCloseButton(true)
                    ->closeModalByClickingAway(true)
                    ->closeModalByEscaping(true)
                    ->form(function (Task $task): array {
                        return [
                            Select::make('status')
                                ->label('Select Option')
                                ->options([
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                ])
                                ->default(fn(Task $task): string => $task->status)
                                ->required()
                                ->dehydrated(true)
                                ->native(true),

                            Toggle::make('repeat')
                                ->label('Repeat')
                                ->default($task->recurrence !== 'none')
                                ->live()
                                ->dehydrated(true)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) {
                                        $set('recurrence', 'none');
                                        $set('recurrence_end_date', null);
                                    } else {
                                        $set('recurrence', 'daily');
                                    }
                                }),

                            Select::make('recurrence')
                                ->label('Frequency')
                                ->options([
                                    'none' => 'No Repeat',
                                    'daily' => 'Daily',
                                    'weekly' => 'Weekly',
                                    'monthly' => 'Monthly',
                                ])
                                ->default($task->recurrence ?: 'none')
                                ->native(false)
                                ->live()
                                ->dehydrated(true)
                                ->visible(fn(Get $get) => (bool) $get('repeat')),

                            DatePicker::make('recurrence_end_date')
                                ->label('Recurrence End Date')
                                ->default($task->recurrence_end_date ? Carbon::parse($task->recurrence_end_date) : null)
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->dehydrated(true)
                                ->visible(function (Get $get) {
                                    return (bool) $get('repeat') && $get('recurrence') !== 'none';
                                })
                                ->nullable(),

                            DateTimePicker::make('due_date')
                                ->label('Due Date & Time')
                                ->default($task->due_date ? Carbon::parse($task->due_date) : null)
                                ->native(false)
                                ->seconds(false)
                                ->time(true)
                                ->displayFormat('d/m/Y H:i')
                                ->format('Y-m-d H:i')
                                ->hoursStep(1)
                                ->minutesStep(1)
                                ->dehydrated(true)
                                ->required(),

                            Grid::make(2)
                                ->schema([
                                    TextInput::make('reminderTime')
                                        ->label('Reminder Before')
                                        ->numeric()
                                        ->integer()
                                        ->minValue(1)
                                        ->placeholder('Enter time')
                                        ->dehydrated(true)
                                        ->default($task->reminderTime)
                                        ->nullable(),

                                    Select::make('reminderUnit')
                                        ->label('Reminder Unit')
                                        ->options([
                                            'minutes' => 'Minutes',
                                            'hours' => 'Hours',
                                            'days' => 'Days',
                                        ])
                                        ->native(false)
                                        ->placeholder('Select Option')
                                        ->dehydrated(true)
                                        ->default($task->reminderUnit)
                                        ->nullable(),
                                ]),
                        ];
                    })
                    ->action(function (Task $task, array $data): void {
                        $this->updateTaskStatusFromTable($task, $data['status'], null, $data);
                    })
                    ->visible(fn(Task $task) => $this->canUpdateTaskStatusFromTable($task)),

                Action::make('task_chat')
                    ->label($this->getDbFieldLabel('btn_comment', 'Comment'))
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
                            ->limit(50)
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
                        // FIXED: Status ko forced pending karna hata diya gaya hai taaki database ka original status maintain rahe
                        $this->dispatch('openTaskDetailsModal', taskId: $task->id);
                    })
                    ->visible(
                        fn(Task $task) =>
                        in_array(Auth::user()?->role, ['admin', 'user', 'super-admin'], true)
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
                    ->visible(
                        fn(Task $task) =>
                        in_array(Auth::user()?->role, ['admin', 'user', 'super-admin'], true)
                            || Auth::id() === $task->user_id
                    ),
            ], position: ActionsPosition::AfterColumns)
            ->bulkActions([
                BulkAction::make('delete_all_tasks')
                    ->label($this->getDbFieldLabel('btn_delete_all', 'Delete All Tasks'))
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

    public function updateTaskStatusFromTable(
        Task $task,
        string $status,
        ?string $comment = null,
        array $data = []
    ): void {
        try {
            if (! $this->canUpdateTaskStatusFromTable($task)) {
                Notification::make()
                    ->title('Not allowed')
                    ->body('You are not allowed to update this task status.')
                    ->danger()
                    ->send();

                return;
            }

            $newStatus = $data['status'] ?? $status;
            $allowedStatuses = ['pending', 'in_progress', 'completed'];

            if (! in_array($newStatus, $allowedStatuses, true)) {
                Notification::make()
                    ->title('Invalid status')
                    ->danger()
                    ->send();

                return;
            }

            $recurrence = $data['recurrence'] ?? ($task->recurrence ?: 'none');

            $recurrenceEndDate = null;
            if (!empty($data['recurrence_end_date'])) {
                $recurrenceEndDate = Carbon::parse($data['recurrence_end_date'])->startOfDay();
            } else {
                $recurrenceEndDate = $task->recurrence_end_date ? Carbon::parse($task->recurrence_end_date)->startOfDay() : null;
            }

            $dueDate = null;
            if (!empty($data['due_date'])) {
                $dueDate = Carbon::parse($data['due_date']);
            } else {
                $dueDate = $task->due_date ? Carbon::parse($task->due_date) : null;
            }

            $reminderTimeVal = isset($data['reminderTime']) && ! empty($data['reminderTime']) ? (int) $data['reminderTime'] : null;
            $reminderUnitVal = $data['reminderUnit'] ?? null;

            $reminderTriggerTime = null;
            if ($reminderTimeVal !== null && $reminderUnitVal !== null && $dueDate) {
                $reminderTriggerTime = match ($reminderUnitVal) {
                    'minutes' => $dueDate->copy()->subMinutes($reminderTimeVal),
                    'hours' => $dueDate->copy()->subHours($reminderTimeVal),
                    'days' => $dueDate->copy()->subDays($reminderTimeVal),
                    default => null,
                };
            }

            DB::transaction(function () use (
                $task,
                $newStatus,
                $comment,
                $recurrence,
                $recurrenceEndDate,
                $dueDate,
                $reminderTimeVal,
                $reminderUnitVal,
                $reminderTriggerTime
            ) {
                $task->update([
                    'status' => $newStatus,
                    'recurrence' => $recurrence,
                    'recurrence_end_date' => $recurrenceEndDate ? $recurrenceEndDate->format('Y-m-d') : null,
                    'due_date' => $dueDate,
                    'reminderTime' => $reminderTimeVal,
                    'reminderUnit' => $reminderUnitVal,
                ]);

                DB::table('task_updates')->insert([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'status' => $newStatus,
                    'comment' => $comment ? trim((string) $comment) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Reminder::where('task_id', $task->id)->delete();

                if ($newStatus !== 'completed' && $dueDate && $reminderTriggerTime) {
                    $selectedUsers = DB::table('task_assignments')
                        ->where('task_id', $task->id)
                        ->pluck('user_id')
                        ->map(fn($id) => (int) $id)
                        ->unique()
                        ->values();

                    if ($selectedUsers->isEmpty()) {
                        $selectedUsers = collect([(int) Auth::id()]);
                    }

                    foreach ($selectedUsers as $userId) {
                        $reminder = Reminder::create([
                            'task_id' => $task->id,
                            'user_id' => $userId,
                            'reminder_time' => $reminderTriggerTime,
                            'reminder_unit' => $reminderUnitVal,
                            'reminder_value' => $reminderTimeVal,
                        ]);

                        // 1. Existing Email / SMS Reminder Job
                        SendReminderJob::dispatch(
                            $reminder->id,
                            ['email', 'SMS'],
                            "Reminder: Your task '{$task->title}' is due on " . $dueDate->format('d-m-Y H:i') . "."
                        )->delay($reminderTriggerTime);

                        // 2. New WhatsApp Due Reminder Job dispatch kiya gaya hai
                        \App\Jobs\SendTaskDueWhatsAppJob::dispatch(
                            $task->id,
                            $userId,
                            $dueDate->format('Y-m-d H:i:s')
                        )->delay($reminderTriggerTime);
                    }
                }

                if ($newStatus === 'completed') {
                    $this->stopTaskMailFlowIfCompleted($task);
                }
            });

            Notification::make()
                ->title('Task updated successfully')
                ->success()
                ->send();

            $this->dispatch('$refresh');
            $this->dispatch('taskStatusUpdated');
        } catch (\Throwable $e) {
            Log::error('Task status update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Error Updating Task')
                ->body('Something went wrong: ' . $e->getMessage())
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

        $userRole = Auth::user()?->role;

        if (! $isCreator && ! $isAssigned && ! in_array($userRole, ['admin', 'super-admin'], true)) {
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

    private function getDefaultNextStatus(Task $task): string
    {
        return $task->status ?? 'pending';
    }

    private function getStatusActionLabel(Task $task): string
    {
        return match ($task->status) {
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Complete',
            default       => 'Pending',
        };
    }

    private function getStatusActionColor(Task $task): string
    {
        return match ($task->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            default => 'warning',
        };
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
        $this->dispatch('taskStatusUpdated');
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
        $this->dispatch('taskStatusUpdated');
    }

    public function approveReassignmentRequest(Task $task, array $data): void
    {
        DB::transaction(function () use ($task, $data) {
            $pendingRequest = DB::table('task_reassignment_requests')
                ->where('task_id', $task->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            $newUserId = $pendingRequest ? $pendingRequest->new_user_id : null;

            if ($newUserId) {
                DB::table('task_assignments')->where('task_id', $task->id)->delete();
                DB::table('task_assignments')->insert([
                    'task_id' => $task->id,
                    'user_id' => $newUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($pendingRequest) {
                DB::table('task_reassignment_requests')
                    ->where('id', $pendingRequest->id)
                    ->update([
                        'status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id()
                    ]);
            }

            $task->update([
                'status' => 'in_progress'
            ]);

            if (!empty($data['approval_comment'])) {
                TaskConversation::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'message' => 'Re-assignment Approved: ' . trim($data['approval_comment']),
                ]);
            }
        });

        Notification::make()
            ->title('Re-assignment Approved')
            ->body('The task has been successfully reassigned with existing chat history preserved.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    public function rejectReassignmentRequest(Task $task): void
    {
        DB::transaction(function () use ($task) {
            DB::table('task_reassignment_requests')
                ->where('task_id', $task->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'reviewed_by' => Auth::id()
                ]);

            $task->update([
                'status' => 'in_progress'
            ]);
        });

        Notification::make()
            ->title('Re-assignment Rejected')
            ->body('The task re-assignment request has been rejected.')
            ->warning()
            ->send();

        $this->dispatch('$refresh');
    }

    public function handleTaskReassignment(Task $task, int $newUserId): void
    {
        $verification = DB::table('task_verifications')
            ->where('task_id', $task->id)
            ->latest('id')
            ->first();

        if (! $verification || $verification->status !== 'verified') {
            Notification::make()
                ->title('Verification Required')
                ->body('Task cannot be reassigned until its verification status is marked as "Verified".')
                ->danger()
                ->send();
            return;
        }

        $user = Auth::user();
        $loggedInRole = $user->role ?? 'user';

        if (in_array($loggedInRole, ['admin', 'super-admin'], true)) {
            DB::transaction(function () use ($task, $newUserId) {
                DB::table('task_assignments')->where('task_id', $task->id)->delete();
                DB::table('task_assignments')->insert([
                    'task_id' => $task->id,
                    'user_id' => $newUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $task->update(['status' => 'in_progress']);

                TaskConversation::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'message' => 'Task directly reassigned to a new user after verification.',
                ]);
            });

            Notification::make()
                ->title('Task Reassigned Successfully')
                ->success()
                ->send();
            return;
        }

        if ($loggedInRole === 'user' && optional($task->creator)->id !== Auth::id()) {
            DB::transaction(function () use ($task, $newUserId) {
                DB::table('task_reassignment_requests')->insert([
                    'task_id' => $task->id,
                    'requested_by' => Auth::id(),
                    'new_user_id' => $newUserId,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $task->update(['status' => 'reassignment_pending']);

                TaskConversation::create([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'message' => 'Requested task re-assignment to another user after verification. Pending creator approval.',
                ]);
            });

            Notification::make()
                ->title('Verification Request Sent')
                ->body('Task re-assignment is sent to the original Assigned By user for approval.')
                ->info()
                ->send();
        }
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

        if (Auth::user()?->role === 'super-admin') {
            return true;
        }

        $isCreator = optional($task->creator)->id === $userId || (int) $task->user_id === (int) $userId;
        $isAssigned = DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->exists();

        return $isCreator || $isAssigned;
    }

    private function getTaskQuery(): Builder
    {
        if ($this->taskQuery) {
            return $this->taskQuery;
        }

        $user = Auth::user();
        $authId = $user->id;
        $query = Task::query();

        if ($user->role === 'admin') {
            $adminCategoryId = $user->category_id ?? null;
            $adminGroupId = $user->group_id ?? null;

            $query->where(function (Builder $q) use ($authId, $adminCategoryId, $adminGroupId) {
                $q->where('user_id', $authId)
                    ->orWhereHas('taskAssignments', function (Builder $subQ) use ($authId) {
                        $subQ->where('user_id', $authId);
                    });

                if ($adminCategoryId) {
                    $q->orWhere('category_id', $adminCategoryId);
                }
                if ($adminGroupId) {
                    $q->orWhere('group_id', $adminGroupId);
                }

                if ($adminCategoryId || $adminGroupId) {
                    $q->orWhereHas('assignedUsers', function (Builder $userQ) use ($adminCategoryId, $adminGroupId) {
                        if ($adminCategoryId) {
                            $userQ->where('category_id', $adminCategoryId);
                        }
                        if ($adminGroupId) {
                            $userQ->where('group_id', $adminGroupId);
                        }
                    });
                }
            });
        } elseif ($user->role !== 'super-admin') {
            $userCategoryId = $user->category_id ?? null;
            $userGroupId = $user->group_id ?? null;

            $query->where(function (Builder $q) use ($authId, $userCategoryId, $userGroupId) {
                $q->where('user_id', $authId)
                    ->orWhereHas('taskAssignments', function (Builder $subQ) use ($authId) {
                        $subQ->where('user_id', $authId);
                    });

                if ($userCategoryId) {
                    $q->where('category_id', $userCategoryId);
                }
                if ($userGroupId) {
                    $q->where('group_id', $userGroupId);
                }
            });
        }

        if ($user->role !== 'super-admin') {
            if ($this->taskView === 'my_tasks') {
                $query->where('user_id', '!=', $authId)
                    ->whereHas('taskAssignments', function (Builder $q) use ($authId) {
                        $q->where('user_id', $authId);
                    });
            } elseif ($this->taskView === 'assigned_to_others') {
                $query->where('user_id', $authId)
                    ->whereHas('taskAssignments', function (Builder $q) use ($authId) {
                        $q->where('user_id', '!=', $authId);
                    });
            }
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

        return view('livewire.task-table', ['title' => $title]);
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
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'user', 'super-admin'], true);
    }

    private function deleteTaskWithRelatedData(Task $task, bool $useTransaction = true): void
    {
        $callback = function () use ($task) {
            DB::table('task_updates')->where('task_id', $task->id)->delete();
            TaskCompletionRequest::where('task_id', $task->id)->delete();
            DB::table('task_conversations')->where('task_id', $task->id)->delete();
            DB::table('task_verifications')->where('task_id', $task->id)->delete();
            Reminder::where('task_id', $task->id)->delete();
            DB::table('task_assignments')->where('task_id', $task->id)->delete();
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
