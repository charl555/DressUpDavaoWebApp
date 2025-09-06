<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product3dModels extends Model
{
    protected $table = 'product_3d_models';
    protected $primaryKey = 'product_3d_model_id';

    protected $fillable = [
        'product_id',
        'model_path',
        'clipping_planes_data',
    ];

    protected $casts = [
        'clipping_planes_data' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
