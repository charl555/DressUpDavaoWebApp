<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'subtype',
        'description',
        'inclusions',
        'status',
        'colors',
        'size',
        'rental_price',
        'rental_count',
        'maintenance_needed',
        'visibility',
    ];

    public function occasions()
    {
        return $this->hasMany(Occasions::class, 'product_id', 'product_id');
    }

    public function product_measurements()
    {
        return $this->hasOne(ProductMeasurements::class, 'product_id', 'product_id');
    }

    public function product_images()
    {
        return $this->hasMany(ProductImages::class, 'product_id', 'product_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rentals::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product_3d_models()
    {
        return $this->hasOne(Product3dModels::class, 'product_id', 'product_id');
    }
}
