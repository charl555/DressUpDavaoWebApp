<?php

use App\Models\Products;
use App\Models\User;
use App\Models\Shops;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product list filters by size correctly', function () {
    // Create a user and shop
    $user = User::factory()->create();
    $shop = Shops::factory()->create(['user_id' => $user->id]);
    
    // Create products with different sizes (using full size names as stored in database)
    $productXS = Products::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Product XS',
        'size' => 'Extra Small',
        'visibility' => 'Yes'
    ]);
    
    $productM = Products::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Product M',
        'size' => 'Medium',
        'visibility' => 'Yes'
    ]);
    
    $productL = Products::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Product L',
        'size' => 'Large',
        'visibility' => 'Yes'
    ]);

    // Test filtering by XS (should find Extra Small)
    $response = $this->get('/product-list?size[]=XS');
    $response->assertStatus(200);
    $response->assertSee('Test Product XS');
    $response->assertDontSee('Test Product M');
    $response->assertDontSee('Test Product L');

    // Test filtering by M (should find Medium)
    $response = $this->get('/product-list?size[]=M');
    $response->assertStatus(200);
    $response->assertSee('Test Product M');
    $response->assertDontSee('Test Product XS');
    $response->assertDontSee('Test Product L');

    // Test filtering by multiple sizes
    $response = $this->get('/product-list?size[]=XS&size[]=L');
    $response->assertStatus(200);
    $response->assertSee('Test Product XS');
    $response->assertSee('Test Product L');
    $response->assertDontSee('Test Product M');
});

test('shop overview filters by size correctly', function () {
    // Create a user and shop
    $user = User::factory()->create();
    $shop = Shops::factory()->create(['user_id' => $user->id]);
    
    // Create products with different sizes for this shop
    $productS = Products::factory()->create([
        'user_id' => $user->id,
        'name' => 'Shop Product S',
        'size' => 'Small',
        'visibility' => 'Yes'
    ]);
    
    $productXL = Products::factory()->create([
        'user_id' => $user->id,
        'name' => 'Shop Product XL',
        'size' => 'Extra Large',
        'visibility' => 'Yes'
    ]);
    
    // Create a product from another user (should not appear)
    $otherUser = User::factory()->create();
    Products::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Other Product S',
        'size' => 'Small',
        'visibility' => 'Yes'
    ]);

    // Test filtering by S in shop overview
    $response = $this->get("/shop-overview/{$shop->shop_id}?size[]=S");
    $response->assertStatus(200);
    $response->assertSee('Shop Product S');
    $response->assertDontSee('Shop Product XL');
    $response->assertDontSee('Other Product S'); // Should not see products from other shops

    // Test filtering by XL in shop overview
    $response = $this->get("/shop-overview/{$shop->shop_id}?size[]=XL");
    $response->assertStatus(200);
    $response->assertSee('Shop Product XL');
    $response->assertDontSee('Shop Product S');
});

test('size filter handles invalid sizes gracefully', function () {
    // Create a product
    $user = User::factory()->create();
    Products::factory()->create([
        'user_id' => $user->id,
        'name' => 'Test Product',
        'size' => 'Medium',
        'visibility' => 'Yes'
    ]);

    // Test with invalid size that doesn't exist in mapping
    $response = $this->get('/product-list?size[]=INVALID');
    $response->assertStatus(200);
    // Should not crash and should not show any products
    $response->assertDontSee('Test Product');
});
