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
use Filament\Forms\Get;
use App\Jobs\SendReminderJob;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class TaskTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public string $taskView;
    protected string $layout = 'components.layouts.app';

    /** @var array<string, string> */
    protected $listeners = ['taskCreated' => '$refresh', 'taskStatusUpdated' => '$refresh'];

    public ?string $statusFilter = null;
    public ?string $categoryFilter = null;
    public ?int $userFilter = null;

    // Date Filters Properties
    public ?string $filterDate = null;
    public ?string $filterTimeRange = null;
    public ?string $filterYear = null;
    public ?string $filterMonth = null;
    public ?string $groupFilter = null;

    public function mount(): void
    {
        $this->taskView = request()->get('task_view', 'all_tasks');

        $this->filterDate = request()->get('date', session('dashboard_date'));
        $this->filterTimeRange = request()->get('time_range', session('dashboard_time_range', 'all'));
        $this->filterYear = request()->get('year', session('dashboard_year'));
        $this->filterMonth = request()->get('month', session('dashboard_month'));

        if (request()->is('tasks/user*')) {
            if (request()->has('user_id')) {
                $this->userFilter = (int) request()->get('user_id');
            } else {
                $this->userFilter = (int) Auth::id();
            }

            $statusSegment = request()->segment(3);
            if ($statusSegment && in_array($statusSegment, ['pending', 'in_progress', 'completed', 'canceled'], true)) {
                $this->statusFilter = $statusSegment;
            }
        } elseif (request()->is('tasks/category*')) {
            $categorySegment = request()->segment(3);
            $statusSegment = request()->segment(4);

            if ($categorySegment) {
                $this->categoryFilter = urldecode((string) $categorySegment);
            }
            if ($statusSegment && in_array($statusSegment, ['pending', 'in_progress', 'completed', 'canceled'], true)) {
                $this->statusFilter = $statusSegment;
            }
        } else {
            $statusSegment = request()->segment(2);
            if ($statusSegment && in_array($statusSegment, ['pending', 'in_progress', 'completed', 'canceled'], true)) {
                $this->statusFilter = $statusSegment;
            }
        }

        if (! $this->statusFilter && request()->has('status')) {
            $reqStatus = (string) request()->get('status');
            if (in_array($reqStatus, ['pending', 'in_progress', 'completed', 'canceled'], true)) {
                $this->statusFilter = $reqStatus;
            }
        }

        if (! $this->categoryFilter && request()->has('category')) {
            $this->categoryFilter = urldecode((string) request()->get('category'));
        }

        if (! $this->groupFilter && request()->has('group')) {
            $this->groupFilter = urldecode((string) request()->get('group'));
        }
    }

    private function applyDateFiltersToTaskQuery(Builder $query): void
    {
        if (filled($this->filterDate)) {
            $query->whereDate('created_at', Carbon::parse($this->filterDate)->format('Y-m-d'));
            return;
        }

        if ($this->filterTimeRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
            return;
        } elseif ($this->filterTimeRange === 'this_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            return;
        } elseif ($this->filterTimeRange === 'this_month') {
            $query->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month);
            return;
        } elseif ($this->filterTimeRange === 'this_year') {
            $query->whereYear('created_at', Carbon::now()->year);
            return;
        }

        if (filled($this->filterYear)) {
            $query->whereYear('created_at', $this->filterYear);
        }

        if (filled($this->filterMonth)) {
            $query->whereMonth('created_at', $this->filterMonth);
        }
    }

    private function getTaskQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $authId = (int) $user->id;
        $userRole = strtolower($user->role ?? 'employee');

        $query = Task::query()->with(['creator', 'category', 'assignedUsers'])->whereHas('creator');

        $this->applyDateFiltersToTaskQuery($query);

        $userGroupIds = [];
        $userGroupLabels = [];

        try {
            if (DB::getSchemaBuilder()->hasTable('group_user')) {
                $userGroupIds = DB::table('group_user')
                    ->where('user_id', $authId)
                    ->pluck('group_id')
                    ->filter()
                    ->toArray();
            } elseif (DB::getSchemaBuilder()->hasTable('groups_users')) {
                $userGroupIds = DB::table('groups_users')
                    ->where('user_id', $authId)
                    ->pluck('group_id')
                    ->filter()
                    ->toArray();
            } elseif (DB::getSchemaBuilder()->hasTable('user_groups')) {
                $userGroupIds = DB::table('user_groups')
                    ->where('user_id', $authId)
                    ->pluck('group_id')
                    ->filter()
                    ->toArray();
            }

            if (! empty($userGroupIds)) {
                $userGroupLabels = DB::table('groups')
                    ->whereIn('id', $userGroupIds)
                    ->pluck('label')
                    ->filter()
                    ->toArray();
            }
        } catch (\Throwable $e) {
            Log::warning('Group user pivot table check warning: ' . $e->getMessage());
        }

        if ($this->taskView === 'my_tasks') {
            $query->where(function (Builder $q) use ($authId, $userGroupIds, $userGroupLabels) {
                $q->whereHas('taskAssignments', function (Builder $aq) use ($authId) {
                    $aq->where('user_id', $authId);
                });

                if (! empty($userGroupIds) || ! empty($userGroupLabels)) {
                    $q->orWhereIn('tasks.label_id', array_merge($userGroupIds, $userGroupLabels));
                }
            });
        } elseif ($this->taskView === 'assigned_to_others') {
            $query->where('tasks.user_id', $authId);
        } else {
            if (! in_array($userRole, ['super-admin', 'admin', 'manager'], true)) {
                $query->where(function (Builder $q) use ($authId, $userGroupIds, $userGroupLabels) {
                    $q->where('tasks.user_id', $authId)
                        ->orWhereHas('taskAssignments', function (Builder $aq) use ($authId) {
                            $aq->where('user_id', $authId);
                        });

                    if (! empty($userGroupIds) || ! empty($userGroupLabels)) {
                        $q->orWhereIn('tasks.label_id', array_merge($userGroupIds, $userGroupLabels));
                    }
                });
            }
        }

        if (! empty($this->userFilter)) {
            $targetUserId = $this->userFilter;
            $query->where(function (Builder $q) use ($targetUserId) {
                $q->where('tasks.user_id', $targetUserId)
                    ->orWhereHas('taskAssignments', function (Builder $subQ) use ($targetUserId) {
                        $subQ->where('user_id', $targetUserId);
                    });
            });
        }

        if (! empty($this->categoryFilter)) {
            $query->whereHas('category', function (Builder $catQuery) {
                $catQuery->where('name', $this->categoryFilter);
            });
        }

        if (! empty($this->statusFilter) && in_array($this->statusFilter, ['pending', 'in_progress', 'completed', 'canceled'], true)) {
            $query->where('tasks.status', $this->statusFilter);
        }

        if (! empty($this->groupFilter)) {
            $groupName = trim($this->groupFilter);

            $group = DB::table('groups')
                ->where('label', $groupName)
                ->orWhere('id', $groupName)
                ->first();

            if ($group) {
                $query->where(function (Builder $q) use ($group, $groupName) {
                    $q->where('tasks.label_id', (string) $group->id)
                        ->orWhere('tasks.label_id', $group->id)
                        ->orWhere('tasks.label_id', $groupName);
                });
            } else {
                $query->where('tasks.label_id', $groupName);
            }
        }

        return $query;
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
            ->defaultSort('id', 'desc')
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
                        'class' => 'cursor-pointer text-primary-600 hover:underline dark:text-primary-400 text-[11px] sm:text-xs py-1 px-1 min-w-[120px] max-w-[160px] truncate',
                    ]),

                TextColumn::make('creator.first_name')
                    ->label($this->getDbFieldLabel('creator_first_name', 'Assigned By'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->formatStateUsing(function (Task $record) {
                        /** @var \App\Models\User|null $creator */
                        $creator = $record->creator;
                        if ($creator) {
                            return trim(ucfirst(strtolower($creator->first_name)) . ' ' . ucfirst(strtolower($creator->last_name)));
                        }
                        return 'N/A';
                    })
                    ->extraAttributes(['class' => 'text-[11px] sm:text-xs py-1 px-1 whitespace-nowrap']),

                TextColumn::make('assignedUsers')
                    ->label($this->getDbFieldLabel('assigned_users', 'Assigned To'))
                    ->icon('heroicon-o-users')
                    ->formatStateUsing(function ($state, Task $record) {
                        /** @var Collection<int, \App\Models\User> $assignedUsers */
                        $assignedUsers = $record->assignedUsers;
                        if ($assignedUsers && $assignedUsers->isNotEmpty()) {
                            return $assignedUsers->map(function ($user) {
                                return trim(ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name)));
                            })->implode(', ');
                        }
                        return 'No users assigned';
                    })
                    ->extraAttributes(['class' => 'text-[11px] sm:text-xs py-1 px-1 min-w-[120px]']),

                TextColumn::make('transferred_trail')
                    ->label('Transfer History')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->formatStateUsing(function (Task $record) {
                        $trail = DB::table('task_transfers')
                            ->where('task_id', $record->id)
                            ->orderBy('id')
                            ->pluck('transfer_notes')
                            ->filter()
                            ->implode(' ➔ ');

                        return $trail ?: 'Direct Task';
                    })
                    ->visible(fn() => in_array(strtolower(Auth::user()?->role ?? ''), ['admin', 'super-admin', 'manager'], true))
                    ->extraAttributes(['class' => 'text-[11px] text-gray-500 py-1 px-1']),

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
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),

                TextColumn::make('category.name')
                    ->label($this->getDbFieldLabel('category_name', 'Category'))
                    ->icon('heroicon-o-tag')
                    ->sortable()
                    ->placeholder('No Category')
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),

                TextColumn::make('priority')
                    ->label($this->getDbFieldLabel('priority', 'Priority'))
                    ->badge()
                    ->sortable()
                    ->icon('heroicon-o-exclamation-circle')
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'info',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => Str::ucfirst($state) . ' Priority')
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'canceled' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn(string $state) => Str::headline($state))
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                    ]),
                SelectFilter::make('priority')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ]),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('verify_task')
                    ->label('Review Task')
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('info')
                    ->icon('heroicon-o-shield-check')
                    ->modalHeading(fn(Task $task) => 'Task Verification & Transfer - ' . $task->title)
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Save Verification')
                    ->fillForm(function (Task $task): array {
                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        return [
                            'change_user_reason' => $existing?->change_user_reason ?? '',
                            'status' => $existing?->status ?? 'pending',
                            'approval_decision' => ($existing && isset($existing->approval_status) && $existing->approval_status === 'pending') ? 'approve' : 'approve',
                            'transfer_to_user_id' => null,
                            'transfer_reason' => '',
                        ];
                    })
                    ->form(function (Task $task): array {
                        $userId = Auth::id();
                        $creator = $task->creator;
                        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === $userId);

                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        $isPendingApproval = $isCreator && $existing && isset($existing->approval_status) && $existing->approval_status === 'pending';

                        $schema = [
                            Textarea::make('change_user_reason')
                                ->label('Reason / Notes')
                                ->placeholder('Enter reason for option selection...')
                                ->required()
                                ->maxLength(1000)
                                ->rows(3),

                            Select::make('status')
                                ->label('Select Verification Option')
                                ->options([
                                    'pending'   => 'Pending',
                                    'verified'  => 'Verified',
                                    'hold'      => 'Hold',
                                    'cancel'    => 'Cancel',
                                ])
                                ->default('pending')
                                ->required()
                                ->native(true),

                            Select::make('transfer_to_user_id')
                                ->label('Transfer Task To (Optional)')
                                ->options(fn() => \App\Models\User::where('id', '!=', Auth::id())->pluck('first_name', 'id'))
                                ->native(true)
                                ->nullable(),

                            Textarea::make('transfer_reason')
                                ->label('Transfer Reason (Required if transferring)')
                                ->placeholder('Enter reason if transferring task to someone else...')
                                ->rows(2)
                                ->visible(fn(Get $get) => filled($get('transfer_to_user_id'))),
                        ];

                        if ($isPendingApproval) {
                            array_unshift(
                                $schema,
                                Select::make('approval_decision')
                                    ->label('Approval Action')
                                    ->options([
                                        'approve' => 'Approve Requested Status',
                                        'reject'  => 'Reject Requested Status',
                                    ])
                                    ->default('approve')
                                    ->required()
                                    ->native(true)
                            );
                        }

                        return $schema;
                    })
                    ->action(function (Task $task, array $data): void {
                        $userId = Auth::id();
                        $creator = $task->creator;
                        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === $userId);

                        DB::transaction(function () use ($task, $data, $isCreator, $userId) {
                            if (!empty($data['transfer_to_user_id'])) {
                                $currentUser = Auth::user();
                                $newUserId = (int) $data['transfer_to_user_id'];
                                $newUser = \App\Models\User::find($newUserId);

                                DB::table('task_assignments')->where('task_id', $task->id)->delete();
                                DB::table('task_assignments')->insert([
                                    'task_id' => $task->id,
                                    'user_id' => $newUserId,
                                ]);

                                $transferNote = "Transferred from " . trim(($currentUser->first_name ?? '') . ' ' . ($currentUser->last_name ?? '')) . " to " . trim(($newUser->first_name ?? '') . ' ' . ($newUser->last_name ?? '')) . ". Reason: " . ($data['transfer_reason'] ?? 'Review Task Transfer');

                                DB::table('task_transfers')->insert([
                                    'task_id' => $task->id,
                                    'from_user_id' => $currentUser->id,
                                    'to_user_id' => $newUserId,
                                    'transfer_notes' => $transferNote,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);

                                DB::table('task_conversations')->insert([
                                    'task_id' => $task->id,
                                    'user_id' => $currentUser->id,
                                    'message' => $transferNote,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }

                            if ($isCreator && isset($data['approval_decision'])) {
                                if ($data['approval_decision'] === 'reject') {
                                    $restoredStatus = 'pending';

                                    DB::table('task_verifications')->updateOrInsert(
                                        ['task_id' => $task->id],
                                        [
                                            'change_user_reason' => trim($data['change_user_reason']),
                                            'status' => $restoredStatus,
                                            'approval_status' => 'rejected',
                                            'updated_at' => now(),
                                        ]
                                    );

                                    DB::table('task_conversations')->insert([
                                        'task_id' => $task->id,
                                        'user_id' => $userId,
                                        'message' => "Task verification request REJECTED. Reason: " . trim($data['change_user_reason']),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);

                                    Notification::make()
                                        ->title('Request Rejected')
                                        ->body("Verification request rejected and set to pending.")
                                        ->warning()
                                        ->send();
                                } else {
                                    DB::table('task_verifications')->updateOrInsert(
                                        ['task_id' => $task->id],
                                        [
                                            'change_user_reason' => trim($data['change_user_reason']),
                                            'status' => $data['status'],
                                            'approval_status' => 'approved',
                                            'requested_by' => $userId,
                                            'created_by' => $userId,
                                            'updated_at' => now(),
                                        ]
                                    );

                                    if ($data['status'] === 'cancel') {
                                        $task->update(['status' => 'canceled']);
                                    } elseif ($data['status'] === 'verified') {
                                        $task->update(['status' => 'completed']);
                                    }

                                    DB::table('task_conversations')->insert([
                                        'task_id' => $task->id,
                                        'user_id' => $userId,
                                        'message' => 'Verification status APPROVED and set to [' . strtoupper($data['status']) . '].',
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);

                                    Notification::make()
                                        ->title('Verification Approved')
                                        ->body('Task verification status updated successfully.')
                                        ->success()
                                        ->send();
                                }
                            } else {
                                DB::table('task_verifications')->updateOrInsert(
                                    ['task_id' => $task->id],
                                    [
                                        'change_user_reason' => trim($data['change_user_reason']),
                                        'status' => $data['status'],
                                        'approval_status' => 'pending',
                                        'requested_by' => $userId,
                                        'created_by' => $userId,
                                        'updated_at' => now(),
                                        'created_at' => now(),
                                    ]
                                );

                                DB::table('task_conversations')->insert([
                                    'task_id' => $task->id,
                                    'user_id' => $userId,
                                    'message' => 'Submitted task verification request with status [' . strtoupper($data['status']) . ']. Reason: ' . trim($data['change_user_reason']),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);

                                Notification::make()
                                    ->title('Request Sent to Creator')
                                    ->body('Verification status change request sent for approval.')
                                    ->info()
                                    ->send();
                            }
                        });

                        $this->dispatch('$refresh');
                    }),

                Action::make('task_chat')
                    ->label('Comment')
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('gray')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading(fn(Task $task) => 'Task Chat - ' . $task->title)
                    ->modalWidth('xl')
                    ->modalSubmitActionLabel('Send')
                    ->form([
                        Textarea::make('message')
                            ->label('Message')
                            ->placeholder('Type message...')
                            ->required()
                            ->maxLength(1000)
                            ->rows(2)
                            ->extraAttributes([
                                'class' => 'text-xs sm:text-sm w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                            ]),
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
                    }),

                Action::make('edit_task')
                    ->label('Edit')
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn() => strtolower(Auth::user()?->role ?? '') === 'super-admin')
                    ->action(function (Task $task): void {
                        $this->dispatch('openTaskDetailsModal', taskId: $task->id);
                    }),

                Action::make('delete_task')
                    ->label('Delete')
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading(fn(Task $task) => 'Delete Task - ' . $task->title)
                    ->modalDescription('Are you sure you want to delete this task? This will permanently remove the task and all its connected records.')
                    ->modalSubmitActionLabel('Yes')
                    ->modalCancelActionLabel('No')
                    ->visible(fn() => strtolower(Auth::user()?->role ?? '') === 'super-admin')
                    ->action(function (Task $task): void {
                        DB::transaction(function () use ($task) {
                            $taskId = $task->id;

                            DB::table('task_assignments')->where('task_id', $taskId)->delete();
                            DB::table('task_transfers')->where('task_id', $taskId)->delete();
                            DB::table('task_verifications')->where('task_id', $taskId)->delete();
                            DB::table('task_conversations')->where('task_id', $taskId)->delete();

                            if (DB::getSchemaBuilder()->hasTable('reminders')) {
                                DB::table('reminders')->where('task_id', $taskId)->delete();
                            }
                            if (DB::getSchemaBuilder()->hasTable('task_completion_requests')) {
                                DB::table('task_completion_requests')->where('task_id', $taskId)->delete();
                            }

                            $task->delete();
                        });

                        Notification::make()
                            ->title('Task Deleted Successfully')
                            ->body('The task and all its connected flows were deleted permanently.')
                            ->success()
                            ->send();

                        $this->dispatch('$refresh');
                    }),
            ])
            ->striped();
    }

    public function sendTaskMessage(Task $task, string $message): void
    {
        DB::table('task_conversations')->insert([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'message' => trim($message),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Notification::make()
            ->title('Message sent')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    public function render(): View|Factory
    {
        $title = match ($this->taskView) {
            'my_tasks' => 'My Task List',
            'assigned_to_others' => 'Assigned Task List',
            default => 'All Tasks List',
        };

        return view('livewire.task-table', ['title' => $title])
            ->layout('components.layouts.app', [
                'title' => $title . ' | TMS',
            ]);
    }
}
