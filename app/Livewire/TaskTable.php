<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\TaskCompletionRequest;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Str;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    public $taskView; // Define taskView as a public property
    protected $listeners = ['taskCreated' => '$refresh', 'taskStatusUpdated' => '$refresh'];
    private $taskQuery;

    public function mount()
    {
        $this->taskView = request()->get('task_view', 'all_tasks'); // Default to 'all_tasks'
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn() => $this->getTaskQuery()
            )

            ->columns([
                Split::make([

                    Stack::make([

                        TextColumn::make('title')

                            ->label('')
                            ->searchable()
                            ->sortable()
                            ->weight(FontWeight::Bold)
                            ->icon('heroicon-m-clipboard-document-list')
                            ->color('primary')
                            ->action(function (Task $record): void {

                                $this->dispatch(
                                    'openTaskViewModal',
                                    $record->id
                                );
                            })

                            ->extraAttributes([
                                'class' => '
                                text-lg
                                font-bold
                                cursor-pointer
                                hover:text-primary-600
                                transition
                            '
                            ]),

                        TextColumn::make('creator.first_name')
                            ->label('')
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->formatStateUsing(
                                fn($record) =>
                                'Assigned By: ' .
                                    $record->creator->first_name .
                                    ' ' .
                                    $record->creator->last_name
                            ),

                        Split::make([
                            TextColumn::make('due_date')

                                ->label('')
                                ->sortable()
                                ->badge()
                                ->icon('heroicon-m-clock')
                                ->color(
                                    fn(string $state): string =>
                                    Carbon::parse($state)->isPast()
                                        ? 'danger'
                                        : 'success'
                                )

                                ->formatStateUsing(
                                    fn($state) =>
                                    Carbon::parse($state)
                                        ->format('d M Y h:i A')
                                ),

                            TextColumn::make('assignedUsers')

                                ->label('')
                                ->badge()
                                ->icon('heroicon-m-users')
                                ->color('info')
                                ->formatStateUsing(
                                    fn($state, $record) =>
                                    $record->assignedUsers
                                        ->map(function ($user) {

                                            return ucfirst(
                                                strtolower($user->first_name)
                                            ) . ' ' .
                                                ucfirst(
                                                    strtolower($user->last_name)
                                                );
                                        })
                                        ->join(', ')
                                ),

                            TextColumn::make('category.name')

                                ->label('')
                                ->badge()
                                ->icon('heroicon-m-tag')
                                ->color('gray')
                                ->formatStateUsing(
                                    fn($state) =>
                                    $state ?: 'No Category'
                                ),

                            TextColumn::make('recurrence')

                                ->label('')
                                ->badge()
                                ->icon('heroicon-m-arrow-path')
                                ->color('warning')
                                ->formatStateUsing(
                                    fn($state) => match ($state) {
                                        'daily' => 'Daily',
                                        'weekly' => 'Weekly',
                                        'monthly' => 'Monthly',
                                        'none' => 'No Repeat',
                                        default => ucfirst($state),
                                    }
                                ),

                            TextColumn::make('priority')

                                ->label('')
                                ->badge()
                                ->icon('heroicon-m-flag')
                                ->weight(FontWeight::Bold)
                                ->color(
                                    fn(string $state): string => match ($state) {
                                        'low' => 'success',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                        default => 'gray',
                                    }
                                )
                                ->formatStateUsing(
                                    fn($state) =>
                                    ucfirst($state) . ' Priority'
                                ),

                            TextColumn::make('group.label')

                                ->label('')
                                ->badge()
                                ->icon('heroicon-m-user-group')
                                ->color('info')
                                ->formatStateUsing(
                                    fn($state) =>
                                    $state
                                        ? 'Group: ' . $state
                                        : 'No Group'
                                ),

                            TextColumn::make('status')
                                ->label('')
                                ->badge()
                                ->icon('heroicon-m-signal')
                                ->weight(FontWeight::Bold)
                                ->color(
                                    fn(string $state): string => match ($state) {
                                        'pending' => 'gray',
                                        'in_progress' => 'warning',
                                        'complete_intimation' => 'info',
                                        'completed' => 'success',
                                        default => 'gray',
                                    }
                                )

                                ->formatStateUsing(function ($state, $record) {

                                    $loggedInUserId = Auth::id();

                                    if (
                                        $this->taskView === 'assigned_to_others'
                                    ) {

                                        if (
                                            optional($record->creator)->id
                                            === $loggedInUserId
                                        ) {

                                            return Str::headline($state);
                                        }
                                    } elseif (
                                        $this->taskView === 'my_tasks'
                                    ) {

                                        $userStatus = DB::table('task_updates')

                                            ->where(
                                                'task_id',
                                                $record->id
                                            )

                                            ->where(
                                                'user_id',
                                                $loggedInUserId
                                            )

                                            ->latest('updated_at')

                                            ->value('status');

                                        return $userStatus

                                            ? (
                                                $userStatus ===
                                                'complete_intimation'

                                                ? 'Requested for Completion'

                                                : Str::headline($userStatus)
                                            )

                                            : (
                                                $state ===
                                                'complete_intimation'

                                                ? 'Requested for Completion'

                                                : Str::headline($state)
                                            );
                                    }

                                    return Str::headline($state);
                                }),

                        ])->from('md'),

                    ])->space(3),

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
                    ->visible(
                        $this->taskView === 'my_tasks'
                    ),

                SelectFilter::make('category')->relationship('category', 'name'),
                SelectFilter::make('assigned_to')->relationship('assignedUsers','first_name')->searchable()
                ->visible($this->taskView === 'assigned_to_others'),

                SelectFilter::make('priority')

                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),

                Filter::make('overdue')

                    ->query(
                        fn(Builder $query) =>
                        $query
                            ->where('due_date', '<', now())
                            ->where('status', '!=', 'completed')
                    ),

            ], layout: FiltersLayout::AboveContentCollapsible)

            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->button()
                    ->form([

                        Select::make('user_id')
                            ->label('Select User')
                            ->options(function (Task $task) {

                                return $task->assignedUsers
                                    ->mapWithKeys(function ($user) {

                                        return [

                                            $user->id =>
                                            $user->first_name .
                                                ' ' .
                                                $user->last_name
                                        ];
                                    });
                            })

                            ->searchable()
                            ->required(),

                    ])

                    ->modalHeading('Approve Task')

                    ->modalDescription(
                        'Select user to approve task completion.'
                    )

                    ->modalSubmitActionLabel('Approve')

                    ->action(function (
                        array $data,
                        Task $task
                    ) {

                        $selectedUserId = $data['user_id'];

                        $this->approvalTask(
                            $task,
                            'approved',
                            $selectedUserId
                        );
                    })

                    ->visible(function (Task $task) {

                        return
                            $this->taskView ===
                            'assigned_to_others'

                            &&

                            $task->creator?->id ===
                            Auth::id()

                            &&

                            TaskCompletionRequest::where(
                                'task_id',
                                $task->id
                            )

                            ->where(
                                'request_status',
                                'pending'
                            )

                            ->exists();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->button()
                    ->action(function (Task $task) {

                        $this->approvalTask(
                            $task,
                            'rejected'
                        );
                    }),

                Action::make('in_progress')
                    ->label('Start')
                    ->color('warning')
                    ->icon('heroicon-m-play')
                    ->button()
                    ->action(function (Task $task) {

                        $this->updateStatus(
                            $task,
                            'in_progress'
                        );
                    }),

                Action::make('complete')
                    ->label('Complete')
                    ->color('success')
                    ->icon('heroicon-m-check')
                    ->button()
                    ->action(function (Task $task) {

                        $this->updateStatus(
                            $task,
                            'completed'
                        );
                    }),

                Action::make('completeintimation')
                    ->label('Request Complete')
                    ->color('info')
                    ->icon('heroicon-m-paper-airplane')
                    ->button()
                    ->action(function (Task $task) {

                        $this->updateStatus(
                            $task,
                            'complete_intimation'
                        );
                    }),

                Action::make('edit')
                    ->icon('heroicon-m-pencil-square')
                    ->color('info')
                    ->button()
                    ->tooltip('Edit Task')
                    ->action(
                        fn(Task $task) =>
                        $this->dispatch(
                            'openTaskDetailsModal',
                            $task->id
                        )
                    ),

                Action::make('delete')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Task')
                    ->modalDescription(
                        'Are you sure you want to delete this task?'
                    )
                    ->modalSubmitActionLabel('Delete')
                    ->action(
                        fn(Task $task) =>
                        $this->delete($task)
                    ),

            ])

            ->bulkActions([
                BulkActionGroup::make([ DeleteBulkAction::make(),])

            ])

            ->striped()
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50])
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ]);
    }

    public function updateStatus(Task $task, $status)
    {
        // Dispatch directly to TaskUpdateModal component with task details
        $this->dispatch('status-updated', [
            'task' => $task->toArray(),
            'status' => $status,
        ])->to(TaskUpdateModal::class);
    }

    public function approvalTask(Task $task, $status)
    {
        // Dispatch directly to TaskUpdateModal component with task details
        $this->dispatch('status-updated', [
            'task' => $task->toArray(),
            'status' => $status,
        ])->to(TaskApproval::class);
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
            // User's assigned tasks
            $query->whereHas('taskAssignments', function (Builder $query) use ($Auth_id) {
                $query->where('user_id', $Auth_id); // Check the authenticated user's ID
            });
        } elseif ($this->taskView === 'assigned_to_others') {
            $Auth_id = $user->id;

            // Tasks assigned to others but created by the logged-in user
            $query->where('user_id', $Auth_id)->whereHas('assignedUsers', function (Builder $query) use ($Auth_id) {
                $query->where('user_id', '!=', $Auth_id);
            });
            // } elseif ($user->role !== 'admin') {
        } elseif ($this->taskView === 'tasks' && $user->role !== 'admin') {
            $Auth_id = $user->id;

            $query->where('user_id', $Auth_id)
                ->orWhereHas('taskAssignments', function (Builder $query) use ($Auth_id) {
                    $query->where('user_id', $Auth_id); // Also include tasks assigned to the user
                });
        }
        // Admins see all tasks
        $this->taskQuery = $query;

        return $query;
    }

    public function render()
    {
        // show title based on $this->taskView
        $title = match ($this->taskView) {
            'my_tasks' => 'My Tasks',
            'assigned_to_others' => 'Tasks Assigned to Others',
            default => 'All Tasks',
        };

        return view('livewire.task-table')->layout('components.layouts.app', ['title' => $title]);
    }

    public function delete(Task $task)
    {
        // Logic for deleting a task
        $task->delete();
    }
}
