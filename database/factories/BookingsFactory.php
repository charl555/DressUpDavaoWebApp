<?php

namespace Database\Factories;

use App\Models\Bookings;
use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingsFactory extends Factory
{
    protected $model = Bookings::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'created_by' => User::factory(),
            'product_id' => Products::factory(),
            'booking_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['Pending', 'Confirmed', 'Completed', 'Cancelled']),
        ];
    }
}

