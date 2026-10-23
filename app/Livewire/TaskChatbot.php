<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class TaskChatbot extends Component
{
    public bool $isOpen = false;
    public string $userMessage = '';
    public array $messages = [];

    public function mount(): void
    {
        $this->messages = [
            [
                'role' => 'assistant',
                'content' => "Hello! I am your Enterprise TMS AI Assistant. How can I assist you today?"
            ]
        ];
    }

    public function toggleChat(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function selectOption(string $option): void
    {
        $this->userMessage = $option;
        $this->sendMessage();
    }

    public function sendMessage(): void
    {
        $trimMsg = trim($this->userMessage);
        if (empty($trimMsg)) {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'content' => e($trimMsg)
        ];

        $this->userMessage = '';

        $this->processRoleBasedReply($trimMsg);
    }

    private function processRoleBasedReply(string $input): void
    {
        $inputLower = Str::lower($input);
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if (!$authUser) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Error: Session expired. Please log in again.'
            ];
            return;
        }

        $authUserId = (int) $authUser->id;
        $role = strtolower(trim($authUser->role ?? 'employee'));

        // Role Definition Flags
        $isSuperAdmin = in_array($role, ['super-admin', 'admin'], true);
        $isManager = $role === 'manager';
        $isHr = $role === 'hr';
        $isEmployee = in_array($role, ['employee', 'user'], true);

        // RBAC Task Base Query
        $taskBaseQuery = function () use ($isSuperAdmin, $authUserId) {
            $query = Task::query()->whereHas('creator');

            if (!$isSuperAdmin) {
                $query->where(function ($q) use ($authUserId) {
                    $q->where('tasks.user_id', $authUserId)
                        ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId));
                });
            }

            return $query;
        };

        // Extract Target Status Intent
        $targetStatus = null;
        if (Str::contains($inputLower, ['pending', 'baki', 'uncompleted', 'remaining'])) {
            $targetStatus = 'pending';
        } elseif (Str::contains($inputLower, ['in_progress', 'progress', 'working'])) {
            $targetStatus = 'in_progress';
        } elseif (Str::contains($inputLower, ['completed', 'complete', 'done', 'finish'])) {
            $targetStatus = 'completed';
        }

        // -------------------------------------------------------------
        // MODULE 1: AUTH USER-WISE ALL USERS LISTING
        // -------------------------------------------------------------
        if (Str::contains($inputLower, ['give me all the user', 'give me all users', 'list of users', 'user list', 'all users', 'users list', 'show users', 'get users', 'all user'])) {
            if ($isEmployee) {
                $reply = "Sorry You Don't Have a access so please ask me another question.";
            } else {
                $userQuery = User::query();

                if ($isManager) {
                    // Manager sees HR and Employee roles
                    $userQuery->whereIn('role', ['hr', 'employee']);
                } elseif ($isHr) {
                    // HR sees Employee role only
                    $userQuery->where('role', 'employee');
                }
                // Super-Admin / Admin sees all roles

                // Order by first_name and last_name to prevent SQL unknown column 'name' errors
                $users = $userQuery->orderBy('first_name', 'asc')
                    ->orderBy('last_name', 'asc')
                    ->get();

                if ($users->isEmpty()) {
                    $reply = "No users found according to your role access levels.";
                } else {
                    $reply = "👥 <b>Accessible Users List (" . ucfirst($role) . " View):</b><br/>";
                    foreach ($users as $u) {
                        $name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
                        if (empty($name)) {
                            $name = 'User #' . $u->id;
                        }
                        $uRole = ucfirst($u->role ?? 'Employee');
                        $userTaskUrl = url('/tasks/user/pending?user_id=' . $u->id);
                        $reply .= "• <a href='{$userTaskUrl}' class='text-sky-600 underline font-semibold'>{$name}</a> (Role: {$uRole})<br/>";
                    }
                }
            }

            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            return;
        }

        // -------------------------------------------------------------
        // MODULE 2: AUTH USER-WISE CATEGORY LISTING
        // -------------------------------------------------------------
        if (Str::contains($inputLower, ['give me all the category', 'category list', 'all category', 'all categories', 'show category', 'list category', 'categories list', 'show categories'])) {
            $categoriesQuery = Category::query();

            if (!$isSuperAdmin) {
                $categoriesQuery->whereHas('tasks', function ($q) use ($authUserId) {
                    $q->where(function ($sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId));
                    });
                });
            }

            $categories = $categoriesQuery->withCount(['tasks' => function ($q) use ($isSuperAdmin, $authUserId) {
                if (!$isSuperAdmin) {
                    $q->where(function ($sub) use ($authUserId) {
                        $sub->where('tasks.user_id', $authUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $authUserId));
                    });
                }
            }])->orderBy('name')->get();

            if ($categories->isEmpty()) {
                $reply = "No accessible categories found for your account.";
            } else {
                $reply = "📂 <b>Accessible Category List (" . ucfirst($role) . " View):</b><br/>";
                foreach ($categories as $cat) {
                    $catRedirectUrl = url('/tasks?' . http_build_query(['category' => $cat->name]));
                    $reply .= "• <a href='{$catRedirectUrl}' class='text-sky-600 underline font-semibold'>{$cat->name}</a> (Total Tasks: {$cat->tasks_count})<br/>";
                }
            }

            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            return;
        }

        // -------------------------------------------------------------
        // MODULE 3: SPECIFIC USER SEARCH
        // -------------------------------------------------------------
        $allUsers = User::all();
        $matchedUser = null;

        foreach ($allUsers as $u) {
            $fullName = Str::lower(trim("{$u->first_name} {$u->last_name}"));
            $firstName = Str::lower(trim($u->first_name ?? ''));
            $lastName = Str::lower(trim($u->last_name ?? ''));

            if ((!empty($fullName) && Str::contains($inputLower, $fullName)) ||
                (!empty($firstName) && Str::contains($inputLower, $firstName)) ||
                (!empty($lastName) && Str::contains($inputLower, $lastName))
            ) {
                $matchedUser = $u;
                break;
            }
        }

        if ($matchedUser) {
            $targetUserId = (int) $matchedUser->id;
            $targetRole = strtolower($matchedUser->role ?? 'employee');

            $hasAccess = false;
            if ($isSuperAdmin || $targetUserId === $authUserId) {
                $hasAccess = true;
            } elseif ($isManager && in_array($targetRole, ['hr', 'employee'], true)) {
                $hasAccess = true;
            } elseif ($isHr && $targetRole === 'employee') {
                $hasAccess = true;
            }

            if (!$hasAccess) {
                $reply = "Sorry You Don't Have a access so please ask me another question.";
            } else {
                $userTaskQuery = Task::query()
                    ->where(function ($q) use ($targetUserId) {
                        $q->where('tasks.user_id', $targetUserId)
                            ->orWhereHas('taskAssignments', fn($aq) => $aq->where('user_id', $targetUserId));
                    })
                    ->whereHas('creator');

                if ($targetStatus) {
                    $userTaskQuery->where('status', $targetStatus);
                }

                $count = (clone $userTaskQuery)->count();
                $tasks = $userTaskQuery->latest()->limit(5)->get();

                $statusText = $targetStatus ? ucfirst(str_replace('_', ' ', $targetStatus)) : 'All';
                $displayName = trim(($matchedUser->first_name ?? '') . ' ' . ($matchedUser->last_name ?? ''));
                if (empty($displayName)) {
                    $displayName = 'User #' . $matchedUser->id;
                }
                $userRedirectUrl = url('/tasks/user/pending?' . http_build_query(['user_id' => $targetUserId, 'status' => $targetStatus ?? 'pending']));

                if ($count === 0) {
                    $reply = "User <b>{$displayName}</b> does not have any " . strtolower($statusText) . " tasks.";
                } else {
                    $reply = "👤 <b>User: {$displayName} ({$statusText} Tasks: {$count})</b> "
                        . "(<a href='{$userRedirectUrl}' class='text-sky-600 underline font-semibold'>View All Tasks</a>)<br/>";

                    foreach ($tasks as $t) {
                        $taskUrl = url('/tasks?task_id=' . $t->id);
                        $due = $t->due_date ? $t->due_date->format('d-m-Y g:i A') : 'No Due Date';
                        $stBadge = ucfirst(str_replace('_', ' ', $t->status));
                        $reply .= "• <a href='{$taskUrl}' class='text-sky-600 underline font-bold'>{$t->title}</a> (Status: {$stBadge} | Due: {$due})<br/>";
                    }
                }
            }

            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            return;
        }

        // -------------------------------------------------------------
        // MODULE 4: SPECIFIC CATEGORY SEARCH
        // -------------------------------------------------------------
        $allCategories = Category::all();
        $matchedCategory = null;

        foreach ($allCategories as $cat) {
            $catNameLower = Str::lower($cat->name);
            if (Str::contains($inputLower, $catNameLower)) {
                $matchedCategory = $cat;
                break;
            }
        }

        if ($matchedCategory) {
            $catQuery = $taskBaseQuery()->where('category_id', $matchedCategory->id);
            if ($targetStatus) {
                $catQuery->where('status', $targetStatus);
            }

            $count = (clone $catQuery)->count();
            $tasks = $catQuery->latest()->limit(5)->get();
            $statusText = $targetStatus ? ucfirst(str_replace('_', ' ', $targetStatus)) : 'All';
            $categoryRedirectUrl = url('/tasks?' . http_build_query(['category' => $matchedCategory->name, 'status' => $targetStatus ?? 'total']));

            if ($count === 0) {
                $reply = "No " . strtolower($statusText) . " tasks found under Category <b>{$matchedCategory->name}</b>.";
            } else {
                $reply = "📂 <b>Category: {$matchedCategory->name} ({$statusText} Tasks: {$count})</b> "
                    . "(<a href='{$categoryRedirectUrl}' class='text-sky-600 underline font-semibold'>View List</a>)<br/>";

                foreach ($tasks as $t) {
                    $taskUrl = url('/tasks?task_id=' . $t->id);
                    $due = $t->due_date ? $t->due_date->format('d-m-Y g:i A') : 'No Due Date';
                    $stBadge = ucfirst(str_replace('_', ' ', $t->status));
                    $reply .= "• <a href='{$taskUrl}' class='text-sky-600 underline font-bold'>{$t->title}</a> (Status: {$stBadge} | Due: {$due})<br/>";
                }
            }

            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            return;
        }

        // -------------------------------------------------------------
        // MODULE 5: SPECIFIC GROUP SEARCH
        // -------------------------------------------------------------
        $allGroups = Group::all();
        $matchedGroup = null;

        foreach ($allGroups as $grp) {
            $grpNameLower = Str::lower($grp->label ?? $grp->name ?? '');
            if (!empty($grpNameLower) && Str::contains($inputLower, $grpNameLower)) {
                $matchedGroup = $grp;
                break;
            }
        }

        if ($matchedGroup) {
            $groupLabel = $matchedGroup->label ?? $matchedGroup->name;
            $grpQuery = $taskBaseQuery()->where(function ($q) use ($matchedGroup, $groupLabel) {
                $q->where('label_id', $matchedGroup->id)
                    ->orWhere('label_id', (string)$matchedGroup->id)
                    ->orWhere('label_id', $groupLabel);
            });

            if ($targetStatus) {
                $grpQuery->where('status', $targetStatus);
            }

            $count = (clone $grpQuery)->count();
            $tasks = $grpQuery->latest()->limit(5)->get();
            $statusText = $targetStatus ? ucfirst(str_replace('_', ' ', $targetStatus)) : 'All';
            $groupRedirectUrl = url('/tasks?' . http_build_query(['group' => $groupLabel, 'status' => $targetStatus ?? 'total']));

            if ($count === 0) {
                $reply = "No " . strtolower($statusText) . " tasks found under Group <b>{$groupLabel}</b>.";
            } else {
                $reply = "👥 <b>Group: {$groupLabel} ({$statusText} Tasks: {$count})</b> "
                    . "(<a href='{$groupRedirectUrl}' class='text-sky-600 underline font-semibold'>View List</a>)<br/>";

                foreach ($tasks as $t) {
                    $taskUrl = url('/tasks?task_id=' . $t->id);
                    $stBadge = ucfirst(str_replace('_', ' ', $t->status));
                    $reply .= "• <a href='{$taskUrl}' class='text-sky-600 underline font-bold'>{$t->title}</a> (Status: {$stBadge})<br/>";
                }
            }

            $this->messages[] = ['role' => 'assistant', 'content' => $reply];
            return;
        }

        // -------------------------------------------------------------
        // MODULE 6: GENERAL TASK STATUS INTENT
        // -------------------------------------------------------------
        if ($targetStatus === 'pending' || Str::contains($inputLower, ['pending task', 'pending'])) {
            $count = $taskBaseQuery()->where('status', 'pending')->count();
            $tasks = $taskBaseQuery()->where('status', 'pending')->latest()->limit(5)->get();

            if ($count === 0) {
                $reply = "You do not have any pending tasks!";
            } else {
                $pendingUrl = url('/tasks/pending');
                $reply = "📌 <b>Total Pending Tasks ({$count}):</b> (<a href='{$pendingUrl}' class='text-sky-600 underline font-semibold'>View All Pending</a>)<br/>";
                foreach ($tasks as $t) {
                    $due = $t->due_date ? $t->due_date->format('d-m-Y g:i A') : 'No Due Date';
                    $taskUrl = url('/tasks?task_id=' . $t->id);
                    $reply .= "• <a href='{$taskUrl}' class='text-sky-600 underline font-bold'>{$t->title}</a> (Due: {$due})<br/>";
                }
            }
        } elseif ($targetStatus === 'in_progress' || Str::contains($inputLower, ['in progress task', 'in progress'])) {
            $count = $taskBaseQuery()->where('status', 'in_progress')->count();
            $tasks = $taskBaseQuery()->where('status', 'in_progress')->latest()->limit(5)->get();

            if ($count === 0) {
                $reply = "No tasks are currently 'In Progress'.";
            } else {
                $progressUrl = url('/tasks/in_progress');
                $reply = "🔄 <b>In Progress Tasks ({$count}):</b> (<a href='{$progressUrl}' class='text-sky-600 underline font-semibold'>View All In Progress</a>)<br/>";
                foreach ($tasks as $t) {
                    $taskUrl = url('/tasks?task_id=' . $t->id);
                    $reply .= "• <a href='{$taskUrl}' class='text-sky-600 underline font-bold'>{$t->title}</a><br/>";
                }
            }
        } elseif ($targetStatus === 'completed' || Str::contains($inputLower, ['complete task', 'completed'])) {
            $count = $taskBaseQuery()->where('status', 'completed')->count();
            $tasks = $taskBaseQuery()->where('status', 'completed')->latest()->limit(5)->get();

            if ($count === 0) {
                $reply = "No tasks have been completed yet.";
            } else {
                $completedUrl = url('/tasks/completed');
                $reply = "✅ <b>Completed Tasks ({$count}):</b> (<a href='{$completedUrl}' class='text-sky-600 underline font-semibold'>View All Completed</a>)<br/>";
                foreach ($tasks as $t) {
                    $taskUrl = url('/tasks?task_id=' . $t->id);
                    $reply .= "• <a href='{$taskUrl}' class='text-sky-600 underline font-bold'>{$t->title}</a><br/>";
                }
            }
        } elseif (Str::contains($inputLower, ['total task', 'total', 'summary', 'overview'])) {
            $total = $taskBaseQuery()->count();
            $pending = $taskBaseQuery()->where('status', 'pending')->count();
            $inProgress = $taskBaseQuery()->where('status', 'in_progress')->count();
            $completed = $taskBaseQuery()->where('status', 'completed')->count();

            $allTasksUrl = url('/tasks');
            $roleLabel = ucfirst($role);

            $reply = "📊 <b>Overall Task Overview ({$roleLabel} View):</b><br/>"
                . "• Total Tasks: <b>{$total}</b> (<a href='{$allTasksUrl}' class='text-sky-600 underline font-semibold'>Open List</a>)<br/>"
                . "• Pending Tasks: <b>{$pending}</b><br/>"
                . "• In Progress: <b>{$inProgress}</b><br/>"
                . "• Completed: <b>{$completed}</b>";
        } elseif (Str::contains($inputLower, ['hi', 'hello', 'hey'])) {
            $reply = "Hello {$authUser->first_name}! You are logged in as <b>" . ucfirst($role) . "</b>. Ask me anything regarding Tasks, Categories, Users, or Groups.";
        } else {
            // DEFAULT FALLBACK MESSAGE
            $reply = "Sorry, I can Understand Your Question. Please ask another question.";
        }

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $reply
        ];
    }

    public function render()
    {
        return view('livewire.task-chatbot');
    }
}
