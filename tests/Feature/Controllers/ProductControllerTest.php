<?php

namespace Tests\Feature\Controllers;

use App\Models\ProductImages;
use App\Models\ProductMeasurements;
use App\Models\Products;
use App\Models\Shops;
use App\Models\User;
use App\Models\UserMeasurements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $shop;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'Tailor']);
        $this->shop = Shops::factory()->create(['user_id' => $this->user->id, 'shop_status' => 'Verified']);

        $this->product = Products::factory()->create([
            'user_id' => $this->user->id,
            'visibility' => 'Yes',
            'status' => 'Available'
        ]);

        ProductImages::factory()->create(['product_id' => $this->product->product_id]);
        ProductMeasurements::factory()->create(['product_id' => $this->product->product_id]);
    }

    /**
     * @test
     */
    public function it_can_show_product_details()
    {
        $response = $this->get("/product-overview/{$this->product->product_id}");

        $response->assertStatus(200);
        $response->assertViewIs('productoverview');
        $response->assertViewHas('product');

        $viewProduct = $response->viewData('product');
        $this->assertEquals($this->product->product_id, $viewProduct->product_id);
    }

    /**
     * @test
     */
    public function it_returns_404_for_non_existent_product()
    {
        $response = $this->get('/product-overview/999999');

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function it_returns_404_for_product_with_unverified_shop()
    {
        $unverifiedShop = Shops::factory()->create(['shop_status' => 'Pending']);
        $unverifiedProduct = Products::factory()->create([
            'user_id' => $unverifiedShop->user_id,
            'visibility' => 'Yes'
        ]);

        $response = $this->get("/product-overview/{$unverifiedProduct->product_id}");

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function it_can_list_products_with_default_filters()
    {
        // Create more products
        Products::factory()->count(5)->create([
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/product-list');

        $response->assertStatus(200);
        $response->assertViewIs('productlist');
        $response->assertViewHas('products');

        $products = $response->viewData('products');
        $this->assertGreaterThan(0, $products->count());
    }

    /**
     * @test
     */
    public function it_can_filter_products_by_type()
    {
        $gown = Products::factory()->create([
            'type' => 'Gown',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        $suit = Products::factory()->create([
            'type' => 'Suit',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/product-list?type=Gown');

        $response->assertStatus(200);
        $products = $response->viewData('products');

        $this->assertTrue($products->contains('type', 'Gown'));
        $this->assertFalse($products->contains('type', 'Suit'));
    }

    /**
     * @test
     */
    public function it_can_filter_products_by_multiple_types()
    {
        Products::factory()->create(['type' => 'Gown', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Dress', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Suit', 'visibility' => 'Yes', 'user_id' => $this->user->id]);

        $response = $this->get('/product-list?type[]=Gown&type[]=Dress');

        $response->assertStatus(200);
        $products = $response->viewData('products');

        $this->assertTrue($products->contains('type', 'Gown'));
        $this->assertTrue($products->contains('type', 'Dress'));
        $this->assertFalse($products->contains('type', 'Suit'));
    }

    /**
     * @test
     */
    public function it_filters_products_by_gender_for_female_users()
    {
        $femaleUser = User::factory()->create(['gender' => 'Female']);
        $this->actingAs($femaleUser);

        Products::factory()->create(['type' => 'Gown', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Dress', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Suit', 'visibility' => 'Yes', 'user_id' => $this->user->id]);

        $response = $this->get('/product-list');

        $products = $response->viewData('products');

        $this->assertTrue($products->contains('type', 'Gown'));
        $this->assertTrue($products->contains('type', 'Dress'));
        $this->assertFalse($products->contains('type', 'Suit'));
    }

    /**
     * @test
     */
    public function it_filters_products_by_gender_for_male_users()
    {
        $maleUser = User::factory()->create(['gender' => 'Male']);
        $this->actingAs($maleUser);

        Products::factory()->create(['type' => 'Gown', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Suit', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Jacket', 'visibility' => 'Yes', 'user_id' => $this->user->id]);

        $response = $this->get('/product-list');

        $products = $response->viewData('products');

        $this->assertTrue($products->contains('type', 'Suit'));
        $this->assertTrue($products->contains('type', 'Jacket'));
        $this->assertFalse($products->contains('type', 'Gown'));
    }

    /**
     * @test
     */
    public function it_shows_all_products_for_guests()
    {
        $this->assertGuest();  // Ensure no user is logged in

        Products::factory()->create(['type' => 'Gown', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Suit', 'visibility' => 'Yes', 'user_id' => $this->user->id]);
        Products::factory()->create(['type' => 'Jacket', 'visibility' => 'Yes', 'user_id' => $this->user->id]);

        $response = $this->get('/product-list');

        $products = $response->viewData('products');

        // Guests should see all products
        $this->assertTrue($products->contains('type', 'Gown'));
        $this->assertTrue($products->contains('type', 'Suit'));
        $this->assertTrue($products->contains('type', 'Jacket'));
    }

    /**
     * @test
     */
    public function it_can_filter_products_by_size()
    {
        Products::factory()->create([
            'size' => 'M',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        Products::factory()->create([
            'size' => 'L',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/product-list?size=M');

        $products = $response->viewData('products');
        $this->assertTrue($products->contains('size', 'M'));
        $this->assertFalse($products->contains('size', 'L'));
    }

    /**
     * @test
     */
    public function it_can_filter_products_by_color()
    {
        Products::factory()->create([
            'colors' => 'Red,Blue',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        Products::factory()->create([
            'colors' => 'Green,Yellow',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/product-list?color=Red');

        $products = $response->viewData('products');

        // Check that at least one product contains Red in colors
        $hasRedProduct = $products->contains(function ($product) {
            return str_contains($product->colors, 'Red');
        });

        $this->assertTrue($hasRedProduct);
    }

    /**
     * @test
     */
    public function it_can_filter_products_by_measurements()
    {
        $userWithMeasurements = User::factory()->create(['gender' => 'Female']);
        $this->actingAs($userWithMeasurements);

        UserMeasurements::factory()->create([
            'user_id' => $userWithMeasurements->id,
            'chest' => 36,
            'waist' => 28,
            'hips' => 38,
            'shoulder' => 16
        ]);

        // Create product with measurements close to user's measurements
        $productWithMeasurements = Products::factory()->create([
            'type' => 'Gown',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        ProductMeasurements::factory()->create([
            'product_id' => $productWithMeasurements->product_id,
            'gown_chest' => 37,  // Within 2 inches tolerance
            'gown_waist' => 29,
            'gown_hips' => 39,
            'gown_shoulder' => 17
        ]);

        // Create product with measurements far from user's measurements
        $productWithoutMeasurements = Products::factory()->create([
            'type' => 'Gown',
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        // Set ALL measurements far outside tolerance to ensure filter excludes this product
        ProductMeasurements::create([
            'product_id' => $productWithoutMeasurements->product_id,
            'gown_chest' => 50,  // Outside tolerance (user has 36, tolerance is 2)
            'gown_waist' => 50,  // Outside tolerance (user has 28)
            'gown_hips' => 50,  // Outside tolerance (user has 38)
            'gown_shoulder' => 50,  // Outside tolerance (user has 16)
            'gown_bust' => 50,  // Outside tolerance
            'jacket_chest' => 50,
            'jacket_waist' => 50,
            'jacket_hip' => 50,
            'jacket_shoulder' => 50,
            'trouser_waist' => 50,
            'trouser_hip' => 50,
        ]);

        $response = $this->get('/product-list?measurements_filter=1');

        $products = $response->viewData('products');

        // Should only show products with measurements close to user's measurements
        $this->assertTrue($products->contains('product_id', $productWithMeasurements->product_id));
        $this->assertFalse($products->contains('product_id', $productWithoutMeasurements->product_id));
    }

    /**
     * @test
     */
    public function it_returns_ajax_response_for_products_list()
    {
        $response = $this->getJson('/product-list');

        $response->assertStatus(200);
        // AJAX requests return rendered HTML string, not a view object
        // The controller calls ->render() which returns HTML content
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @test
     */
    public function it_calculates_fit_score_for_products()
    {
        // Create user without gender so all products are shown
        $userWithMeasurements = User::factory()->create(['gender' => null]);
        $this->actingAs($userWithMeasurements);

        UserMeasurements::factory()->create([
            'user_id' => $userWithMeasurements->id,
            'chest' => 36,
            'waist' => 28,
            'hips' => 38,
            'shoulder' => 16
        ]);

        $product = Products::factory()->create([
            'type' => 'Gown',
            'visibility' => 'Yes',
            'status' => 'Available',
            'user_id' => $this->user->id
        ]);

        ProductMeasurements::factory()->create([
            'product_id' => $product->product_id,
            'gown_chest' => 36,
            'gown_waist' => 28
        ]);

        $response = $this->get('/product-list');

        $products = $response->viewData('products');

        // Find the specific product we created
        $testProduct = $products->firstWhere('product_id', $product->product_id);

        $this->assertNotNull($testProduct, 'Product should be in the list');
        $this->assertNotNull($testProduct->fit_score);
        $this->assertNotNull($testProduct->recommendation_level);
        $this->assertIsFloat($testProduct->fit_score);
        // recommendation_level is an array with level, label, description, color
        $this->assertIsArray($testProduct->recommendation_level);
        $this->assertArrayHasKey('level', $testProduct->recommendation_level);
        $this->assertArrayHasKey('label', $testProduct->recommendation_level);
    }

    /**
     * @test
     */
    public function it_maintains_consistent_sorting_with_session_seed()
    {
        // Create multiple products with measurements so they sort by fit_score (deterministic)
        $userWithMeasurements = User::factory()->create(['gender' => null]);
        $this->actingAs($userWithMeasurements);

        UserMeasurements::factory()->create([
            'user_id' => $userWithMeasurements->id,
            'chest' => 36,
            'waist' => 28,
            'hips' => 38,
            'shoulder' => 16
        ]);

        // Create products with measurements - these will be sorted by fit_score (deterministic)
        for ($i = 1; $i <= 5; $i++) {
            $product = Products::factory()->create([
                'type' => 'Gown',
                'visibility' => 'Yes',
                'status' => 'Available',
                'user_id' => $this->user->id
            ]);

            ProductMeasurements::create([
                'product_id' => $product->product_id,
                'gown_chest' => 36 + $i,  // Varying measurements for different fit scores
                'gown_waist' => 28 + $i,
                'gown_hips' => 38 + $i,
            ]);
        }

        $firstResponse = $this->get('/product-list');
        $firstProducts = $firstResponse
            ->viewData('products')
            ->filter(fn($p) => $p->has_actual_measurements)
            ->pluck('product_id')
            ->toArray();

        $secondResponse = $this->get('/product-list');
        $secondProducts = $secondResponse
            ->viewData('products')
            ->filter(fn($p) => $p->has_actual_measurements)
            ->pluck('product_id')
            ->toArray();

        // Products with measurements should be sorted consistently by fit_score
        $this->assertEquals($firstProducts, $secondProducts);
    }

    /**
     * @test
     */
    public function it_only_shows_visible_products()
    {
        $visibleProduct = Products::factory()->create([
            'visibility' => 'Yes',
            'user_id' => $this->user->id
        ]);

        $hiddenProduct = Products::factory()->create([
            'visibility' => 'No',
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/product-list');

        $products = $response->viewData('products');

        $this->assertTrue($products->contains('product_id', $visibleProduct->product_id));
        $this->assertFalse($products->contains('product_id', $hiddenProduct->product_id));
    }
}
