<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'attempts',
        'blocked_until',
        'reason'
    ];

    protected $casts = [
        'blocked_until' => 'datetime'
    ];

    public function isActive(): bool
    {
        return $this->blocked_until && $this->blocked_until->isFuture();
    }
}
