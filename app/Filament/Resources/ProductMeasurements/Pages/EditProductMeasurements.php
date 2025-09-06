<?php

namespace App\Filament\Resources\ProductMeasurements\Pages;

use App\Filament\Resources\ProductMeasurements\ProductMeasurementsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductMeasurements extends EditRecord
{
    protected static string $resource = ProductMeasurementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
