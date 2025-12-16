<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    protected $primaryKey = 'subscription_id';

    protected $fillable = [
        'user_id',
        'subscription_type',
        'subscription_status',
        'product_limit',
        'start_date',
        'end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
