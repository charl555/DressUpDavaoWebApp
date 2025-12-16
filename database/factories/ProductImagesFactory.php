<?php

namespace Database\Factories;

use App\Models\ProductImages;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImagesFactory extends Factory
{
    protected $model = ProductImages::class;

    public function definition(): array
    {
        return [
            'product_id' => Products::factory(),
            'thumbnail_image' => 'product-images/thumbnails/' . $this->faker->uuid() . '.jpg',
            'images' => [
                'product-images/' . $this->faker->uuid() . '.jpg',
                'product-images/' . $this->faker->uuid() . '.jpg',
            ],
        ];
    }
}

