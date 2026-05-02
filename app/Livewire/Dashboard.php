<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task; // Assuming you have a Task model
use App\Models\Category; // Assuming you have a Category model
use App\Models\Group;
use App\Models\User; // Assuming you have a User model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $labels = [];
    public $categories = [];
    public $team = [];
    public $groups = [];
    public $tasksAssignedByUser = [];

    public function mount()
    {
        $user = Auth::user(); // Get the logged-in user
        $isAdmin = $user->role === 'admin'; // Check if the user is an admin
    
        // Fetch task counts based on the user role
        $this->labels = [
            [
                'title' => 'Pending',
                'count' => Task::when(!$isAdmin, function ($query) use ($user) {
                    // Only fetch tasks assigned to the user (not admin)
                    $query->whereHas('taskAssignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->where('status', 'pending')->count(),
                'status' => 'pending',
                'bg' => '#fce2c7', 'border' => '#f9d6b3'
            ],
            [
                'title' => 'In Progress',
                'count' => Task::when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('taskAssignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->where('status', 'in_progress')->count(),
                'status' => 'in-progress',
                'bg' => '#cbe6fc', 'border' => '#afd7f9'
            ],
            [
                'title' => 'Completed',
                'count' => Task::when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('taskAssignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->where('status', 'completed')->count(),
                'status' => 'completed',
                'bg' => '#caf5de', 'border' => '#9ffac9'
            ],
            [
                'title' => 'Total',
                'count' => Task::when(!$isAdmin, function ($query) use ($user) {
                    $query->whereHas('taskAssignments', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                })->count(),
                'status' => 'total',
                'bg' => '#f5f3fa', 'border' => '#d6d0e4'
            ]
        ];
    
        // Fetch categories and tasks data based on the user role
        $this->categories = Category::withCount([
            'tasks as completed_tasks_count' => function ($query) use ($isAdmin, $user) {
                $query->when(!$isAdmin, function ($q) use ($user) {
                    $q->whereHas('taskAssignments', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                })->where('status', 'completed');
            },
            'tasks as total_tasks_count' => function ($query) use ($isAdmin, $user) {
                $query->when(!$isAdmin, function ($q) use ($user) {
                    $q->whereHas('taskAssignments', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
                });
            }
        ])->get()->map(function ($category) {
            return [
                'title' => $category->name,
                'completed' => $category->completed_tasks_count,
                'total' => $category->total_tasks_count,
                'percentage' => ($category->total_tasks_count > 0) ? round(($category->completed_tasks_count / $category->total_tasks_count) * 100) : 0
            ];
        })->toArray();
    
        // Fetch tasks assigned BY the user if the user is NOT an admin
        if (!$isAdmin) {
            $loggedInUserId = Auth::user()->id; // Get the authenticated user's ID

            $this->team = User::whereHas('taskAssignments', function ($query) use ($loggedInUserId) {
                    // Get task assignments where the task is created by the authenticated user
                    $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                        $taskQuery->where('user_id', $loggedInUserId);
                    });
                })
                ->where('id', '!=', $loggedInUserId) // Exclude the authenticated user
                ->withCount([
                    'taskAssignments as completed_tasks_count' => function ($query) use ($loggedInUserId) {
                        // Count only completed tasks assigned to the user
                        $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                            $taskQuery->where('status', 'completed')
                            ->where('user_id', $loggedInUserId); // Only tasks assigned by authenticated user
                        });
                    },
                    'taskAssignments as total_tasks_count' => function ($query) use ($loggedInUserId) {
                                // Count only tasks assigned by the authenticated user
                                $query->whereHas('task', function ($taskQuery) use ($loggedInUserId) {
                                    $taskQuery->where('user_id', $loggedInUserId); // Only tasks assigned by authenticated user
                                });
                    },
                        ])
                ->get()
                ->map(function ($user) {
                    // Calculate percentage of completed tasks
                    return [
                        'name' => $user->first_name . " " . $user->last_name,
                        'completed' => $user->completed_tasks_count,
                        'total' => $user->total_tasks_count,
                        'percentage' => ($user->total_tasks_count > 0) ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100) : 0
                    ];
                })
                ->toArray();
            
        } else {
            // For admins, fetch the team performance data
            $this->team = User::withCount([
'taskAssignments as completed_tasks_count' => function ($query) {
                    $query->whereHas('task', function ($taskQuery) {
                        $taskQuery->where('status', 'completed');
                    });
                },
                'taskAssignments as total_tasks_count' => function ($query) {
                    $query->whereHas('task');
                }
            ])->get()->map(function ($user) {
                return [
                    'name' => $user->first_name . " " . $user->last_name,
                    'completed' => $user->completed_tasks_count,
                    'total' => $user->total_tasks_count,
                    'percentage' => ($user->total_tasks_count > 0) ? round(($user->completed_tasks_count / $user->total_tasks_count) * 100) : 0
                ];
            })->toArray();
        }

        // Fetch groups
        if ($isAdmin) {
            $this->groups = Group::withCount([
                'tasks as pending_tasks_count' => function ($query) {
                    $query->where('status', 'pending');
                },
                'tasks as inprogress_tasks_count' => function ($query) {
                    $query->where('status', 'in_progress');
                },
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'completed');
                },
                'tasks as total_tasks_count'
            ])->get()->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->label,
                    'pending' => $group->pending_tasks_count,
                    'in_progress' => $group->inprogress_tasks_count,
                    'completed' => $group->completed_tasks_count,
                    'total' => $group->total_tasks_count,
                    'percentage' => ($group->total_tasks_count > 0) ? round(($group->completed_tasks_count / $group->total_tasks_count) * 100) : 0,
                ];
            })->toArray();
        }

    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.app', [
            'title' => 'Dashboard | TMS',
        ]);
    }
}
