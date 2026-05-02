<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = ['group_id', 'user_id'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
