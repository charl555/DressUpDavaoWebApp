<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occasions extends Model
{
    protected $table = 'occasions';
    protected $primaryKey = 'occasions_id';

    protected $fillable = [
        'product_id',
        'occasion_name',
    ];

    protected $casts = [
        'occasion_name' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
