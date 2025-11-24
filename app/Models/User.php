<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
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
        'gender',
        'phone_number',
        'email',
        'password',
        'role',  // Admin, SuperAdmin, User
        'bodytype',
        'preferences',
        'deletion_requested_at',
        'scheduled_deletion_at',
        'deletion_reason',
    ];

    protected $casts = [
        'preferences' => 'array',
        'deletion_requested_at' => 'datetime',
        'scheduled_deletion_at' => 'datetime',
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
            'deletion_requested_at' => 'datetime',
            'scheduled_deletion_at' => 'datetime',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function stored_3d_models()
    {
        return $this->hasMany(Stored3dModels::class, 'user_id', 'id');
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

    public function rentals()
    {
        return $this->hasMany(Rentals::class, 'user_id', 'id');
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

    public function shop_reviews()
    {
        return $this->hasMany(ShopReviews::class, 'user_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'user_id', 'id');
    }

    public function shop_account_requests()
    {
        return $this->hasOne(ShopAccountRequests::class, 'user_id', 'id');
    }

    public function kiri_engine_jobs()
    {
        return $this->hasMany(KiriEngineJobs::class, 'user_id', 'id');
    }

    public function favorites()
    {
        return $this
            ->belongsToMany(Products::class, 'favorites', 'user_id', 'product_id')
            ->withTimestamps()
            ->using(Favorites::class);
    }

    public function hasFavorited(Products $product): bool
    {
        // Use direct query to avoid ambiguous column issues
        return \App\Models\Favorites::where('user_id', $this->id)
            ->where('product_id', $product->product_id)
            ->exists();
    }
}
