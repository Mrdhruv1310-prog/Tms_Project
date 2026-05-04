<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeamPerformance extends Component
{
    public $team = [];

    public function mount()
    {
        // Get the authenticated user
        $loggedInUser = Auth::user();

        // Fetch team performance data
        if ($loggedInUser->role === 'admin') {
            // Fetch team performance data with all statuses
            $this->team = User::withCount([
                'taskAssignments as completed_tasks_count' => function ($query) {
                    $query->whereHas('task', function ($taskQuery) {
                        $taskQuery->where('status', 'completed');
                    });
                },
                'taskAssignments as in_progress_tasks_count' => function ($query) {
                    $query->whereHas('task', function ($taskQuery) {
                        $taskQuery->where('status', 'in_progress');
                    });
                },
                'taskAssignments as pending_tasks_count' => function ($query) {
                    $query->whereHas('task', function ($taskQuery) {
                        $taskQuery->where('status', 'pending');
                    });
                },
                'taskAssignments as total_tasks_count' => function ($query) {
                    $query->whereHas('task');
                },
            ])
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'completed' => $user->completed_tasks_count,
                        'in_progress' => $user->in_progress_tasks_count,
                        'pending' => $user->pending_tasks_count,
                        'total' => $user->total_tasks_count,
                        'percentage' => $user->total_tasks_count > 0 ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100) : 0,
                    ];
                })
                ->toArray();
        }
        else {
            $loggedInUserId = Auth::user()->id;

            $this->team = User::whereHas('taskAssignments', function ($query) use ($loggedInUserId) {
                // Get task assignments where the task is created by the authenticated user
                $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                    $taskQuery->where('user_id', $loggedInUserId);
                });
            })
            ->where('id', '!=', $loggedInUserId) // Exclude the authenticated user
            ->withCount([
                'taskAssignments as completed_tasks_count' => function ($query) use ($loggedInUserId) {
                    // Count only completed tasks assigned by the authenticated user
                    $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                        $taskQuery->where('status', 'completed')
                                  ->where('user_id', $loggedInUserId);
                    });
                },
                'taskAssignments as in_progress_tasks_count' => function ($query) use ($loggedInUserId) {
                    // Count only in-progress tasks assigned by the authenticated user
                    $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                        $taskQuery->where('status', 'in_progress')
                                  ->where('user_id', $loggedInUserId);
                    });
                },
                'taskAssignments as pending_tasks_count' => function ($query) use ($loggedInUserId) {
                    // Count only pending tasks assigned by the authenticated user
                    $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                        $taskQuery->where('status', 'pending')
                                  ->where('user_id', $loggedInUserId);
                    });
                },
                'taskAssignments as total_tasks_count' => function ($query) use ($loggedInUserId) {
                    // Count only tasks assigned by the authenticated user
                    $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                        $taskQuery->where('user_id', $loggedInUserId);
                    });
                },
            ])
            ->get()
            ->map(function ($user) {
                // Calculate the percentage of completed tasks
                return [
                    'name' => $user->first_name . " " . $user->last_name,
                    'completed' => $user->completed_tasks_count,
                    'in_progress' => $user->in_progress_tasks_count,
                    'pending' => $user->pending_tasks_count,
                    'total' => $user->total_tasks_count,
                    'percentage' => ($user->total_tasks_count > 0) ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100) : 0
                ];
            })
            ->toArray();

        }
    }

    public function render()
    {
        return view('livewire.team-performance')->layout('components.layouts.app', [
            'title' => 'Team Performance | TMS',
        ]);
    }
}
