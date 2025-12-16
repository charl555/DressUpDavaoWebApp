<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopReviews extends Model
{
    use HasFactory;

    protected $table = 'shop_reviews';
    protected $primaryKey = 'shop_review_id';

    protected $fillable = [
        'user_id',
        'shop_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shop()
    {
        return $this->belongsTo(Shops::class, 'shop_id', 'shop_id');
    }
}
