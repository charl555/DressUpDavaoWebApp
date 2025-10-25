<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shops extends Model
{
    use HasFactory;

    protected $table = 'shops';
    protected $primaryKey = 'shop_id';

    protected $fillable = [
        'user_id',
        'shop_name',
        'shop_address',
        'shop_description',
        'shop_slug',
        'shop_logo',
        'shop_policy',
        'shop_status',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'allow_3d_model_access',
        'payment_options',
    ];

    protected $casts = [
        'payment_options' => 'array',
    ];

    public function getShopPolicyAttribute()
    {
        return $this->attributes['shop_policy'] ?? 'No specific policy provided by this shop.';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'user_id', 'user_id');
    }

    public function shop_reviews()
    {
        return $this->hasMany(ShopReviews::class, 'shop_id', 'shop_id');
    }

    public function shop_account_requests()
    {
        return $this->hasOne(ShopAccountRequests::class, 'shop_id', 'shop_id');
    }
}
