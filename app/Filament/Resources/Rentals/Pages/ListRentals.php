<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRentals extends ListRecords
{
    protected static string $resource = RentalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add New Rental'),
        ];
    }
}
