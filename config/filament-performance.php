<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Optimizations
    |--------------------------------------------------------------------------
    |
    | Configuration for optimizing Filament performance
    |
    */

    // Enable query caching for resources
    'cache_queries' => env('FILAMENT_CACHE_QUERIES', true),
    
    // Cache duration in minutes
    'cache_duration' => env('FILAMENT_CACHE_DURATION', 60),
    
    // Pagination settings
    'default_pagination' => env('FILAMENT_DEFAULT_PAGINATION', 25),
    'max_pagination' => env('FILAMENT_MAX_PAGINATION', 100),
    
    // Image optimization
    'image_optimization' => [
        'enabled' => env('FILAMENT_IMAGE_OPTIMIZATION', true),
        'quality' => env('FILAMENT_IMAGE_QUALITY', 85),
        'max_width' => env('FILAMENT_IMAGE_MAX_WIDTH', 1200),
        'max_height' => env('FILAMENT_IMAGE_MAX_HEIGHT', 1200),
    ],
    
    // Eager loading defaults
    'eager_load' => [
        'products' => ['occasions', 'product_images', 'user'],
        'rentals' => ['product', 'customer', 'payments'],
        'customers' => ['user'],
        'product_images' => ['product'],
    ],
];
