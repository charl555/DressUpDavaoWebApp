<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMeasurements extends Model
{
    use HasFactory;

    protected $table = 'product_measurements';
    protected $primaryKey = 'product_measurements_id';

    protected $fillable = [
        'product_id',
        'gown_upper_chest',
        'gown_bust',
        'gown_chest',
        'gown_shoulder',
        'gown_waist',
        'gown_hips',
        'gown_back_width',
        'gown_figure',
        'gown_arm_hole',
        'gown_neck',
        'gown_bust_point',
        'gown_bust_distance',
        'gown_sleeve_width',
        'gown_length',
        'jacket_chest',
        'jacket_bust',
        'jacket_shoulder',
        'jacket_sleeve_length',
        'jacket_sleeve_width',
        'jacket_length',
        'jacket_bicep',
        'jacket_arm_hole',
        'jacket_waist',
        'jacket_hip',
        'jacket_back_width',
        'jacket_figure',
        'trouser_waist',
        'trouser_hip',
        'trouser_inseam',
        'trouser_outseam',
        'trouser_thigh',
        'trouser_knee',
        'trouser_bottom',
        'trouser_leg_opening',
        'trouser_crotch',
        'trouser_length',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'product_id');
    }
}
