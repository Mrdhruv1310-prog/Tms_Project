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
                // Task::query()->with(['assignedUsers', 'category']),
                // ->orderByDesc('id'),
            ) // Eager-load relationships
            ->columns([
                Split::make([
                    // First Column (Task Details) - this should take up most of the space
                    Stack::make([
                        // Task Title (1st row)
                        TextColumn::make('title')
                            //dispatch event
                            ->action(function (Task $record): void {
                                $this->dispatch('openTaskViewModal', $record->id);
                            })
                            ->searchable()
                            ->label('Task Title')
                            ->tooltip(fn($record, $column) => $column->getLabel())
                            ->weight(FontWeight::Bold)
                            ->icon('heroicon-o-clipboard'),

                        // Assigned By, Due Date, and Status side by side (2nd row)
                        Split::make([
                            TextColumn::make('creator.first_name')
                                ->label('Assigned By')
                                ->formatStateUsing(fn($record) => 'Assigned By: ' . $record->creator->first_name . ' ' . $record->creator->last_name)
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-user'),
                        ]),


                        // Assigned User, Category, and Priority side by side (3rd row)
                        Split::make([
                            TextColumn::make('due_date')
                                ->sortable()
                                ->searchable()
                                ->label('Due Date')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-clock')
                                ->extraAttributes(['class' => 'whitespace-nowrap overflow-hidden'])
                                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:i'))
                                ->color(fn(string $state): string => Carbon::parse($state)->isPast() ? 'danger' : 'info'),

                            TextColumn::make('assignedUsers') // Assuming it's a relationship that returns a collection of users
                                ->label('Assigned To')
                                ->tooltip(fn($record, $column) => $column->getLabel())
                                ->icon('heroicon-o-user-group')
                                ->formatStateUsing(fn($state, $record) => $record->assignedUsers->map(function ($user) {
                                    return ucfirst(strtolower($user->first_name)) . ' ' . ucfirst(strtolower($user->last_name));
                                })->join(', ')) // Join users with commas if there are multiple users
                                ->extraAttributes(['class' => 'whitespace-nowrap overflow-hidden']),

                            TextColumn::make('category.name')->label('Category')->tooltip(fn($record, $column) => $column->getLabel())->icon('heroicon-o-tag'),

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
                                ->extraAttributes(['class' => 'whitespace-nowrap overflow-hidden']),

                            TextColumn::make('priority')
                                ->badge()
                                ->color(
                                    fn(string $state): string => match ($state) {
                                        'low' => 'success',
                                        'medium' => 'info',
                                        'high' => 'danger',
                                    },
                                )
                                //capital first of priority
                                ->formatStateUsing(fn($state) => Str::ucfirst($state) . ' Priority') // Add text before the name
                                ->label('Priority')
                                ->tooltip(fn($record, $column) => $column->getLabel()),
                            TextColumn::make('group.label') // Use a relationship or define group_id logic
                                ->badge()
                                ->color('info')
                                ->label('Group') // Column label
                                ->tooltip(fn($record, $column) => $column->getLabel()) // Tooltip to show the column label
                                ->sortable() // Make it sortable
                                ->searchable() // Allow searching the column
                                ->formatStateUsing(fn($state) => $state ? 'Group: ' . $state : 'No Group') // Add prefix and handle null cases
                                ->extraAttributes(['class' => 'whitespace-nowrap overflow-hidden']), // Extra styling

                            TextColumn::make('status')
                                ->badge()
                                ->color(
                                    fn(string $state): string => match ($state) {
                                        'pending' => 'primary',
                                        'in_progress' => 'warning',
                                        'complete_intimation' => 'info',
                                        'completed' => 'success',
                                    },
                                )
                                ->label('Status')
                                //when taskview is assigned to others show status from task table only
                                ->formatStateUsing(function ($state, $record) {
                                    $loggedInUserId = Auth::id(); // Get the logged-in user ID
                                    // Check if the logged-in user is the creator
                                    if ($this->taskView === 'assigned_to_others') {
                                        if (optional($record->creator)->id === $loggedInUserId) {
                                            // Fetch status from the tasks table
                                            return Str::ucfirst($state);
                                        }
                                    } elseif ($this->taskView === 'my_tasks') {
                                        // Fetch the status from the task_updates table for the specific user
                                        $userStatus = DB::table('task_updates')
                                            ->where('task_id', $record->id)
                                            ->where('user_id', $loggedInUserId)
                                            ->orderByDesc('updated_at') // Get the latest status update
                                            ->value('status');

                                        // Fallback to task status if no updates found
                                        return $userStatus
                                            ? ($userStatus === 'complete_intimation' ? 'Requested for Completion' : Str::ucfirst($userStatus))
                                            : ($state === 'complete_intimation' ? 'Requested for Completion' : Str::ucfirst($state));
                                    } else {
                                        return Str::ucfirst($state);
                                    }
                                })
                                ->tooltip(fn($record, $column) => $column->getLabel()),

                        ])->from('md'),
                    ]), // Larger portion of the space
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

                // Conditionally add the Assigned To filter based on task view
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
            // ->headerActions([
            //     ExportAction::make()
            //         ->exporter(Task::class)
            // ])
            ->actions([

                Action::make('approve')
                    ->action(function (Task $task) {
                        $this->approvalTask($task, 'approved'); // Update status to approved
                    })
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(function (Task $task) {
                        // Check if the logged-in user is the task creator and there's a pending request
                        return $this->taskView === 'assigned_to_others' && $task->creator?->id === Auth::user()->id && // Use safe navigation operator to avoid errors
                            TaskCompletionRequest::where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();
                    }),

                Action::make('reject')
                    ->action(function (Task $task) {
                        $this->approvalTask($task, 'rejected'); // Update status to rejected
                    })
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(function (Task $task) {
                        // Check if the logged-in user is the task creator and there's a pending request
                        return $this->taskView === 'assigned_to_others' && $task->creator?->id === Auth::user()->id && // Ensure current user is the creator
                            TaskCompletionRequest::where('task_id', $task->id)
                            ->where('request_status', 'pending')
                            ->exists();
                    }),

                // In Progress Button
                Action::make('in_progress')
                    ->action(function (Task $task) {
                        $this->updateStatus($task, 'in_progress'); // Update the status
                    })
                    ->label('In Progress')
                    ->color('warning')
                    ->icon('heroicon-o-forward')
                    ->visible(function (Task $task) {
                        $userId = Auth::user()->id;

                        // Check if the user is assigned to this task
                        $isAssigned = DB::table('task_assignments')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->exists();

                        // Fetch My Task, Assigned Task, All Tasks Status
                        $userStatus = DB::table('task_updates')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->latest('updated_at')
                            ->value('status');

                        if ($this->taskView === 'my_tasks' && $isAssigned && ($userStatus === 'pending' || is_null($userStatus))) {
                            return true;
                        }
                    }),

                // Complete Button
                Action::make('complete')
                    ->action(function (Task $task) {
                        $this->updateStatus($task, 'completed'); // Update the status
                    })
                    ->label('Complete')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(function (Task $task) {
                        $userId = Auth::user()->id;

                        // Fetch the latest status for the logged-in user for this task
                        $userStatus = DB::table('task_updates')
                            ->where('task_id', $task->id)       // Filter by task ID
                            ->where('user_id', $userId)         // Filter by user ID (User 5 in this case)
                            ->orderBy('updated_at', 'desc') // Sort by the updated_at timestamp in descending order
                            ->limit(1)                    // Limit the result to the latest record
                            ->value('status');            // Get the 'status' field of the latest entry

                        // Disable if the user's status is already completed
                        return $this->taskView === 'my_tasks' && $userStatus === 'in_progress'
                            && $userId === optional($task->creator)->id
                            && $task->taskAssignments->contains('user_id', $userId);
                    }),
                Action::make('completeintimation')
                    ->action(function (Task $task) {
                        $this->updateStatus($task, 'complete_intimation');
                    })
                    ->label('Complete Intimate')
                    ->color('info')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(function (Task $task) {

                        $userId = Auth::user()->id;

                        // Check if user is assigned
                        $isAssigned = DB::table('task_assignments')
                            ->where('task_id', $task->id)
                            ->where('user_id', $userId)
                            ->exists();

                        // Latest user status
                        $userStatus = DB::table('task_updates')
                            ->where('task_id', $task->id)       // Filter by task ID
                            ->where('user_id', $userId)         // Filter by user ID (User 5 in this case)
                            ->orderBy('updated_at', 'desc') // Sort by the updated_at timestamp in descending order
                            ->limit(1)                    // Limit the result to the latest record
                            ->value('status');            // Get the 'status' field of the latest entry

                        //show button if user is assigned to task and task is in progress and user is not the creator

                        if ($isAssigned && $userStatus === 'in_progress' && $userId !== optional($task->creator)->id) {
                            return true;
                        }
                    }),


                // Edit Button
                Action::make('edit')
                    ->badge()
                    ->badgeColor('info')
                    ->size(ActionSize::Large)
                    ->action(fn(Task $task) => $this->dispatch('openTaskDetailsModal', $task->id))

                    ->label('')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn(Task $task) => (Auth::user()->role === 'admin') ||
                        (Auth::user()->id === $task->user_id && ($task->status === 'pending' || $task->status === 'in_progress'))),

                // Delete Button
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
                    ->visible(fn(Task $task) => Auth::user()->role === 'admin' || Auth::user()->id === $task->user_id), // Check if the user is a "user" and is the creator of the task
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('id', 'desc')->striped();
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
