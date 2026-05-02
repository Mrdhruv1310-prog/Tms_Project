<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    //add fillable
    public $fillable = ['label'];

    // One-to-Many: A Group has many Tasks
    public function tasks()
    {
        return $this->hasMany(Task::class, 'label_id');
    }

    // Many-to-Many: A Group has many Users
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users');
    }
}
