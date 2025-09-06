<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'rental_id',
        'payment_method',
        'payment_status',
        'amount_paid',
        'payment_date',
    ];

    public function rental()
    {
        return $this->belongsTo(Rentals::class, 'rental_id', 'rental_id');
    }
}
