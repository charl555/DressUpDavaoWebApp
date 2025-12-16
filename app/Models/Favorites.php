<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Favorites extends Pivot
{
    use HasFactory;

    protected $table = 'favorites';
    protected $primaryKey = 'favorite_id';

    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
