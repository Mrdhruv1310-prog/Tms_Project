<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

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
        'due_date',
        'status',
        'user_id',
        'parent_task_id',
    ];

    public function updates()
    {
        return $this->hasMany(TaskUpdate::class);
    }

    // Relationship with User model through task_assignments
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'task_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'label_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function children()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }
}
