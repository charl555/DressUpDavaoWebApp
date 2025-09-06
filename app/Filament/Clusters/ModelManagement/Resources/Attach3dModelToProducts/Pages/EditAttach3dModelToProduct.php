<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Pages;

use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Attach3dModelToProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttach3dModelToProduct extends EditRecord
{
    protected static string $resource = Attach3dModelToProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
