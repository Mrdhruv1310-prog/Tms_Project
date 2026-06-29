<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users'); // Specify pivot table name
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
