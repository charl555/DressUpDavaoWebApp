<?php

namespace App\Filament\Resources\ProductMaintenances\Pages;

use App\Filament\Resources\ProductMaintenances\ProductMaintenanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductMaintenances extends ListRecords
{
    protected static string $resource = ProductMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
