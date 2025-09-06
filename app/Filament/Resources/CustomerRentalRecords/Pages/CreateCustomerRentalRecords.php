<?php

namespace App\Filament\Resources\CustomerRentalRecords\Pages;

use App\Filament\Resources\CustomerRentalRecords\CustomerRentalRecordsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerRentalRecords extends CreateRecord
{
    protected static string $resource = CustomerRentalRecordsResource::class;

    public static function canCreate(): bool
    {
        return false;
    }
}
