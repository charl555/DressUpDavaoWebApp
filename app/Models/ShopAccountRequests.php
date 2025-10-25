<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopAccountRequests extends Model
{
    protected $table = 'shop_account_requests';
    protected $primaryKey = 'shop_account_request_id';

    protected $fillable = [
        'user_id',
        'shop_id',
        'document_url',
        'document_type',
        'status',
        'rejection_reason',
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
