<?php

namespace Database\Factories;

use App\Models\ProductMeasurements;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductMeasurementsFactory extends Factory
{
    protected $model = ProductMeasurements::class;

    public function definition(): array
    {
        return [
            'product_id' => Products::factory(),
            // Gown measurements
            'gown_chest' => $this->faker->optional()->numberBetween(30, 50),
            'gown_waist' => $this->faker->optional()->numberBetween(24, 40),
            'gown_hips' => $this->faker->optional()->numberBetween(32, 52),
            'gown_shoulder' => $this->faker->optional()->numberBetween(14, 20),
            'gown_length' => $this->faker->optional()->numberBetween(40, 70),
            'gown_bust' => $this->faker->optional()->numberBetween(30, 50),
            // Jacket measurements
            'jacket_chest' => $this->faker->optional()->numberBetween(34, 50),
            'jacket_waist' => $this->faker->optional()->numberBetween(28, 44),
            'jacket_shoulder' => $this->faker->optional()->numberBetween(16, 22),
            // Trouser measurements
            'trouser_waist' => $this->faker->optional()->numberBetween(28, 44),
            'trouser_hip' => $this->faker->optional()->numberBetween(34, 52),
            'trouser_inseam' => $this->faker->optional()->numberBetween(28, 36),
        ];
    }
}

