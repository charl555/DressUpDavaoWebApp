<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options to optimize database performance
    | for your Laravel application. These settings help improve query speed,
    | reduce memory usage, and optimize database connections.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Query Optimization
    |--------------------------------------------------------------------------
    |
    | These settings help optimize database queries and reduce the number
    | of queries executed per request.
    |
    */
    'query_optimization' => [
        // Enable query caching for frequently accessed data
        'cache_enabled' => env('DB_CACHE_ENABLED', true),
        
        // Cache duration in minutes for query results
        'cache_duration' => env('DB_CACHE_DURATION', 60),
        
        // Enable query logging for performance monitoring
        'query_logging' => env('DB_QUERY_LOGGING', false),
        
        // Maximum number of queries to log
        'max_logged_queries' => env('DB_MAX_LOGGED_QUERIES', 100),
        
        // Enable slow query logging
        'slow_query_logging' => env('DB_SLOW_QUERY_LOGGING', true),
        
        // Threshold for slow queries in milliseconds
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Eager Loading Configuration
    |--------------------------------------------------------------------------
    |
    | Define default relationships to eager load for common models to prevent
    | N+1 query problems.
    |
    */
    'eager_loading' => [
        'products' => [
            'default' => ['product_images', 'user', 'occasions'],
            'detailed' => ['product_images', 'user.shop', 'occasions', 'product_measurements', 'rentals'],
            'shop_view' => ['product_images', 'occasions'],
            'admin_view' => ['product_images', 'user', 'occasions', 'rentals.customer'],
        ],
        
        'rentals' => [
            'default' => ['product', 'customer', 'payments'],
            'detailed' => ['product.product_images', 'customer.user', 'payments'],
            'admin_view' => ['product.user', 'customer', 'payments'],
        ],
        
        'users' => [
            'default' => ['shop', 'subscription'],
            'admin_view' => ['shop', 'subscription', 'products'],
            'profile_view' => ['user_measurements', 'subscription'],
        ],
        
        'shops' => [
            'default' => ['user'],
            'detailed' => ['user', 'products.product_images'],
        ],
        
        'customers' => [
            'default' => ['user'],
            'detailed' => ['user', 'rentals.product'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Configure pagination settings to optimize performance for large datasets.
    |
    */
    'pagination' => [
        // Default items per page
        'default_per_page' => env('DB_DEFAULT_PER_PAGE', 12),
        
        // Maximum items per page to prevent memory issues
        'max_per_page' => env('DB_MAX_PER_PAGE', 100),
        
        // Use simple pagination for better performance on large datasets
        'use_simple_pagination' => env('DB_USE_SIMPLE_PAGINATION', false),
        
        // Cache pagination counts for expensive queries
        'cache_pagination_counts' => env('DB_CACHE_PAGINATION_COUNTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Pool Settings
    |--------------------------------------------------------------------------
    |
    | Configure database connection pooling for better performance under load.
    |
    */
    'connection_pool' => [
        // Enable connection pooling
        'enabled' => env('DB_POOL_ENABLED', true),
        
        // Maximum number of connections in the pool
        'max_connections' => env('DB_POOL_MAX_CONNECTIONS', 10),
        
        // Minimum number of connections to maintain
        'min_connections' => env('DB_POOL_MIN_CONNECTIONS', 2),
        
        // Connection timeout in seconds
        'connection_timeout' => env('DB_POOL_CONNECTION_TIMEOUT', 30),
        
        // Idle timeout in seconds
        'idle_timeout' => env('DB_POOL_IDLE_TIMEOUT', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for database index optimization and maintenance.
    |
    */
    'indexes' => [
        // Enable automatic index analysis
        'auto_analysis' => env('DB_AUTO_INDEX_ANALYSIS', false),
        
        // Frequency of index analysis in hours
        'analysis_frequency' => env('DB_INDEX_ANALYSIS_FREQUENCY', 24),
        
        // Enable index usage monitoring
        'usage_monitoring' => env('DB_INDEX_USAGE_MONITORING', false),
        
        // Threshold for unused index detection (days)
        'unused_threshold_days' => env('DB_UNUSED_INDEX_THRESHOLD', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Optimization
    |--------------------------------------------------------------------------
    |
    | Settings to optimize memory usage for database operations.
    |
    */
    'memory' => [
        // Enable memory optimization for large result sets
        'optimize_large_results' => env('DB_OPTIMIZE_LARGE_RESULTS', true),
        
        // Chunk size for processing large datasets
        'chunk_size' => env('DB_CHUNK_SIZE', 1000),
        
        // Enable result set streaming for very large queries
        'enable_streaming' => env('DB_ENABLE_STREAMING', false),
        
        // Memory limit for single queries (MB)
        'query_memory_limit' => env('DB_QUERY_MEMORY_LIMIT', 128),
    ],

    /*
    |--------------------------------------------------------------------------
    | Specific Model Optimizations
    |--------------------------------------------------------------------------
    |
    | Model-specific optimization settings based on usage patterns.
    |
    */
    'model_optimizations' => [
        'products' => [
            // Cache frequently accessed products
            'cache_popular_products' => true,
            'popular_products_cache_duration' => 120, // minutes
            
            // Optimize product search
            'search_cache_duration' => 30, // minutes
            'search_result_limit' => 50,
            
            // Product listing optimizations
            'listing_cache_duration' => 60, // minutes
            'listing_per_page' => 12,
        ],
        
        'rentals' => [
            // Cache rental statistics
            'cache_rental_stats' => true,
            'stats_cache_duration' => 60, // minutes
            
            // Optimize rental queries
            'default_date_range_days' => 30,
            'max_date_range_days' => 365,
        ],
        
        'users' => [
            // Cache user session data
            'cache_user_sessions' => true,
            'session_cache_duration' => 30, // minutes
            
            // User activity tracking
            'track_last_activity' => true,
            'activity_update_frequency' => 300, // seconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Settings for monitoring database performance and identifying bottlenecks.
    |
    */
    'monitoring' => [
        // Enable performance monitoring
        'enabled' => env('DB_PERFORMANCE_MONITORING', false),
        
        // Log slow queries
        'log_slow_queries' => env('DB_LOG_SLOW_QUERIES', true),
        
        // Slow query threshold in milliseconds
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000),
        
        // Monitor query patterns
        'monitor_query_patterns' => env('DB_MONITOR_QUERY_PATTERNS', false),
        
        // Alert thresholds
        'alert_thresholds' => [
            'queries_per_request' => env('DB_ALERT_QUERIES_PER_REQUEST', 50),
            'memory_usage_mb' => env('DB_ALERT_MEMORY_USAGE', 256),
            'response_time_ms' => env('DB_ALERT_RESPONSE_TIME', 2000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Database-specific caching configuration for improved performance.
    |
    */
    'cache' => [
        // Default cache store for database queries
        'store' => env('DB_CACHE_STORE', 'redis'),
        
        // Cache key prefix
        'prefix' => env('DB_CACHE_PREFIX', 'db_cache'),
        
        // Enable cache tags for better cache management
        'use_tags' => env('DB_CACHE_USE_TAGS', true),
        
        // Cache invalidation strategies
        'invalidation' => [
            'on_model_update' => true,
            'on_related_update' => true,
            'scheduled_cleanup' => true,
            'cleanup_frequency' => 'daily',
        ],
    ],
];
