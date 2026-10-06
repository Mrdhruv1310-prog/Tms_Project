<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $label
 * @property string|null $name
 * @property int $pending_tasks_count
 * @property int $inprogress_tasks_count
 * @property int $completed_tasks_count
 * @property int $total_tasks_count
 * @property \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @method static \Illuminate\Database\Eloquent\Builder<Group> query()
 * @method static \Illuminate\Database\Eloquent\Builder<Group> where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Group create(array<string, mixed> $attributes)
 * @method static Group findOrFail($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Collection<int, static> all($columns = ['*'])
 * @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>>
 * @method static \Illuminate\Database\Eloquent\Builder<Group> withCount(mixed $relations)
 */
class Group extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>> */
    use HasFactory;

    //add fillable
    public $fillable = ['label'];

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'label_id');
    }

    // Many-to-Many: A Group has many Users
    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        // Explicitly 'group_users' table pass kiya gaya hai taaki 'group_user' missing error khatam ho jaye[cite: 18]
        return $this->belongsToMany(User::class, 'group_users', 'group_id', 'user_id');
    }
}
