<?php

namespace Database\Factories;

use App\Models\ShopReviews;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopReviewFactory extends Factory
{
    protected $model = ShopReviews::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => Shops::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
        ];
    }
}
