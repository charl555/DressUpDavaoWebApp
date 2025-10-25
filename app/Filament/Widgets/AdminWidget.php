<?php

namespace App\Filament\Widgets;

use App\Models\Shops;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AdminWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::where('role', 'User')->count())
                ->description('Total number of registered users')
                ->color('gray'),
            Stat::make('Total Shops', Shops::where('shop_status', 'Verified')->count())
                ->description('Total number of verified shops')
                ->color('gray'),
        ];
    }
}
