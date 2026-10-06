<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static Builder<PasswordResetToken> query()
 * @method static Builder<PasswordResetToken> where($column,$operator = null, $value = null,$boolean = 'and')
 * @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>>
 */
class PasswordResetToken extends Model
{
    /** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>> */
    use HasFactory;

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];
    protected $table = 'password_reset_tokens';
    // Disable timestamps if you don't have `updated_at` column
    public $timestamps = false;
}
