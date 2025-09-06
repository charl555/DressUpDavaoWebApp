<?php

namespace App\Filament\Resources\CustomerRentalRecords\Pages;

use App\Filament\Resources\CustomerRentalRecords\CustomerRentalRecordsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerRentalRecords extends EditRecord
{
    protected static string $resource = CustomerRentalRecordsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
