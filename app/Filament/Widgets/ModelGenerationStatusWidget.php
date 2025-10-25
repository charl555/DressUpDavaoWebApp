<?php

namespace App\Filament\Widgets;

use App\Models\KiriEngineJobs;
use App\Models\Product3dModels;
use App\Models\Products;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ModelGenerationStatusWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public function getHeading(): string
    {
        return '3D Model Generation Overview';
    }

    public function getDescription(): string
    {
        return 'Track your 3D model generation progress and product coverage';
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $userId = Auth::id();

        // Get job status counts
        $jobStatuses = KiriEngineJobs::where('user_id', $userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get product 3D model coverage
        $totalProducts = Products::where('user_id', $userId)->count();
        $productsWithModels = Product3dModels::whereHas('product', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
        $productsWithoutModels = $totalProducts - $productsWithModels;

        // If no jobs exist, show a placeholder
        if (empty($jobStatuses)) {
            return [
                'datasets' => [
                    [
                        'label' => 'No 3D Model Jobs',
                        'data' => [1],
                        'backgroundColor' => ['#6B7280'],
                    ],
                ],
                'labels' => ['No Jobs Created Yet'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => '3D Model Jobs',
                    'data' => [
                        $jobStatuses['finished'] ?? 0,
                        $jobStatuses['processing'] ?? 0,
                        $jobStatuses['uploading'] ?? 0,
                        $jobStatuses['pending'] ?? 0,
                        $jobStatuses['failed'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#10B981',  // green - finished
                        '#3B82F6',  // blue - processing
                        '#F59E0B',  // yellow - uploading
                        '#6B7280',  // gray - pending
                        '#EF4444',  // red - failed
                    ],
                ],
            ],
            'labels' => ['Finished', 'Processing', 'Uploading', 'Pending', 'Failed'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
