<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other', 'Prefer not to say']),
            'phone_number' => fake()->numerify('09#########'),  // Philippine phone format
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'User',
            'bodytype' => fake()->optional(0.7)->randomElement(['Slim', 'Average', 'Athletic', 'Plus Size']),  // 70% chance to have a bodytype
            'preferences' => [
                'color' => fake()->optional()->randomElement(['red', 'blue', 'green', 'black', 'white', 'purple', 'pink', 'neutral', 'earth', 'bright']),
                'occasion' => fake()->optional()->randomElement(['formal', 'casual', 'business', 'party', 'wedding', 'everyday', 'gala', 'prom']),
                'fabric' => fake()->optional()->randomElement(['cotton', 'silk', 'linen', 'wool', 'polyester', 'velvet', 'satin', 'chiffon', 'denim', 'lace']),
            ],
            'deletion_requested_at' => null,
            'scheduled_deletion_at' => null,
            'deletion_reason' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'Admin',
        ]);
    }

    /**
     * Create a super admin user.
     */
    public function superAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'SuperAdmin',
        ]);
    }

    /**
     * Create a tailor/shop owner user.
     */
    public function tailor(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'Tailor',
        ]);
    }

    /**
     * Create a user with scheduled deletion.
     */
    public function scheduledForDeletion(): static
    {
        return $this->state(fn(array $attributes) => [
            'deletion_requested_at' => now(),
            'scheduled_deletion_at' => now()->addDays(30),
            'deletion_reason' => 'User requested account deletion',
        ]);
    }

    /**
     * Create a user with specific gender.
     */
    public function male(): static
    {
        return $this->state(fn(array $attributes) => [
            'gender' => 'Male',
        ]);
    }

    public function female(): static
    {
        return $this->state(fn(array $attributes) => [
            'gender' => 'Female',
        ]);
    }

    /**
     * Create a user with specific bodytype.
     */
    public function withBodyType(string $bodytype): static
    {
        return $this->state(fn(array $attributes) => [
            'bodytype' => $bodytype,
        ]);
    }

    /**
     * Create a user with specific preferences.
     */
    public function withPreferences(array $preferences): static
    {
        return $this->state(fn(array $attributes) => [
            'preferences' => array_merge($attributes['preferences'] ?? [], $preferences),
        ]);
    }

    /**
     * Create a user with a profile picture.
     */
    public function withProfilePicture(): static
    {
        return $this->state(fn(array $attributes) => [
            'profile_picture' => 'profile-pictures/' . fake()->uuid() . '.jpg',
        ]);
    }

    /**
     * Create a user with bio.
     */
    public function withBio(): static
    {
        return $this->state(fn(array $attributes) => [
            'bio' => fake()->paragraph(),
        ]);
    }
}
