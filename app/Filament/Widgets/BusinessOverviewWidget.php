<?php

namespace App\Filament\Widgets;

use App\Models\Customers;
use App\Models\Product3dModels;
use App\Models\Products;
use App\Models\Rentals;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class BusinessOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return [
                    Stat::make('Total Products', 0)
                        ->description('Please log in to view your stats')
                        ->color('gray'),
                ];
            }

            // Total products
            $totalProducts = Products::where('user_id', $userId)->count();

            // Active rentals
            $activeRentals = Rentals::whereHas('product', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereIn('rental_status', ['Booked', 'Picked Up'])->count();

            // Reserved products
            $reservedProducts = Products::where('user_id', $userId)->where('status', 'Reserved')->count();

            // 3D Models

            return [
                Stat::make('Total Products', $totalProducts)
                    ->description('Products in your inventory'),
                Stat::make('Active Rentals', $activeRentals)
                    ->description('Currently rented products'),
                Stat::make('Reserved Products', $reservedProducts)
                    ->description('Reserved products'),
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Error', 'Unable to load stats')
                    ->description('Please check your database connection')
                    ->color('danger'),
            ];
        }
    }
}
