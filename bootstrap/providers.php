<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    // App\Providers\DatabasePerformanceServiceProvider::class, // Temporarily disabled
    App\Providers\Filament\AdminPanelProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,
    Spatie\LaravelImageOptimizer\ImageOptimizerServiceProvider::class,
];
