<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "🚀 Adding performance indexes...\n\n";
        
        // Add indexes using raw SQL to avoid conflicts with existing indexes
        $this->addIndexSafely('users', 'role', 'users_role_performance_idx');
        $this->addIndexSafely('users', 'bodytype', 'users_bodytype_performance_idx');
        $this->addIndexSafely('users', 'created_at', 'users_created_at_performance_idx');
        
        // Products table indexes - Most critical for performance
        $this->addIndexSafely('products', 'visibility', 'products_visibility_performance_idx');
        $this->addIndexSafely('products', 'status', 'products_status_performance_idx');
        $this->addIndexSafely('products', 'type', 'products_type_performance_idx');
        $this->addIndexSafely('products', 'subtype', 'products_subtype_performance_idx');
        $this->addIndexSafely('products', 'rental_price', 'products_rental_price_performance_idx');
        $this->addIndexSafely('products', 'rental_count', 'products_rental_count_performance_idx');
        $this->addIndexSafely('products', 'created_at', 'products_created_at_performance_idx');
        
        // Composite indexes for products
        $this->addCompositeIndexSafely('products', ['visibility', 'status'], 'products_visibility_status_performance_idx');
        $this->addCompositeIndexSafely('products', ['user_id', 'visibility'], 'products_user_visibility_performance_idx');
        $this->addCompositeIndexSafely('products', ['user_id', 'status'], 'products_user_status_performance_idx');
        $this->addCompositeIndexSafely('products', ['type', 'subtype'], 'products_type_subtype_performance_idx');
        $this->addCompositeIndexSafely('products', ['visibility', 'type'], 'products_visibility_type_performance_idx');
        $this->addCompositeIndexSafely('products', ['status', 'rental_price'], 'products_status_price_performance_idx');
        
        // Rentals table indexes - Critical for rental management
        $this->addIndexSafely('rentals', 'rental_status', 'rentals_status_performance_idx');
        $this->addIndexSafely('rentals', 'pickup_date', 'rentals_pickup_date_performance_idx');
        $this->addIndexSafely('rentals', 'event_date', 'rentals_event_date_performance_idx');
        $this->addIndexSafely('rentals', 'return_date', 'rentals_return_date_performance_idx');
        $this->addIndexSafely('rentals', 'actual_return_date', 'rentals_actual_return_performance_idx');
        $this->addIndexSafely('rentals', 'is_returned', 'rentals_is_returned_performance_idx');
        $this->addIndexSafely('rentals', 'created_at', 'rentals_created_at_performance_idx');
        
        // Composite indexes for rentals
        $this->addCompositeIndexSafely('rentals', ['rental_status', 'pickup_date'], 'rentals_status_pickup_performance_idx');
        $this->addCompositeIndexSafely('rentals', ['rental_status', 'return_date'], 'rentals_status_return_performance_idx');
        $this->addCompositeIndexSafely('rentals', ['product_id', 'rental_status'], 'rentals_product_status_performance_idx');
        $this->addCompositeIndexSafely('rentals', ['customer_id', 'rental_status'], 'rentals_customer_status_performance_idx');
        $this->addCompositeIndexSafely('rentals', ['pickup_date', 'return_date'], 'rentals_date_range_performance_idx');
        
        // Payments table indexes
        $this->addIndexSafely('payments', 'payment_status', 'payments_status_performance_idx');
        $this->addIndexSafely('payments', 'payment_method', 'payments_method_performance_idx');
        $this->addIndexSafely('payments', 'payment_date', 'payments_date_performance_idx');
        $this->addIndexSafely('payments', 'amount_paid', 'payments_amount_performance_idx');
        $this->addCompositeIndexSafely('payments', ['rental_id', 'payment_status'], 'payments_rental_status_performance_idx');
        $this->addCompositeIndexSafely('payments', ['payment_date', 'payment_status'], 'payments_date_status_performance_idx');
        
        // Customers table indexes
        $this->addIndexSafely('customers', 'phone_number', 'customers_phone_performance_idx');
        $this->addCompositeIndexSafely('customers', ['first_name', 'last_name'], 'customers_name_performance_idx');
        
        // Shops table indexes
        $this->addIndexSafely('shops', 'shop_slug', 'shops_slug_performance_idx');
        $this->addIndexSafely('shops', 'shop_name', 'shops_name_performance_idx');
        
        // Subscriptions table indexes
        $this->addIndexSafely('subscriptions', 'subscription_type', 'subscriptions_type_performance_idx');
        $this->addIndexSafely('subscriptions', 'subscription_status', 'subscriptions_status_performance_idx');
        $this->addIndexSafely('subscriptions', 'start_date', 'subscriptions_start_performance_idx');
        $this->addIndexSafely('subscriptions', 'end_date', 'subscriptions_end_performance_idx');
        $this->addCompositeIndexSafely('subscriptions', ['subscription_status', 'end_date'], 'subscriptions_status_end_performance_idx');
        $this->addCompositeIndexSafely('subscriptions', ['user_id', 'subscription_status'], 'subscriptions_user_status_performance_idx');
        
        // User Measurements table indexes (if table exists)
        if (Schema::hasTable('user_measurements')) {
            $this->addCompositeIndexSafely('user_measurements', ['chest', 'waist', 'hips'], 'user_measurements_body_performance_idx');
        }
        
        // Product Measurements table indexes
        $this->addCompositeIndexSafely('product_measurements', ['gown_chest', 'gown_waist', 'gown_hips'], 'product_measurements_gown_performance_idx');
        $this->addCompositeIndexSafely('product_measurements', ['trouser_waist', 'trouser_hip'], 'product_measurements_trouser_performance_idx');
        
        // Kiri Engine Jobs table indexes
        $this->addIndexSafely('kiri_engine_jobs', 'status', 'kiri_jobs_status_performance_idx');
        $this->addIndexSafely('kiri_engine_jobs', 'is_downloaded', 'kiri_jobs_downloaded_performance_idx');
        $this->addIndexSafely('kiri_engine_jobs', 'url_expiry', 'kiri_jobs_expiry_performance_idx');
        $this->addCompositeIndexSafely('kiri_engine_jobs', ['user_id', 'status'], 'kiri_jobs_user_status_performance_idx');
        $this->addCompositeIndexSafely('kiri_engine_jobs', ['status', 'created_at'], 'kiri_jobs_status_created_performance_idx');
        
        // Product 3D Models table indexes
        $this->addIndexSafely('product_3d_models', 'model_path', 'product_3d_models_path_performance_idx');
        
        // System table optimizations (if tables exist)
        if (Schema::hasTable('cache')) {
            $this->addIndexSafely('cache', 'expiration', 'cache_expiration_performance_idx');
        }
        
        $this->addCompositeIndexSafely('jobs', ['queue', 'reserved_at'], 'jobs_queue_reserved_performance_idx');
        $this->addIndexSafely('jobs', 'available_at', 'jobs_available_performance_idx');
        $this->addIndexSafely('failed_jobs', 'failed_at', 'failed_jobs_failed_at_performance_idx');
        $this->addCompositeIndexSafely('failed_jobs', ['queue', 'failed_at'], 'failed_jobs_queue_failed_performance_idx');
        $this->addCompositeIndexSafely('sessions', ['user_id', 'last_activity'], 'sessions_user_activity_performance_idx');
        
        echo "\n✅ Performance indexes migration completed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🗑️ Removing performance indexes...\n";
        
        // Drop performance indexes
        $indexes = [
            'users' => ['users_role_performance_idx', 'users_bodytype_performance_idx', 'users_created_at_performance_idx'],
            'products' => [
                'products_visibility_performance_idx', 'products_status_performance_idx', 'products_type_performance_idx',
                'products_subtype_performance_idx', 'products_rental_price_performance_idx', 'products_rental_count_performance_idx',
                'products_created_at_performance_idx', 'products_visibility_status_performance_idx', 'products_user_visibility_performance_idx',
                'products_user_status_performance_idx', 'products_type_subtype_performance_idx', 'products_visibility_type_performance_idx',
                'products_status_price_performance_idx'
            ],
            'rentals' => [
                'rentals_status_performance_idx', 'rentals_pickup_date_performance_idx', 'rentals_event_date_performance_idx',
                'rentals_return_date_performance_idx', 'rentals_actual_return_performance_idx', 'rentals_is_returned_performance_idx',
                'rentals_created_at_performance_idx', 'rentals_status_pickup_performance_idx', 'rentals_status_return_performance_idx',
                'rentals_product_status_performance_idx', 'rentals_customer_status_performance_idx', 'rentals_date_range_performance_idx'
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $index) {
                $this->dropIndexSafely($table, $index);
            }
        }
        
        echo "✅ Performance indexes removed successfully!\n";
    }

    /**
     * Add an index safely (only if it doesn't exist).
     */
    private function addIndexSafely(string $table, string $column, string $indexName): void
    {
        try {
            if (!Schema::hasTable($table)) {
                echo "  ⚠️  Table {$table} does not exist, skipping index {$indexName}\n";
                return;
            }
            
            if (!$this->indexExists($table, $indexName)) {
                DB::statement("CREATE INDEX `{$indexName}` ON `{$table}` (`{$column}`)");
                echo "  ✅ Added index: {$indexName} on {$table}.{$column}\n";
            } else {
                echo "  ⚠️  Index already exists: {$indexName}\n";
            }
        } catch (\Exception $e) {
            echo "  ❌ Failed to add index {$indexName}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Add a composite index safely.
     */
    private function addCompositeIndexSafely(string $table, array $columns, string $indexName): void
    {
        try {
            if (!Schema::hasTable($table)) {
                echo "  ⚠️  Table {$table} does not exist, skipping composite index {$indexName}\n";
                return;
            }
            
            if (!$this->indexExists($table, $indexName)) {
                $columnList = '`' . implode('`, `', $columns) . '`';
                DB::statement("CREATE INDEX `{$indexName}` ON `{$table}` ({$columnList})");
                echo "  ✅ Added composite index: {$indexName} on {$table}(" . implode(', ', $columns) . ")\n";
            } else {
                echo "  ⚠️  Composite index already exists: {$indexName}\n";
            }
        } catch (\Exception $e) {
            echo "  ❌ Failed to add composite index {$indexName}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Drop an index safely.
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        try {
            if ($this->indexExists($table, $indexName)) {
                DB::statement("DROP INDEX `{$indexName}` ON `{$table}`");
                echo "  ✅ Dropped index: {$indexName}\n";
            }
        } catch (\Exception $e) {
            // Ignore errors when dropping indexes
            echo "  ⚠️  Could not drop index {$indexName}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Check if an index exists.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
                return !empty($result);
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
};
