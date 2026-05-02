<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', // Add this line
        'user_id',
        'reminder_time',
        'reminder_unit',
        'reminder_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
