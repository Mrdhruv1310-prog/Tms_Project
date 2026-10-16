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

    /** @var Builder<Task>|null */
    private ?Builder $taskQuery = null;

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
        if ($this->taskQuery) {
            return $this->taskQuery;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $authId = (int) $user->id;
        $userRole = strtolower($user->role ?? 'employee');

        $query = Task::query()->whereHas('creator');

        $this->applyDateFiltersToTaskQuery($query);

        // Super Admin ke ilawa normal user restrictions
        if ($userRole !== 'super-admin') {
            $query->where(function (Builder $q) use ($authId) {
                $q->where('user_id', $authId)
                    ->orWhereHas('taskAssignments', function (Builder $subQ) use ($authId) {
                        $subQ->where('user_id', $authId);
                    });
            });
        }

        if (! empty($this->userFilter)) {
            $targetUserId = $this->userFilter;
            $query->where(function (Builder $q) use ($targetUserId) {
                $q->where('user_id', $targetUserId)
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
            $query->where('status', $this->statusFilter);
        }

        // Group Filter: Group Label_ID ya Group Members ke tasks (group_users table ke users)
        if (! empty($this->groupFilter)) {
            $groupName = trim($this->groupFilter);

            // Group Table se HR Group ID fetch karein
            $group = DB::table('groups')
                ->where('label', $groupName)
                ->first();

            if ($group) {
                // Strictly check label_id = group.id (or label_id = group.label)
                $query->where(function (Builder $q) use ($group, $groupName) {
                    $q->where('tasks.label_id', (string) $group->id)
                        ->orWhere('tasks.label_id', $group->id)
                        ->orWhere('tasks.label_id', $groupName);
                });
            } else {
                $query->where('tasks.label_id', $groupName);
            }
        }

        // Status Order: Pending -> In Progress -> Complete Intimation -> Completed -> Canceled
        $query->orderByRaw("
        CASE
            WHEN status = 'pending' THEN 1
            WHEN status = 'in_progress' THEN 2
            WHEN status = 'complete_intimation' THEN 3
            WHEN status = 'completed' THEN 4
            WHEN status = 'canceled' THEN 5
            ELSE 6
        END ASC
    ")->orderBy('id', 'desc');

        $this->taskQuery = $query;

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
                        return $creator ? $creator->first_name . ' ' . $creator->last_name : 'N/A';
                    })
                    ->extraAttributes(['class' => 'text-[11px] sm:text-xs py-1 px-1 whitespace-nowrap']),

                TextColumn::make('assignedUsers')
                    ->label($this->getDbFieldLabel('assigned_users', 'Assigned To'))
                    ->icon('heroicon-o-users')
                    ->formatStateUsing(function ($state, Task $record) {
                        /** @var Collection<int, \App\Models\User> $assignedUsers */
                        $assignedUsers = $record->assignedUsers;
                        return $assignedUsers->map(function ($user) {
                            return ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
                        })->join(', ');
                    })
                    ->extraAttributes(['class' => 'text-[11px] sm:text-xs py-1 px-1 min-w-[100px]']),

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
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),

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
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),

                TextColumn::make('group_display')
                    ->label($this->getDbFieldLabel('group_label', 'Group'))
                    ->badge()
                    ->color('info')
                    ->state(function (Task $record) {
                        if (blank($record->label_id)) {
                            return '-';
                        }

                        $group = DB::table('groups')
                            ->where('id', $record->label_id)
                            ->orWhere('label', $record->label_id)
                            ->first();

                        return $group ? ($group->label ?? $group->name ?? '-') : '-';
                    })
                    ->extraAttributes(['class' => 'text-[10px] sm:text-xs py-1 px-1 whitespace-nowrap']),

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
                        'canceled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (string $state, Task $record) {
                        $loggedInUserId = Auth::id();

                        if ($this->taskView === 'assigned_to_others') {
                            /** @var \App\Models\User|null $creator */
                            $creator = $record->creator;
                            if ($creator?->id === $loggedInUserId) {
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
                    ->extraAttributes(['class' => 'text-xs py-1 px-1 whitespace-nowrap']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'complete_intimation' => 'Request Complete',
                        'completed' => 'Completed',
                        'reassignment_pending' => 'Re-assignment Pending',
                        'canceled' => 'Canceled',
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
                    ->label(function (Task $task): string {
                        /** @var object{status?: string, approval_status?: string, previous_status?: string}|null $existing */
                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        if (! $existing) {
                            return 'Pending';
                        }

                        if (isset($existing->approval_status) && $existing->approval_status === 'rejected') {
                            return ucfirst($existing->status ?? 'Pending');
                        }

                        if (isset($existing->approval_status) && $existing->approval_status === 'pending') {
                            return 'Approval Pending (' . ucfirst($existing->status) . ')';
                        }

                        return ucfirst($existing->status ?? 'Pending');
                    })
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color(function (Task $task): string {
                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        $status = $existing?->status ?? 'pending';

                        return match ($status) {
                            'verified' => 'success',
                            'cancel'   => 'danger',
                            'hold'     => 'warning',
                            default    => 'warning',
                        };
                    })
                    ->extraAttributes(function (Task $task): array {
                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        $status = $existing?->status ?? 'pending';

                        // Force inline CSS for accurate color rendering
                        return match ($status) {
                            'verified' => ['style' => 'background-color: #10b981 !important; color: #ffffff !important; border-color: #059669 !important; font-size: 11px !important; padding: 2px 6px !important;'],
                            'cancel'   => ['style' => 'background-color: #ef4444 !important; color: #ffffff !important; border-color: #dc2626 !important; font-size: 11px !important; padding: 2px 6px !important;'],
                            'hold'     => ['style' => 'background-color: #f59e0b !important; color: #ffffff !important; border-color: #d97706 !important; font-size: 11px !important; padding: 2px 6px !important;'],
                            default    => ['style' => 'background-color: #f59e0b !important; color: #ffffff !important; border-color: #d97706 !important; font-size: 11px !important; padding: 2px 6px !important;'],
                        };
                    })
                    ->icon('heroicon-o-shield-check')
                    ->modalHeading(fn(Task $task) => 'Task Verification Request - ' . $task->title)
                    ->modalWidth('lg')
                    ->modalSubmitActionLabel('Save Verification')
                    ->fillForm(function (Task $task): array {
                        /** @var object{change_user_reason?: string, status?: string, approval_status?: string}|null $existing */
                        $existing = DB::table('task_verifications')
                            ->where('task_id', $task->id)
                            ->latest('id')
                            ->first();

                        return [
                            'change_user_reason' => $existing?->change_user_reason ?? '',
                            'status' => $existing?->status ?? 'pending',
                            'approval_decision' => ($existing && isset($existing->approval_status) && $existing->approval_status === 'pending') ? 'approve' : 'approve',
                        ];
                    })
                    ->form(function (Task $task): array {
                        $userId = Auth::id();
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === $userId);

                        /** @var object{approval_status?: string}|null $existing */
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
                                    'pending' => 'Pending',
                                    'verified' => 'Verified',
                                    'hold' => 'Hold',
                                    'cancel' => 'Cancel',
                                ])
                                ->required()
                                ->native(false),
                        ];

                        if ($isPendingApproval) {
                            array_unshift(
                                $schema,
                                Select::make('approval_decision')
                                    ->label('Approval Action')
                                    ->options([
                                        'approve' => 'Approve Requested Status',
                                        'reject' => 'Reject Requested Status',
                                    ])
                                    ->default('approve')
                                    ->required()
                                    ->native(false)
                            );
                        }

                        return $schema;
                    })
                    ->action(function (Task $task, array $data): void {
                        $userId = Auth::id();
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === $userId);

                        DB::transaction(function () use ($task, $data, $isCreator, $userId) {
                            /** @var object{id?: int, status?: string, previous_status?: string, approval_status?: string}|null $existingVerification */
                            $existingVerification = DB::table('task_verifications')
                                ->where('task_id', $task->id)
                                ->latest('id')
                                ->first();

                            if ($isCreator && isset($data['approval_decision'])) {
                                // CASE 1: Creator Decision (Approve / Reject)
                                if ($data['approval_decision'] === 'reject') {
                                    // Rejection: previous_status me stored value fetch karke status me wapis restore karein
                                    $restoredStatus = $existingVerification?->previous_status ?? 'pending';

                                    DB::table('task_verifications')
                                        ->where('task_id', $task->id)
                                        ->update([
                                            'change_user_reason' => trim($data['change_user_reason']),
                                            'status' => $restoredStatus, // Restored back to previous status
                                            'approval_status' => 'rejected',
                                            'updated_at' => now(),
                                        ]);

                                    DB::table('task_conversations')->insert([
                                        'task_id' => $task->id,
                                        'user_id' => $userId,
                                        'message' => 'Verification status change request was REJECTED. Restored back to previous status [' . strtoupper($restoredStatus) . ']. Reason: ' . trim($data['change_user_reason']),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);

                                    Notification::make()
                                        ->title('Request Rejected')
                                        ->body('Verification status request rejected and restored to ' . ucfirst($restoredStatus) . '.')
                                        ->warning()
                                        ->send();
                                } else {
                                    // Approval: Requested status officially accept hoga
                                    DB::table('task_verifications')->updateOrInsert(
                                        ['task_id' => $task->id],
                                        [
                                            'change_user_reason' => trim($data['change_user_reason']),
                                            'status' => $data['status'],
                                            'approval_status' => 'approved',
                                            'requested_by' => $userId,
                                            'created_by' => $userId,
                                            'updated_at' => now(),
                                            'created_at' => now(),
                                        ]
                                    );

                                    if ($data['status'] === 'cancel') {
                                        $task->update(['status' => 'canceled']);
                                    } elseif ($data['status'] === 'pending' && $task->status === 'canceled') {
                                        $task->update(['status' => 'pending']);
                                    }

                                    DB::table('task_conversations')->insert([
                                        'task_id' => $task->id,
                                        'user_id' => $userId,
                                        'message' => 'Verification status APPROVED and set to [' . strtoupper($data['status']) . ']. Reason: ' . trim($data['change_user_reason']),
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
                                // CASE 2: Assigned User Submit Request (Save Verification Click)
                                // Pehle se jo active status tha use previous_status me store karein
                                $activePreviousStatus = $existingVerification?->status ?? 'pending';

                                DB::table('task_verifications')->updateOrInsert(
                                    ['task_id' => $task->id],
                                    [
                                        'change_user_reason' => trim($data['change_user_reason']),
                                        'previous_status' => $activePreviousStatus, // Stored previous status before update
                                        'status' => $data['status'], // New requested status
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
                                    'message' => 'Requested verification status change to [' . strtoupper($data['status']) . ']. Sent for approval. Reason: ' . trim($data['change_user_reason']),
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
                    })
                    ->visible(function (Task $task) {
                        $userId = Auth::id();
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === (int) $userId);
                        $isAssigned = DB::table('task_assignments')->where('task_id', $task->id)->where('user_id', $userId)->exists();
                        /** @var \App\Models\User $authUser */
                        $authUser = Auth::user();
                        return $isCreator || $isAssigned || ($authUser->role === 'super-admin');
                    }),

                Action::make('approve')
                    ->action(fn(Task $task) => $this->approveCompletionRequest($task))
                    ->label($this->getDbFieldLabel('btn_approve', 'Approve'))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve completion request')
                    ->modalDescription('Are you sure you want to approve this task completion request?')
                    ->modalSubmitActionLabel('Yes, approve')
                    ->visible(function (Task $task) {
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        return $this->taskView === 'assigned_to_others'
                            && $creator?->id === Auth::id()
                            && TaskCompletionRequest::query()->where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();
                    }),

                Action::make('approve_reassignment')
                    ->action(fn(Task $task, array $data) => $this->approveReassignmentRequest($task, $data))
                    ->label($this->getDbFieldLabel('btn_approve_reassignment', 'Approve Re-assignment'))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
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
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        return $this->taskView === 'assigned_to_others'
                            && $creator?->id === Auth::id()
                            && $task->status === 'reassignment_pending';
                    }),

                Action::make('reject_reassignment')
                    ->action(fn(Task $task) => $this->rejectReassignmentRequest($task))
                    ->label($this->getDbFieldLabel('btn_reject_reassignment', 'Reject Re-assignment'))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('danger')
                    ->icon('heroicon-o-user-minus')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Task Re-assignment')
                    ->modalDescription('Are you sure you want to reject this re-assignment request?')
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->visible(function (Task $task) {
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        return $this->taskView === 'assigned_to_others'
                            && $creator?->id === Auth::id()
                            && $task->status === 'reassignment_pending';
                    }),

                Action::make('reject')
                    ->action(fn(Task $task) => $this->rejectCompletionRequest($task))
                    ->label($this->getDbFieldLabel('btn_reject', 'Reject'))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Reject completion request')
                    ->modalDescription('Are you sure you want to reject this completion request? Task will go back to In Progress.')
                    ->modalSubmitActionLabel('Yes, reject')
                    ->visible(function (Task $task) {
                        /** @var \App\Models\User|null $creator */
                        $creator = $task->creator;
                        return $this->taskView === 'assigned_to_others'
                            && $creator?->id === Auth::id()
                            && TaskCompletionRequest::query()->where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();
                    }),

                Action::make('status_update')
                    ->label(fn(Task $task) => $this->getStatusActionLabel($task))
                    ->button()
                    ->size(ActionSize::ExtraSmall)
                    ->color(fn(Task $task) => $this->getStatusActionColor($task))
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->modalHeading(fn(Task $task) => 'Update Status & Conversation - ' . $task->title)
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Update')
                    ->modalCloseButton(true)
                    ->closeModalByClickingAway(true)
                    ->closeModalByEscaping(true)
                    ->fillForm(function (Task $task): array {
                        $reminder = Reminder::where('task_id', $task->id)
                            ->whereIn('reminder_unit', ['minutes', 'hours', 'days'])
                            ->latest('id')
                            ->first();

                        return [
                            'status' => $task->status ?? 'pending',
                            'repeat' => ($task->recurrence && $task->recurrence !== 'none'),
                            'recurrence' => $task->recurrence ?: 'none',
                            'recurrence_end_date' => $task->recurrence_end_date ? Carbon::parse($task->recurrence_end_date)->format('Y-m-d') : null,
                            'due_date' => $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d H:i') : null,
                            'reminderTime' => $reminder ? $reminder->reminder_value : $task->reminderTime,
                            'reminderUnit' => $reminder ? $reminder->reminder_unit : $task->reminderUnit,
                        ];
                    })
                    ->form(function (): array {
                        return [
                            Select::make('status')
                                ->label('Select Option')
                                ->options([
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                ])
                                ->required()
                                ->dehydrated(true)
                                ->native(true),

                            Toggle::make('repeat')
                                ->label('Repeat')
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
                                ->native(false)
                                ->live()
                                ->dehydrated(true)
                                ->visible(fn(Get $get) => (bool) $get('repeat')),

                            DatePicker::make('recurrence_end_date')
                                ->label('Recurrence End Date')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->dehydrated(true)
                                ->visible(fn(Get $get) => (bool) $get('repeat') && $get('recurrence') !== 'none')
                                ->nullable(),

                            DateTimePicker::make('due_date')
                                ->label('Due Date & Time')
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
                    ->size(ActionSize::ExtraSmall)
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
                    ->iconButton()
                    ->size(ActionSize::Small)
                    ->color('info')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function (Task $task): void {
                        $this->dispatch('openTaskDetailsModal', taskId: $task->id);
                    })
                    ->visible(function (Task $task) {
                        /** @var \App\Models\User|null $authUser */
                        $authUser = Auth::user();
                        return in_array($authUser?->role, ['admin', 'super-admin'], true);
                    }),

                Action::make('delete')
                    ->action(fn(Task $task) => $this->delete($task))
                    ->iconButton()
                    ->size(ActionSize::Small)
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->modalIcon('heroicon-o-trash')
                    ->modalIconColor('danger')
                    ->modalHeading('Delete task')
                    ->modalDescription('Are you sure you\'d like to delete this task? This cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it')
                    ->modalAlignment(Alignment::Center)
                    ->visible(function (Task $task) {
                        /** @var \App\Models\User|null $authUser */
                        $authUser = Auth::user();
                        return in_array($authUser?->role, ['admin', 'super-admin'], true);
                    }),
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
            ->emptyStateHeading('No Tasks Found')
            ->emptyStateDescription('There are no tasks matching your selected date or timeline filter.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->striped();
    }

    private function applyVerificationChange(Task $task, string $status, string $reason): void
    {
        DB::transaction(function () use ($task, $status, $reason) {
            DB::table('task_verifications')->updateOrInsert(
                ['task_id' => $task->id],
                [
                    'change_user_reason' => trim($reason),
                    'status' => $status,
                    'approval_status' => 'approved',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            if ($status === 'hold' || $status === 'verified' || $status === 'cancel') {
                Reminder::query()->where('task_id', $task->id)->delete();
            }

            if ($status === 'cancel') {
                $task->update(['status' => 'canceled']);
            } elseif ($status === 'pending') {
                if ($task->status === 'canceled') {
                    $task->update(['status' => 'pending']);
                }
                $this->scheduleTaskReminders($task);
            }

            DB::table('task_conversations')->insert([
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'message' => 'Verification status officially updated to [' . strtoupper($status) . ']. Reason: ' . trim($reason),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    private function scheduleTaskReminders(Task $task): void
    {
        if ($task->due_date && $task->reminderTime && $task->reminderUnit) {
            $dueDate = Carbon::parse($task->due_date);
            $reminderTriggerTime = match ($task->reminderUnit) {
                'minutes' => $dueDate->copy()->subMinutes((int)$task->reminderTime),
                'hours' => $dueDate->copy()->subHours((int)$task->reminderTime),
                'days' => $dueDate->copy()->subDays((int)$task->reminderTime),
                default => null,
            };

            if ($reminderTriggerTime) {
                $selectedUsers = DB::table('task_assignments')
                    ->where('task_id', $task->id)
                    ->pluck('user_id')
                    ->map(fn($id) => (int) $id)
                    ->unique();

                if ($selectedUsers->isEmpty()) {
                    $selectedUsers = collect([(int) $task->user_id]);
                }

                foreach ($selectedUsers as $userId) {
                    $reminderId = DB::table('reminders')->insertGetId([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                        'reminder_time' => $reminderTriggerTime,
                        'reminder_unit' => $task->reminderUnit,
                        'reminder_value' => $task->reminderTime,
                    ]);

                    SendReminderJob::dispatch(
                        $reminderId,
                        ['email', 'SMS'],
                        "Reminder: Your task '{$task->title}' is due on " . $dueDate->format('d-m-Y H:i') . "."
                    )->delay($reminderTriggerTime);

                    \App\Jobs\SendTaskDueWhatsAppJob::dispatch(
                        $task->id,
                        $userId,
                        $dueDate->format('Y-m-d H:i:s')
                    )->delay($reminderTriggerTime);
                }
            }
        }
    }

    public function updateStatus(Task $task, string $status): void
    {
        $this->dispatch('status-updated', [
            'task' => $task->toArray(),
            'status' => $status,
        ])->to(TaskUpdateModal::class);
    }

    /**
     * @param array<string, mixed> $data
     */
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
            $allowedStatuses = ['pending', 'in_progress', 'completed', 'canceled'];

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

                DB::table('reminders')->where('task_id', $task->id)->delete();

                if ($newStatus !== 'completed' && $newStatus !== 'canceled' && $dueDate && $reminderTriggerTime) {
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
                        $reminderId = DB::table('reminders')->insertGetId([
                            'task_id' => $task->id,
                            'user_id' => $userId,
                            'reminder_time' => $reminderTriggerTime,
                            'reminder_unit' => $reminderUnitVal,
                            'reminder_value' => $reminderTimeVal,
                        ]);

                        SendReminderJob::dispatch(
                            $reminderId,
                            ['email', 'SMS'],
                            "Reminder: Your task '{$task->title}' is due on " . $dueDate->format('d-m-Y H:i') . "."
                        )->delay($reminderTriggerTime);

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

        /** @var \App\Models\User|null $creator */
        $creator = $task->creator;
        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === $userId);
        $isAssigned = DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->exists();

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        $userRole = strtolower($authUser?->role ?? '');

        if (! $isCreator && ! $isAssigned && ! in_array($userRole, ['admin', 'super-admin'], true)) {
            return false;
        }

        return true;
    }

    private function getStatusActionLabel(Task $task): string
    {
        return match ($task->status) {
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Complete',
            'canceled'    => 'Canceled',
            default       => 'Pending',
        };
    }

    private function getStatusActionColor(Task $task): string
    {
        return match ($task->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'canceled' => 'gray',
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

            TaskCompletionRequest::query()
                ->where('task_id', $task->id)
                ->where('request_status', 'pending')
                ->update([
                    'request_status' => 'rejected',
                ]);

            /** @var Collection<int, \App\Models\TaskAssignment> $taskAssignments */
            $taskAssignments = $task->taskAssignments;
            foreach ($taskAssignments as $assignment) {
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
            /** @var TaskCompletionRequest|null $pendingRequest */
            $pendingRequest = TaskCompletionRequest::query()
                ->where('task_id', $task->id)
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
            /** @var TaskCompletionRequest|null $pendingRequest */
            $pendingRequest = TaskCompletionRequest::query()
                ->where('task_id', $task->id)
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

    /**
     * @param array<string, mixed> $data
     */
    public function approveReassignmentRequest(Task $task, array $data): void
    {
        DB::transaction(function () use ($task, $data) {
            /** @var object{id: int, new_user_id: int, status: string}|null $pendingRequest */
            $pendingRequest = DB::table('task_reassignment_requests')
                ->where('task_id', $task->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            $newUserId = $pendingRequest?->new_user_id;

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
                DB::table('task_conversations')->insert([
                    'task_id' => $task->id,
                    'user_id' => Auth::id(),
                    'message' => 'Re-assignment Approved: ' . trim($data['approval_comment']),
                    'created_at' => now(),
                    'updated_at' => now(),
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

    private function canChatOnTask(Task $task): bool
    {
        $userId = Auth::id();

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (strtolower($authUser?->role ?? '') === 'super-admin') {
            return true;
        }

        /** @var \App\Models\User|null $creator */
        $creator = $task->creator;
        $isCreator = ($creator?->id === $userId) || ((int) $task->user_id === $userId);
        $isAssigned = DB::table('task_assignments')
            ->where('task_id', $task->id)
            ->where('user_id', $userId)
            ->exists();

        return $isCreator || $isAssigned;
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
            ->title('Task Deleted successfully')
            ->danger()
            ->send();

        $this->dispatch('taskDeleted');
        $this->dispatch('$refresh');
    }

    /**
     * @param Collection<int, Task> $records
     */
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
                ->title('No tasks deleted')
                ->body('You are not allowed to delete the selected tasks.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Tasks Deleted successfully')
            ->body($deletedCount . ' selected task(s) deleted successfully.')
            ->danger()
            ->send();

        $this->dispatch('taskDeleted');
        $this->dispatch('$refresh');
    }

    private function canDeleteTask(Task $task): bool
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        return Auth::check() && in_array(strtolower($authUser?->role ?? ''), ['admin', 'super-admin'], true);
    }

    private function deleteTaskWithRelatedData(Task $task, bool $useTransaction = true): void
    {
        $callback = function () use ($task) {
            DB::table('task_updates')->where('task_id', $task->id)->delete();
            TaskCompletionRequest::query()->where('task_id', $task->id)->delete();
            DB::table('task_conversations')->where('task_id', $task->id)->delete();
            DB::table('task_verifications')->where('task_id', $task->id)->delete();
            Reminder::query()->where('task_id', $task->id)->delete();
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

            Reminder::query()->where('task_id', $task->id)->delete();
        } catch (\Throwable $e) {
            Log::warning('Could not clear reminders for completed task: ' . $e->getMessage());
        }
    }
}
