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
        // Fetch categories and their associated tasks grouped by status
        $this->categories = Category::with([
            'tasks' => function ($query) {
                $query->select('id', 'category_id', 'status');
            },
        ])
            ->get()
            ->map(function ($category) {
                // Total tasks per status
                $totalTasks = $category->tasks->count();

                // Get the currently authenticated user
                $loggedInUser = Auth::user();

                // Check if the user is an admin
                if ($loggedInUser->role === 'admin') {
                    // Admin: Count all tasks
                    $pendingTasks = $category->tasks->where('status', 'pending')->count();
                    $inProgressTasks = $category->tasks->where('status', 'in_progress')->count();
                    $completedTasks = $category->tasks->where('status', 'completed')->count();
                } else {
                    // Non-admin: Only count tasks assigned to the logged-in user
                    $userTasks = $category->tasks->filter(function ($task) use ($loggedInUser) {
                        return $task->taskAssignments->contains('user_id', $loggedInUser->id);
                    });

                    $totalTasks = $userTasks->count();
                    $pendingTasks = $userTasks->where('status', 'pending')->count();
                    $inProgressTasks = $userTasks->where('status', 'in_progress')->count();
                    $completedTasks = $userTasks->where('status', 'completed')->count();
                }

                // Calculate percentages
                $pendingPercentage = $totalTasks ? ($pendingTasks / $totalTasks) * 100 : 0;
                $inProgressPercentage = $totalTasks ? ($inProgressTasks / $totalTasks) * 100 : 0;
                $completedPercentage = $totalTasks ? ($completedTasks / $totalTasks) * 100 : 0;

                // Return category data along with task breakdown
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
