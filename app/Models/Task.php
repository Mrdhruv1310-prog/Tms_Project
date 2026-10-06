<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int|null $category_id
 * @property string|null $priority
 * @property int|null $label_id
 * @property string|null $recurrence
 * @property string|null $recurrence_end_date
 * @property int|null $reminderTime
 * @property string|null $reminderUnit
 * @property string|null $due_date
 * @property string $status
 * @property int $user_id
 * @property \App\Models\Category|null $category
 * @property \App\Models\Group|null $group
 * @property \App\Models\User|null $creator
 * @property int|null $parent_task_id
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $assignedUsers
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskAssignment> $taskAssignments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read Category|null $category
 * @property-read User|null $assignedBy
 * @method static Task findOrFail($id, $columns = ['*'])
 * @method static Task create(array<string, mixed> $attributes)
 * @method static Task|null find($id)
 * @property bool $enableRepeatTask
 */
class Task extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'title',
        'description',
        'category_id',
        'priority',
        'label_id',
        'recurrence',
        'recurrence_end_date',
        'reminderTime',
        'reminderUnit',
        'due_date',
        'status',
        'user_id',
        'parent_task_id',
    ];

    /**
     * @return HasMany<TaskUpdate, $this>
     */
    public function updates(): HasMany
    {
        return $this->hasMany(TaskUpdate::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function assignedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Reminder, $this>
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * @return HasMany<TaskAssignment, $this>
     */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'task_id');
    }

    /**
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'label_id', 'id');
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }
}
