<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ShopVerificationRequestsWidget;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    // protected string $view = 'filament-panels::pages.dashboard';
    protected static ?int $navigationSort = -2;

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string
    {
        if (Auth::user()->isSuperAdmin()) {
            return '    Admin Dashboard';
        }
        $user = Auth::user();
        $greeting = $this->getGreeting();

        return $greeting . ($user ? ', ' . $user->name : '') . '!';
    }

    public function getSubheading(): ?string
    {
        if (Auth::user()->isSuperAdmin()) {
            return 'Welcome to the admin dashboard';
        }
        {
            return 'Manage your dress rental business with ease';
        }
    }

    private function getGreeting(): string
    {
        if (Auth::user()->isSuperAdmin()) {
            return '';
        }
        $hour = now()->hour;

        if ($hour < 12) {
            return 'Good Morning';
        } elseif ($hour < 17) {
            return 'Good Afternoon';
        } else {
            return 'Good Evening';
        }
    }

    public function getWidgets(): array
    {
        if (Auth::user()->isSuperAdmin()) {
            return [
                \App\Filament\Widgets\ShopVerificationRequestWidget::class,
                \App\Filament\Widgets\AdminWidget::class,
            ];
        }
        return [
            // AccountWidget::class,
            \App\Filament\Widgets\BusinessOverviewWidget::class,
            \App\Filament\Widgets\BusinessOverviewWidget2::class,
            // \App\Filament\Widgets\QuickActionsWidget::class,
            // \App\Filament\Widgets\ModelGenerationStatusWidget::class,
            \App\Filament\Widgets\RecentActivityWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}
