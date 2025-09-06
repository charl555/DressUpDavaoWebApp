<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Customers;
use App\Models\Rentals;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rental_id')
                    ->options(Rentals::all()->pluck('rental_id', 'rental_id'))
                    ->label('Rental ID')
                    ->disabled(),
                Select::make('customer_id')
                    ->options(Customers::all()->pluck('first_name', 'customer_id'))
                    ->label('Customer')
                    ->disabled(),
                TextInput::make('payment_method')->disabled(),
                TextInput::make('amount_paid')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                    ->disabled(),
                TextInput::make('payment_date')->disabled(),
                TextInput::make('payment_status')->disabled(),
            ]);
    }
}
