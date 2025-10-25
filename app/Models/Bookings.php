<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'created_by',
        'product_id',
        'booking_date',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
