<?php

namespace Database\Factories;

use App\Models\Shops;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopsFactory extends Factory
{
    protected $model = Shops::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->tailor(),
            'shop_name' => $this->faker->company(),
            'shop_slug' => $this->faker->slug(),
            'shop_description' => $this->faker->paragraph(),
            'shop_address' => $this->faker->address(),
            'shop_logo' => null,
            'shop_policy' => $this->faker->paragraph(),
            'shop_status' => 'Verified',
            'facebook_url' => null,
            'instagram_url' => null,
            'tiktok_url' => null,
            'payment_options' => null,
            'allow_3d_model_access' => false,
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'shop_status' => 'Pending',
            ];
        });
    }

    public function suspended()
    {
        return $this->state(function (array $attributes) {
            return [
                'shop_status' => 'Suspended',
            ];
        });
    }
}
