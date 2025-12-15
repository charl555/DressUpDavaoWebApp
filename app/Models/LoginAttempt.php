<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'attempted_at',
        'success'
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'success' => 'boolean'
    ];
}
