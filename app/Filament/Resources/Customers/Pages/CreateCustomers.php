<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomersResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomers extends CreateRecord
{
    protected static string $resource = CustomersResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
