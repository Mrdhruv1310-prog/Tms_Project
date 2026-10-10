<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Category Report | TMS')]
class CategoryReport extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $categories = [];

    public function mount(): void
    {
        // Ensure user is authenticated
        /** @var User|null $loggedInUser */
        $loggedInUser = Auth::user();
        if (!$loggedInUser) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch categories filtered by role (admin sees all, hr/manager see only their own)
        $this->categories = Category::forCurrentUser()
            ->with([
                'creator',
                'tasks' => function ($query) {
                    $query->select('id', 'category_id', 'status');
                },
                'tasks.taskAssignments' => function ($query) {
                    $query->select('id', 'task_id', 'user_id');
                }
            ])
            ->get()
            ->map(function ($category) use ($loggedInUser) {
                /** @var Category $category */

                // Filter tasks based on user role
                if (in_array($loggedInUser->role, ['admin', 'hr', 'manager'], true)) {
                    $userTasks = $category->tasks;
                } else {
                    // Non-admin/hr/manager: Only count tasks assigned to the logged-in user
                    $userTasks = $category->tasks->filter(function ($task) use ($loggedInUser) {
                        return $task->taskAssignments->contains('user_id', $loggedInUser->id);
                    });
                }

                $totalTasks = $userTasks->count();
                $pendingTasks = $userTasks->where('status', 'pending')->count();
                $inProgressTasks = $userTasks->where('status', 'in_progress')->count();
                $completedTasks = $userTasks->where('status', 'completed')->count();

                // Calculate percentages safely
                $pendingPercentage = $totalTasks > 0 ? ($pendingTasks / $totalTasks) * 100 : 0;
                $inProgressPercentage = $totalTasks > 0 ? ($inProgressTasks / $totalTasks) * 100 : 0;
                $completedPercentage = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

                // Return structured category report data
                return [
                    'title' => (string) $category->name,
                    'creator_name' => $category->creator ? ($category->creator->first_name ?? $category->creator->name) : 'N/A',
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

    public function render(): mixed
    {
        return view('livewire.category-report');
    }
}
