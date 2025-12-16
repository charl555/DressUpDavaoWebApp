<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserMeasurements;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserMeasurementsFactory extends Factory
{
    protected $model = UserMeasurements::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'chest' => $this->faker->numberBetween(30, 50),
            'waist' => $this->faker->numberBetween(25, 40),
            'hips' => $this->faker->numberBetween(30, 50),
            'shoulder' => $this->faker->numberBetween(14, 20),
        ];
    }
}

