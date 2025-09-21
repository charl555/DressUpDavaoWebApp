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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'user_id', 'user_id');
    }
}
