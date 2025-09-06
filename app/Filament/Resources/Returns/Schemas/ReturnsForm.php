<?php

namespace App\Filament\Resources\Returns\Schemas;

use App\Models\Customers;
use App\Models\Products;
use App\Models\Rentals;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReturnsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Return Information')
                    ->columns(2)
                    ->columnSpan(1)
                    ->schema([
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Select::make('product_id')
                                    ->options(Products::all()->pluck('name', 'product_id'))
                                    ->label('Product name')
                                    ->disabled(),
                                Select::make('customer_id')
                                    ->options(Customers::all()->pluck('first_name', 'customer_id'))
                                    ->label('Customer name')
                                    ->disabled(),
                                TextInput::make('rental_price')
                                    ->disabled(),
                            ]),
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('rental_date')
                                    ->disabled(),
                                TextInput::make('return_date')
                                    ->disabled(),
                                TextInput::make('penalty_amount')
                                    ->disabled(),
                            ]),
                        DatePicker::make('actual_return_date')
                            ->columnSpan(2)
                            ->required(),
                    ]),
            ])
            ->columns(2);
    }
}
