<?php

namespace Database\Factories;

use App\Models\Favorites;
use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoritesFactory extends Factory
{
    protected $model = Favorites::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Products::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

