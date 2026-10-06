<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property int $completed_tasks_count
 * @property int $total_tasks_count
 * @method static Builder<Category> query()
 * @method static Builder<Category> withCount(mixed $relations)
 * @method static \Illuminate\Database\Eloquent\Collection<int, Category> all($columns = ['*'])
 */
class Category extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // In the Category model
    public function hasTasks(): bool
    {
        return $this->tasks()->exists(); // Check if any tasks are assigned to this category
    }
}
