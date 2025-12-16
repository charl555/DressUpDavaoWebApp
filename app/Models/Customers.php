<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'address',
    ];

    public function rentals()
    {
        return $this->hasMany(Rentals::class, 'customer_id', 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
