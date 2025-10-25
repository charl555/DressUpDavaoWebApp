<?php

namespace App\Filament\Widgets;

use App\Models\Products;
use App\Models\Rentals;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Facades\Auth;

class BusinessOverviewWidget2 extends StatsOverviewWidget
{
    protected static ?int $sort = 2;
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

            // Overdue returns
            $overdueReturns = Rentals::whereHas('product', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->where('rental_status', 'Picked Up')
                ->where('return_date', '<=', Carbon::now()->subDays(1))
                ->count();

            // Not Available products
            $notAvailableProducts = Products::where('user_id', $userId)->whereIn('status', ['Maintenance', 'Pending Cleaning', 'In Cleaning', 'Steamed & Pressed', 'Quality Check', 'Needs Repair', 'In Alteration', 'Damaged – Not Rentable'])->count();

            // Monthly revenue (sum of payments.amount_paid)
            $monthlyRevenue = \App\Models\Payments::whereHas('rental.product', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount_paid') ?? 0;

            return [
                Stat::make('Overdue Returns', $overdueReturns)
                    ->description('Rentals overdue for return'),
                Stat::make('Not Available Products', $notAvailableProducts)
                    ->description('Products not available for rental'),
                Stat::make('Monthly Revenue', '₱' . number_format($monthlyRevenue, 2))
                    ->description('Revenue for ' . now()->format('F Y')),
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
