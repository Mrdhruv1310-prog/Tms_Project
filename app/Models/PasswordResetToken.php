<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    // Disable timestamps if you don't have `updated_at` column
    public $timestamps = false;
}
