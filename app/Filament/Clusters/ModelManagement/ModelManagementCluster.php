<?php

namespace App\Filament\Clusters\ModelManagement;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ModelManagementCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Product 3D Models';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }
}
