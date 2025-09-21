<?php

namespace Database\Factories;

use App\Models\Shops;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shops>
 */
class ShopsFactory extends Factory
{
    protected $model = Shops::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shop_name' => $this->faker->company() . ' Boutique',
            'shop_address' => $this->faker->address(),
            'shop_description' => $this->faker->paragraph(),
            'shop_slug' => $this->faker->slug(),
            'shop_logo' => null,
            'shop_policy' => $this->faker->paragraph(),
        ];
    }

    /**
     * Set a specific user for the shop.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
