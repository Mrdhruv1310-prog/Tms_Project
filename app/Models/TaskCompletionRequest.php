<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCompletionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'request_status',
        'requested_at',
        'reviewed_at',
        'reviewed_by',
        'review_comment',
    ];

    //timestamp false
    public $timestamps = false;

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

}
