<?php

namespace App\Filament\Resources\CustomerRentalRecords\Pages;

use App\Filament\Resources\CustomerRentalRecords\CustomerRentalRecordsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerRentalRecords extends ListRecords
{
    protected static string $resource = CustomerRentalRecordsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
