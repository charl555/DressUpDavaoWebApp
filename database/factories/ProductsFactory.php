<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products>
 */
class ProductsFactory extends Factory
{
    protected $model = Products::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Gown', 'Suit'];
        $type = $this->faker->randomElement($types);
        
        $gownSubtypes = ['Ball Gown', 'Wedding Gown', 'Prom Dress', 'Evening Gown', 'Cocktail Dress'];
        $suitSubtypes = ['Tuxedo', 'Business Suit', 'Formal Suit', 'Three-piece Suit'];
        
        $subtype = $type === 'Gown' 
            ? $this->faker->randomElement($gownSubtypes)
            : $this->faker->randomElement($suitSubtypes);

        $sizes = ['Extra Small', 'Small', 'Medium', 'Large', 'Extra Large', 'Extra Extra Large'];
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Purple', 'Pink', 'Gold'];
        $statuses = ['Available', 'Rented', 'Reserved', 'Maintenance'];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(3, true) . ' ' . $type,
            'type' => $type,
            'subtype' => $subtype,
            'description' => $this->faker->paragraph(),
            'inclusions' => $this->faker->sentence(),
            'status' => $this->faker->randomElement($statuses),
            'colors' => $this->faker->randomElement($colors),
            'size' => $this->faker->randomElement($sizes),
            'rental_price' => $this->faker->numberBetween(1000, 10000),
            'rental_count' => $this->faker->numberBetween(0, 50),
            'maintenance_needed' => $this->faker->randomElement(['Yes', 'No']),
            'visibility' => $this->faker->randomElement(['Yes', 'No']),
        ];
    }

    /**
     * Indicate that the product should be visible.
     */
    public function visible(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'Yes',
        ]);
    }

    /**
     * Indicate that the product should be available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Available',
        ]);
    }

    /**
     * Set a specific size for the product.
     */
    public function size(string $size): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $size,
        ]);
    }

    /**
     * Set a specific type for the product.
     */
    public function type(string $type): static
    {
        $gownSubtypes = ['Ball Gown', 'Wedding Gown', 'Prom Dress', 'Evening Gown', 'Cocktail Dress'];
        $suitSubtypes = ['Tuxedo', 'Business Suit', 'Formal Suit', 'Three-piece Suit'];
        
        $subtype = $type === 'Gown' 
            ? $this->faker->randomElement($gownSubtypes)
            : $this->faker->randomElement($suitSubtypes);

        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'subtype' => $subtype,
        ]);
    }
}
