<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occasions extends Model
{
    use HasFactory;

    protected $table = 'occasions';
    protected $primaryKey = 'occasion_id';

    protected $fillable = [
        'product_id',
        'occasion_name',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
