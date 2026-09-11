<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CategoryReport extends Component
{
    public $categories = [];

    public function mount()
    {
        // Ensure user is authenticated
        $loggedInUser = Auth::user();
        if (!$loggedInUser) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch categories with tasks AND eager-load taskAssignments to prevent N+1 query issues
        $this->categories = Category::with([
            'tasks' => function ($query) {
                $query->select('id', 'category_id', 'status');
            },
            'tasks.taskAssignments' => function ($query) {
                $query->select('id', 'task_id', 'user_id');
            }
        ])
            ->get()
            ->map(function ($category) use ($loggedInUser) {
                // Filter tasks based on user role
                if ($loggedInUser->role === 'admin' || $loggedInUser->role === 'super-admin') {
                    $userTasks = $category->tasks;
                } else {
                    // Non-admin: Only count tasks assigned to the logged-in user
                    $userTasks = $category->tasks->filter(function ($task) use ($loggedInUser) {
                        return $task->taskAssignments->contains('user_id', $loggedInUser->id);
                    });
                }

                $totalTasks = $userTasks->count();
                $pendingTasks = $userTasks->where('status', 'pending')->count();
                $inProgressTasks = $userTasks->where('status', 'in_progress')->count();
                $completedTasks = $userTasks->where('status', 'completed')->count();

                // Calculate percentages safely (avoiding division by zero)
                $pendingPercentage = $totalTasks > 0 ? ($pendingTasks / $totalTasks) * 100 : 0;
                $inProgressPercentage = $totalTasks > 0 ? ($inProgressTasks / $totalTasks) * 100 : 0;
                $completedPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

                // Return structured category report data
                return [
                    'title' => $category->name,
                    'pending' => [
                        'completed' => $pendingTasks,
                        'total' => $totalTasks,
                        'percentage' => $pendingPercentage,
                    ],
                    'in_progress' => [
                        'completed' => $inProgressTasks,
                        'total' => $totalTasks,
                        'percentage' => $inProgressPercentage,
                    ],
                    'completed' => [
                        'completed' => $completedTasks,
                        'total' => $totalTasks,
                        'percentage' => $completedPercentage,
                    ],
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.category-report')->layout('components.layouts.app', [
            'title' => 'Category Report | TMS',
        ]);
    }
}
