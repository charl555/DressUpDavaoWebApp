<?php

namespace App\Filament\Widgets;

use App\Models\ShopAccountRequests;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ShopVerificationRequestWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Pending Requests', ShopAccountRequests::where('status', 'Pending')->count())
                ->description('Shop verification requests pending review')
                ->color('gray'),
            Stat::make('Under Review', ShopAccountRequests::where('status', 'Under Review')->count())
                ->description('Shop verification requests under review')
                ->color('gray'),
            Stat::make('Verified Shops', ShopAccountRequests::where('status', 'Verified')->count())
                ->description('Verified shop accounts')
                ->color('gray'),
        ];
    }
}
