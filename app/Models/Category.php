<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $name
 * @property int $created_by
 * @property int $completed_tasks_count
 * @property int $total_tasks_count
 * @method static Builder<Category> query()
 * @method static Builder<Category> withCount(mixed $relations)
 * @method static \Illuminate\Database\Eloquent\Collection<int, Category> all($columns = ['*'])
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
    ];

    /**
     * Relationship with User (Creator)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with Tasks
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function hasTasks(): bool
    {
        return $this->tasks()->exists();
    }

    /**
     * Scope to filter categories based on logged-in user role:
     * - 'admin': sees all categories
     * - 'hr' / 'manager': sees only categories created by them
     */
    public function scopeForCurrentUser(Builder $query): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === 'admin') {
            return $query;
        }

        return $query->where('created_by', $user->id);
    }
}
