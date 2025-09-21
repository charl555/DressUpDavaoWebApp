<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class DatabasePerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register performance monitoring services
        $this->app->singleton('db.performance.monitor', function ($app) {
            return new DatabasePerformanceMonitor();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enable query logging if configured
        if (config('database-performance.query_optimization.query_logging', false)) {
            $this->enableQueryLogging();
        }

        // Enable slow query monitoring
        if (config('database-performance.query_optimization.slow_query_logging', true)) {
            $this->enableSlowQueryMonitoring();
        }

        // Set up database connection optimizations
        $this->optimizeDatabaseConnections();

        // Register model observers for cache invalidation
        $this->registerCacheInvalidationObservers();
    }

    /**
     * Enable query logging for performance monitoring.
     */
    protected function enableQueryLogging(): void
    {
        DB::listen(function (QueryExecuted $query) {
            $maxQueries = config('database-performance.query_optimization.max_logged_queries', 100);

            // Store queries in cache with rotation
            $queries = Cache::get('db_logged_queries', []);

            if (count($queries) >= $maxQueries) {
                array_shift($queries);  // Remove oldest query
            }

            $queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'connection' => $query->connectionName,
                'timestamp' => now()->toISOString(),
            ];

            Cache::put('db_logged_queries', $queries, now()->addHours(1));
        });
    }

    /**
     * Enable slow query monitoring and logging.
     */
    protected function enableSlowQueryMonitoring(): void
    {
        DB::listen(function (QueryExecuted $query) {
            $threshold = config('database-performance.query_optimization.slow_query_threshold', 1000);

            if ($query->time > $threshold) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName,
                    'threshold' => $threshold . 'ms',
                ]);

                // Store slow queries for analysis
                $slowQueries = Cache::get('db_slow_queries', []);
                $slowQueries[] = [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                    'connection' => $query->connectionName,
                    'timestamp' => now()->toISOString(),
                ];

                // Keep only last 50 slow queries
                if (count($slowQueries) > 50) {
                    $slowQueries = array_slice($slowQueries, -50);
                }

                Cache::put('db_slow_queries', $slowQueries, now()->addDays(7));
            }
        });
    }

    /**
     * Optimize database connections.
     */
    protected function optimizeDatabaseConnections(): void
    {
        // Set connection-specific optimizations
        $connections = config('database.connections');

        foreach ($connections as $name => $config) {
            if ($config['driver'] === 'mysql') {
                try {
                    // MySQL-specific optimizations
                    DB::connection($name)->getPdo()->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
                    DB::connection($name)->getPdo()->exec('SET SESSION innodb_lock_wait_timeout=50');

                    // Try to enable query cache if available
                    try {
                        DB::connection($name)->getPdo()->exec('SET SESSION query_cache_type=ON');
                    } catch (\PDOException $e) {
                        // Query cache might be disabled globally, which is fine
                        if (config('app.debug')) {
                            Log::info('Query cache not available: ' . $e->getMessage());
                        }
                    }
                } catch (\PDOException $e) {
                    Log::warning('Database connection optimization failed: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Register cache invalidation observers for models.
     */
    protected function registerCacheInvalidationObservers(): void
    {
        if (!config('database-performance.cache.invalidation.on_model_update', true)) {
            return;
        }

        // Register observers for key models
        $models = [
            \App\Models\Products::class => ['products', 'product_lists', 'shop_products'],
            \App\Models\Rentals::class => ['rentals', 'rental_stats'],
            \App\Models\User::class => ['users', 'user_sessions'],
            \App\Models\Shops::class => ['shops', 'shop_lists'],
        ];

        foreach ($models as $model => $cacheTags) {
            $model::observe(new class($cacheTags) {
                protected $cacheTags;

                public function __construct($cacheTags)
                {
                    $this->cacheTags = $cacheTags;
                }

                public function saved($model)
                {
                    $this->invalidateCache();
                }

                public function deleted($model)
                {
                    $this->invalidateCache();
                }

                protected function invalidateCache()
                {
                    if (config('database-performance.cache.use_tags', true)) {
                        foreach ($this->cacheTags as $tag) {
                            Cache::tags($tag)->flush();
                        }
                    }
                }
            });
        }
    }
}

/**
 * Database Performance Monitor Class
 */
class DatabasePerformanceMonitor
{
    protected $queryCount = 0;
    protected $totalQueryTime = 0;
    protected $memoryUsage = 0;

    public function __construct()
    {
        $this->memoryUsage = memory_get_usage(true);

        if (config('database-performance.monitoring.enabled', false)) {
            $this->startMonitoring();
        }
    }

    /**
     * Start performance monitoring.
     */
    protected function startMonitoring(): void
    {
        DB::listen(function (QueryExecuted $query) {
            $this->queryCount++;
            $this->totalQueryTime += $query->time;

            // Check alert thresholds
            $this->checkAlertThresholds();
        });
    }

    /**
     * Check if any alert thresholds are exceeded.
     */
    protected function checkAlertThresholds(): void
    {
        $thresholds = config('database-performance.monitoring.alert_thresholds', []);

        // Check queries per request
        if (isset($thresholds['queries_per_request']) &&
                $this->queryCount > $thresholds['queries_per_request']) {
            Log::warning('High query count detected', [
                'query_count' => $this->queryCount,
                'threshold' => $thresholds['queries_per_request'],
                'url' => request()->url(),
            ]);
        }

        // Check memory usage
        $currentMemory = memory_get_usage(true) / 1024 / 1024;  // MB
        if (isset($thresholds['memory_usage_mb']) &&
                $currentMemory > $thresholds['memory_usage_mb']) {
            Log::warning('High memory usage detected', [
                'memory_usage_mb' => round($currentMemory, 2),
                'threshold' => $thresholds['memory_usage_mb'],
                'url' => request()->url(),
            ]);
        }

        // Check total response time
        if (isset($thresholds['response_time_ms']) &&
                $this->totalQueryTime > $thresholds['response_time_ms']) {
            Log::warning('High database response time detected', [
                'total_query_time_ms' => round($this->totalQueryTime, 2),
                'threshold' => $thresholds['response_time_ms'],
                'query_count' => $this->queryCount,
                'url' => request()->url(),
            ]);
        }
    }

    /**
     * Get performance statistics.
     */
    public function getStats(): array
    {
        return [
            'query_count' => $this->queryCount,
            'total_query_time' => round($this->totalQueryTime, 2),
            'average_query_time' => $this->queryCount > 0 ? round($this->totalQueryTime / $this->queryCount, 2) : 0,
            'memory_usage_mb' => round((memory_get_usage(true) - $this->memoryUsage) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ];
    }
}
