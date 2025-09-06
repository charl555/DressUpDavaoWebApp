<?php

namespace App\Filament\Clusters\ModelManagement;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class ModelManagementCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Product 3D Models';
}
