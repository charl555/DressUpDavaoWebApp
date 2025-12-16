<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\UserMeasurements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $turnstileShouldFail = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Set the cloudflare secret key config so the validation proceeds
        config(['services.cloudflare.secret_key' => 'test-secret-key']);

        // Mock Cloudflare Turnstile response with a callback that checks the test flag
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => function () {
                if ($this->turnstileShouldFail) {
                    return Http::response([
                        'success' => false,
                        'error-codes' => ['invalid-input-response']
                    ], 200);
                }
                return Http::response([
                    'success' => true,
                    'hostname' => 'dressupdavao.shop'
                ], 200);
            }
        ]);
    }

    /**
     * @test
     */
    public function it_can_register_new_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'gender' => 'Male',
            'phone_number' => '09171234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'color_preference' => 'blue',
            'occasion_preference' => 'formal',
            'fabric_preference' => 'cotton',
            'chest' => 40,
            'waist' => 32,
            'hips' => 38,
            'shoulder' => 18
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Registration successful! Welcome to DressUp Davao!'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'gender' => 'Male'
        ]);

        // Check if measurements were created
        $user = User::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('user_measurements', [
            'user_id' => $user->id,
            'chest' => 40
        ]);
    }

    /**
     * @test
     */
    public function it_validates_registration_data()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'name' => '',
            'email' => 'existing@example.com',  // Duplicate
            'gender' => 'Invalid',
            'phone_number' => '123',  // Too short
            'password' => '123',  // Too short
            'password_confirmation' => '456'  // Doesn't match
        ];

        $response = $this->postJson('/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name', 'email', 'gender', 'phone_number', 'password'
        ]);
    }

    /**
     * @test
     */
    public function it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'User'
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'cf-turnstile-response' => 'valid-token'
        ];

        $response = $this->postJson('/login', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Login successful! Welcome back!'
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     */
    public function it_rejects_admin_login_on_user_login_page()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Admin'
        ]);

        $data = [
            'email' => 'admin@example.com',
            'password' => 'password123',
            'cf-turnstile-response' => 'valid-token'
        ];

        $response = $this->postJson('/login', $data);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Admin accounts must use the admin login page.'
        ]);
    }

    /**
     * @test
     */
    public function it_blocks_login_after_multiple_failed_attempts()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword')
        ]);

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
                'cf-turnstile-response' => 'valid-token'
            ]);
        }

        // 6th attempt should be blocked
        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'cf-turnstile-response' => 'valid-token'
        ]);

        $response->assertStatus(429);  // Too Many Requests
        $response->assertJson(['blocked' => true]);
    }

    /**
     * @test
     */
    public function it_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'You have been logged out.');
        $this->assertGuest();
    }

    /**
     * @test
     */
    public function it_can_check_email_availability()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        // Check existing email
        $response = $this->getJson('/check-email?email=existing@example.com');
        $response->assertJson(['exists' => true]);

        // Check non-existing email
        $response = $this->getJson('/check-email?email=new@example.com');
        $response->assertJson(['exists' => false]);
    }

    /**
     * @test
     */
    public function it_rejects_invalid_turnstile_token()
    {
        // Set the flag to make turnstile validation fail
        $this->turnstileShouldFail = true;

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'cf-turnstile-response' => 'invalid-token'
        ];

        $response = $this->postJson('/login', $data);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Security verification expired or invalid. Please try again.'
        ]);
    }
}
