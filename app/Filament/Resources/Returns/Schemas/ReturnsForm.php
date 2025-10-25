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
                    ->description('Process the return of a rented product')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->columns(2)
                    ->columnSpan(1)
                    ->schema([
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                Select::make('product_id')
                                    ->options(Products::all()->pluck('name', 'product_id'))
                                    ->label('Product Name')
                                    ->helperText('The product being returned')
                                    ->disabled(),
                                Select::make('customer_id')
                                    ->options(Customers::all()->pluck('first_name', 'customer_id'))
                                    ->label('Customer Name')
                                    ->helperText('Customer returning the product')
                                    ->disabled(),
                                TextInput::make('rental_price')
                                    ->label('Rental Price')
                                    ->helperText('Original rental amount paid')
                                    ->prefix('₱')
                                    ->disabled(),
                            ]),
                        Group::make()
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('rental_date')
                                    ->label('Rental Date')
                                    ->helperText('Date when the product was rented')
                                    ->disabled(),
                                TextInput::make('return_date')
                                    ->label('Expected Return Date')
                                    ->helperText('Originally scheduled return date')
                                    ->disabled(),
                                TextInput::make('penalty_amount')
                                    ->label('Penalty Amount')
                                    ->helperText('Additional charges for late return (if any)')
                                    ->prefix('₱')
                                    ->disabled(),
                            ]),
                        DatePicker::make('actual_return_date')
                            ->label('Actual Return Date')
                            ->helperText('Select the actual date when the product is being returned')
                            ->columnSpan(2)
                            ->native(false)
                            ->displayFormat('F j, Y')
                            ->maxDate(now()->addDays(30))
                            ->minDate(now()->subDays(365))
                            ->required()
                            ->rules(['required', 'date', 'before_or_equal:today']),
                    ]),
            ])
            ->columns(2);
    }
}
