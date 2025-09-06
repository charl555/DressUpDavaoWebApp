<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMeasurements extends Model
{
    protected $table = 'product_measurements';
    protected $primaryKey = 'product_measurements_id';

    protected $fillable = [
        'product_id',
        'gown_length',
        'gown_upper_chest',
        'gown_chest',
        'gown_waist',
        'gown_hips',
        'jacket_chest',
        'jacket_length',
        'jacket_shoulder',
        'jacket_sleeve_length',
        'jacket_sleeve_width',
        'jacket_bicep',
        'jacket_arm_hole',
        'jacket_waist',
        'trouser_waist',
        'trouser_hip',
        'trouser_inseam',
        'trouser_outseam',
        'trouser_thigh',
        'trouser_leg_opening',
        'trouser_crotch',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
