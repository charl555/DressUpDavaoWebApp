<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Pages;

use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Attach3dModelToProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttach3dModelToProducts extends ListRecords
{
    protected static string $resource = Attach3dModelToProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
