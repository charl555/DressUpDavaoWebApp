<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductEvents extends Model
{
    use HasFactory;

    protected $table = 'product_events';
    protected $primaryKey = 'product_event_id';

    protected $fillable = [
        'product_id',
        'event_name',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
