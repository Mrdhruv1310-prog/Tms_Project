<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUpdate extends Model
{
    use HasFactory;

    protected $table = 'task_updates';

    //add fillable
    public $fillable = ['task_id', 'user_id', 'status', 'comment'];
    
    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
