<?php

namespace Tests\Feature\Controllers;

use App\Models\Bookings;
use App\Models\Favorites;
use App\Models\Products;
use App\Models\ShopReviews;
use App\Models\Shops;
use App\Models\User;
use App\Models\UserMeasurements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function it_can_access_account_page()
    {
        $response = $this->get('/account');

        $response->assertStatus(200);
        $response->assertViewIs('accountpage');
        $response->assertViewHas(['bookings', 'favorites']);
    }

    /**
     * @test
     */
    public function it_can_get_bookings_via_ajax()
    {
        // Create a tailor user with a verified shop and product for the booking
        $tailorUser = User::factory()->create(['role' => 'Tailor']);
        $shop = Shops::factory()->create(['user_id' => $tailorUser->id, 'shop_status' => 'Verified']);
        $product = Products::factory()->create(['user_id' => $tailorUser->id]);

        Bookings::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->product_id
        ]);

        // Use XMLHttpRequest header to trigger ajax response
        $response = $this->get('/user/bookings', [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'current_page' => 1
        ]);
    }

    /**
     * @test
     */
    public function it_can_update_user_profile()
    {
        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'gender' => 'Female',
            'phone_number' => '09171234567'
        ];

        $response = $this->putJson('/profile/update', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /**
     * @test
     */
    public function it_validates_profile_update_data()
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'name' => '',
            'email' => 'existing@example.com',  // Duplicate email
            'gender' => 'Invalid',
            'phone_number' => 'invalid'
        ];

        $response = $this->putJson('/profile/update', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'gender', 'phone_number']);
    }

    /**
     * @test
     */
    public function it_can_update_user_preferences()
    {
        $data = [
            'color_preference' => 'blue',
            'occasion_preference' => 'formal',
            'fabric_preference' => 'cotton'
        ];

        $response = $this->postJson('/account/update-preferences', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Preferences updated successfully!'
        ]);

        $this->user->refresh();
        $this->assertEquals('blue', $this->user->preferences['color']);
        $this->assertEquals('formal', $this->user->preferences['occasion']);
    }

    /**
     * @test
     */
    public function it_can_update_body_measurements()
    {
        $data = [
            'chest' => 40,
            'waist' => 32,
            'hips' => 38,
            'shoulder' => 18
        ];

        $response = $this->putJson('/user/measurements', $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Measurements updated successfully.']);

        $this->assertDatabaseHas('user_measurements', [
            'user_id' => $this->user->id,
            'chest' => 40,
            'waist' => 32
        ]);
    }

    /**
     * @test
     */
    public function it_can_update_password()
    {
        $this->user->update(['password' => Hash::make('oldpassword')]);

        $data = [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ];

        $response = $this->post('/account/update-password', $data);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Password updated successfully.');

        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    /**
     * @test
     */
    public function it_validates_password_update()
    {
        // Test with wrong current password but valid new password format
        $data = [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123'
        ];

        $response = $this->post('/account/update-password', $data);

        $response->assertSessionHasErrors(['current_password']);
    }

    /**
     * @test
     */
    public function it_can_submit_shop_review()
    {
        $tailorUser = User::factory()->create(['role' => 'Tailor']);
        $shop = Shops::factory()->create(['user_id' => $tailorUser->id, 'shop_status' => 'Verified']);

        $data = [
            'shop_id' => $shop->shop_id,
            'rating' => 5,
            'comment' => 'Excellent service!'
        ];

        $response = $this->postJson('/submit-review', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Your review has been submitted successfully!'
        ]);

        $this->assertDatabaseHas('shop_reviews', [
            'user_id' => $this->user->id,
            'shop_id' => $shop->shop_id,
            'rating' => 5
        ]);
    }

    /**
     * @test
     */
    public function it_can_remove_product_from_favorites()
    {
        $tailorUser = User::factory()->create(['role' => 'Tailor']);
        $shop = Shops::factory()->create(['user_id' => $tailorUser->id, 'shop_status' => 'Verified']);
        $product = Products::factory()->create(['user_id' => $tailorUser->id]);

        Favorites::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->product_id
        ]);

        // Controller returns redirect with session message, not JSON
        $response = $this->delete("/products/{$product->product_id}/unfavorite");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product removed from favorites!');

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->product_id
        ]);
    }

    /**
     * @test
     */
    public function it_can_request_account_deletion()
    {
        $data = [
            'delete_confirmation' => 'DELETE',
            'delete_password' => 'password'
        ];

        $this->user->update(['password' => Hash::make('password')]);

        $response = $this->post('/account/request-deletion', $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNotNull($this->user->deletion_requested_at);
        $this->assertNotNull($this->user->scheduled_deletion_at);
    }
}
