<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rentals extends Model
{
    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';

    protected $fillable = [
        'product_id',
        'customer_id',
        'pickup_date',
        'event_date',
        'return_date',
        'rental_status',
        'rental_price',
        'actual_return_date',
        'penalty_amount',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'rental_id', 'rental_id');
    }
}
