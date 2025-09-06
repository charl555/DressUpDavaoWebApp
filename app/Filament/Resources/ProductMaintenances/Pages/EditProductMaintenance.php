<?php

namespace App\Filament\Resources\ProductMaintenances\Pages;

use App\Filament\Resources\ProductMaintenances\ProductMaintenanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductMaintenance extends EditRecord
{
    protected static string $resource = ProductMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
