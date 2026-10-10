<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $role
 * @method static Builder<static> query()
 * @method static Builder<static> active()
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $role
 * @property int|null $category_id
 * @property int|null $group_id
 * @method static User|null where($column, $operator = null, $value = null, $boolean = 'and')
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Task> $tasks
 * @property int $completed_tasks_count
 * @property int $total_tasks_count
 * @method static \App\Models\User findOrFail($id)
 * @method static \App\Models\User|null find($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 * @method static \Illuminate\Database\Eloquent\Builder<User> withCount(mixed $relations)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'phone_number', 'role', 'reporting_manager_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * @return BelongsToMany<Task, $this>
     */
    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_assignments');
    }

    /**
     * @return HasMany<Reminder, $this>
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * @return BelongsToMany<Task, $this>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_assignments');
    }

    /**
     * @return HasMany<TaskAssignment, $this>
     */
    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'user_id');
    }

    /**
     * @return BelongsToMany<Group, $this>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,       // Target Group Model
            'group_users',      // Pivot Table Name
            'user_id',          // Foreign Key for User on pivot
            'group_id'          // Foreign Key for Group on pivot
        );
    }

    /**
     * @param Builder<static> $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * @return HasMany<Notification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Categories created by this user
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    /**
     * User Groups where user_id matches
     */
    public function userGroups(): HasMany
    {
        return $this->hasMany(GroupUser::class, 'user_id');
    }
}
