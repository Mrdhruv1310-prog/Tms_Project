<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // In the Category model
    public function hasTasks()
    {
        return $this->tasks()->exists(); // Check if any tasks are assigned to this category
    }

}