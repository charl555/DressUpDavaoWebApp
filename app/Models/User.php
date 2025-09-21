<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',  // Admin, SuperAdmin, User
        'bodytype',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscription()
    {
        return $this->hasOne(Subscriptions::class, 'user_id', 'id');
    }

    public function user_measurements()
    {
        return $this->hasOne(UserMeasurements::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'user_id', 'id');
    }

    public function customers()
    {
        return $this->hasMany(Customers::class, 'user_id', 'id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'Admin' || $this->role === 'SuperAdmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'SuperAdmin';
    }

    public function shop()
    {
        return $this->hasOne(Shops::class, 'user_id', 'id');
    }

    public function kiri_engine_jobs()
    {
        return $this->hasMany(KiriEngineJobs::class, 'user_id', 'id');
    }
}
