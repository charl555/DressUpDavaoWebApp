<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimizeDatabasePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize-performance 
                            {--migrate : Run the performance indexes migration}
                            {--analyze : Analyze current database performance}
                            {--cleanup : Clean up unused indexes and optimize tables}
                            {--all : Run all optimization steps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database performance by adding indexes, analyzing queries, and cleaning up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Database Performance Optimization...');
        $this->newLine();

        if ($this->option('all')) {
            $this->runAllOptimizations();
        } else {
            if ($this->option('migrate')) {
                $this->runPerformanceMigration();
            }

            if ($this->option('analyze')) {
                $this->analyzePerformance();
            }

            if ($this->option('cleanup')) {
                $this->cleanupDatabase();
            }

            if (!$this->option('migrate') && !$this->option('analyze') && !$this->option('cleanup')) {
                $this->showMenu();
            }
        }

        $this->newLine();
        $this->info('✅ Database optimization completed!');
    }

    /**
     * Run all optimization steps.
     */
    protected function runAllOptimizations()
    {
        $this->runPerformanceMigration();
        $this->analyzePerformance();
        $this->cleanupDatabase();
    }

    /**
     * Show interactive menu.
     */
    protected function showMenu()
    {
        $choice = $this->choice(
            'What would you like to do?',
            [
                'migrate' => 'Run performance indexes migration',
                'analyze' => 'Analyze current database performance',
                'cleanup' => 'Clean up and optimize database',
                'all' => 'Run all optimization steps',
            ],
            'all'
        );

        switch ($choice) {
            case 'migrate':
                $this->runPerformanceMigration();
                break;
            case 'analyze':
                $this->analyzePerformance();
                break;
            case 'cleanup':
                $this->cleanupDatabase();
                break;
            case 'all':
                $this->runAllOptimizations();
                break;
        }
    }

    /**
     * Run the performance indexes migration.
     */
    protected function runPerformanceMigration()
    {
        $this->info('📊 Running performance indexes migration...');

        try {
            // Check if migration exists
            $migrationFile = database_path('migrations/2025_01_15_000000_add_performance_indexes.php');

            if (!file_exists($migrationFile)) {
                $this->error('Performance indexes migration file not found!');
                $this->info('Please ensure the migration file exists at: ' . $migrationFile);
                return;
            }

            // Run the migration
            Artisan::call('migrate', ['--path' => 'database/migrations/2025_01_15_000000_add_performance_indexes.php']);

            $this->info('✅ Performance indexes migration completed successfully!');
            $this->newLine();

            // Show added indexes summary
            $this->showIndexesSummary();
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Analyze current database performance.
     */
    protected function analyzePerformance()
    {
        $this->info('🔍 Analyzing database performance...');
        $this->newLine();

        try {
            // Get database size information
            $this->showDatabaseSize();

            // Show table sizes
            $this->showTableSizes();

            // Show index usage (MySQL specific)
            if (DB::connection()->getDriverName() === 'mysql') {
                $this->showIndexUsage();
            }

            // Show slow queries if available
            $this->showSlowQueries();
        } catch (\Exception $e) {
            $this->error('❌ Performance analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * Clean up and optimize database.
     */
    protected function cleanupDatabase()
    {
        $this->info('🧹 Cleaning up and optimizing database...');
        $this->newLine();

        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                $this->optimizeMySQLTables();
            }

            $this->cleanupOldSessions();
            $this->cleanupExpiredCache();
            $this->cleanupOldNotifications();

            $this->info('✅ Database cleanup completed!');
        } catch (\Exception $e) {
            $this->error('❌ Database cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Show summary of added indexes.
     */
    protected function showIndexesSummary()
    {
        $this->info('📋 Performance Indexes Added:');
        $this->newLine();

        $indexes = [
            'Users' => ['role', 'bodytype', 'email+role', 'created_at'],
            'Products' => ['visibility', 'status', 'type', 'subtype', 'rental_price', 'visibility+status', 'user_id+visibility'],
            'Rentals' => ['rental_status', 'pickup_date', 'return_date', 'rental_status+pickup_date'],
            'Payments' => ['payment_status', 'payment_date', 'rental_id+payment_status'],
            'Customers' => ['phone_number', 'first_name+last_name'],
            'Shops' => ['shop_slug', 'shop_name'],
            'Subscriptions' => ['subscription_status', 'end_date', 'subscription_status+end_date'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            $this->line("  <fg=cyan>$table:</> " . implode(', ', $tableIndexes));
        }

        $this->newLine();
    }

    /**
     * Show database size information.
     */
    protected function showDatabaseSize()
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Database Size (MB)',
                    ROUND(SUM(data_length) / 1024 / 1024, 2) AS 'Data Size (MB)',
                    ROUND(SUM(index_length) / 1024 / 1024, 2) AS 'Index Size (MB)'
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");

            if (!empty($result)) {
                $size = $result[0];
                $this->info("📊 Database Size: {$size->{'Database Size (MB)'}} MB (Data: {$size->{'Data Size (MB)'}} MB, Indexes: {$size->{'Index Size (MB)'}} MB)");
                $this->newLine();
            }
        }
    }

    /**
     * Show table sizes.
     */
    protected function showTableSizes()
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $tables = DB::select("
                SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
                    table_rows
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");

            $this->info('📋 Largest Tables:');
            $this->table(['Table', 'Size (MB)', 'Rows'], array_map(function ($table) {
                return [$table->table_name, $table->{'Size (MB)'}, number_format($table->table_rows)];
            }, $tables));
            $this->newLine();
        }
    }

    /**
     * Show index usage statistics.
     */
    protected function showIndexUsage()
    {
        try {
            $indexes = DB::select("
                SELECT 
                    t.table_name,
                    s.index_name,
                    s.cardinality,
                    IFNULL(u.rows_examined, 0) as rows_examined
                FROM information_schema.statistics s
                LEFT JOIN information_schema.tables t ON s.table_name = t.table_name
                LEFT JOIN (
                    SELECT 
                        object_name,
                        index_name,
                        SUM(count_read) as rows_examined
                    FROM performance_schema.table_io_waits_summary_by_index_usage 
                    WHERE object_schema = DATABASE()
                    GROUP BY object_name, index_name
                ) u ON s.table_name = u.object_name AND s.index_name = u.index_name
                WHERE s.table_schema = DATABASE()
                AND t.table_type = 'BASE TABLE'
                ORDER BY u.rows_examined DESC
                LIMIT 10
            ");

            if (!empty($indexes)) {
                $this->info('📊 Most Used Indexes:');
                $this->table(['Table', 'Index', 'Cardinality', 'Rows Examined'], array_map(function ($index) {
                    return [
                        $index->table_name,
                        $index->index_name,
                        number_format($index->cardinality),
                        number_format($index->rows_examined)
                    ];
                }, $indexes));
                $this->newLine();
            }
        } catch (\Exception $e) {
            $this->warn('Could not retrieve index usage statistics: ' . $e->getMessage());
        }
    }

    /**
     * Show slow queries if available.
     */
    protected function showSlowQueries()
    {
        // This would show slow queries from cache if monitoring is enabled
        $this->info('💡 Tip: Enable slow query monitoring in config/database-performance.php to track slow queries');
        $this->newLine();
    }

    /**
     * Optimize MySQL tables.
     */
    protected function optimizeMySQLTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');

        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            DB::statement("OPTIMIZE TABLE `$tableName`");
            $this->line("  Optimized table: $tableName");
        }
    }

    /**
     * Clean up old sessions.
     */
    protected function cleanupOldSessions()
    {
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(30)->timestamp)
            ->delete();

        $this->line("  Cleaned up $deleted old sessions");
    }

    /**
     * Clean up expired cache entries.
     */
    protected function cleanupExpiredCache()
    {
        if (Schema::hasTable('cache')) {
            $deleted = DB::table('cache')
                ->where('expiration', '<', now()->timestamp)
                ->delete();

            $this->line("  Cleaned up $deleted expired cache entries");
        }
    }

    /**
     * Clean up old notifications.
     */
    protected function cleanupOldNotifications()
    {
        if (Schema::hasTable('notifications')) {
            $deleted = DB::table('notifications')
                ->where('created_at', '<', now()->subDays(90))
                ->whereNotNull('read_at')
                ->delete();

            $this->line("  Cleaned up $deleted old read notifications");
        }
    }
}
