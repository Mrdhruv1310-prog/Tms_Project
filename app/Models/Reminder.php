<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property mixed $reminder_time
 * @property string $reminder_unit
 * @property int $reminder_value
 * @method static Builder<Reminder> query()
 * @method static Reminder|null where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Reminder create(array<string, mixed> $attributes)
 * @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>>
 * @property-read User|null $recipient
 * @method static \Illuminate\Database\Eloquent\Builder<Reminder> query()
 */
class Reminder extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'reminder_time',
        'reminder_unit',
        'reminder_value',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function recipient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
