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
        'fabric',
        'size',
        'rental_price',
        'rental_count',
        'maintenance_needed',
        'visibility',
    ];

    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'product_id', 'product_id')->orderBy('booking_date', 'desc');
    }

    public function kiriEngineJobs()
    {
        return $this->hasMany(KiriEngineJobs::class, 'product_id', 'product_id');
    }

    public function occasions()
    {
        return $this->hasMany(Occasions::class, 'product_id', 'product_id');
    }

    public function events()
    {
        return $this->hasMany(ProductEvents::class, 'product_id', 'product_id');
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

    // Change this to belongsToMany for many-to-many relationship
    public function favoritedBy()
    {
        return $this
            ->belongsToMany(User::class, 'favorites', 'product_id', 'user_id')
            ->withTimestamps()
            ->using(Favorites::class);
    }

    public function getIsFavoritedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        // Use direct query to avoid ambiguous column issues
        return \App\Models\Favorites::where('user_id', auth()->id())
            ->where('product_id', $this->product_id)
            ->exists();
    }

    public function getFavoritesCountAttribute(): int
    {
        // Use direct query to avoid ambiguous column issues
        return \App\Models\Favorites::where('product_id', $this->product_id)->count();
    }
}
