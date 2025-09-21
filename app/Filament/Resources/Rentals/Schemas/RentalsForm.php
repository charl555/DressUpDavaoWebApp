<?php

namespace App\Filament\Resources\Rentals\Schemas;

use App\Models\Customers;
use App\Models\Products;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RentalsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rental Information')
                    ->schema([
                        Select::make('product_id')
                            ->options(Products::where('status', 'Available')->pluck('name', 'product_id'))
                            ->label('Product Name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $product = Products::find($state);
                                if ($product) {
                                    $set('rental_price', $product->rental_price);
                                    $set('amount_paid', $product->rental_price);  // Auto-fill amount paid
                                } else {
                                    $set('rental_price', null);
                                    $set('amount_paid', null);
                                }
                            })
                            ->helperText('Only available products are shown'),
                        TextInput::make('rental_price')
                            ->label('Rental Price')
                            ->numeric()
                            ->required()
                            ->prefix('₱')
                            ->disabled(fn() => true)
                            ->dehydrated(true),
                        Select::make('customer_id')
                            ->options(Customers::all()->mapWithKeys(function ($customer) {
                                return [$customer->customer_id => $customer->first_name . ' ' . $customer->last_name];
                            }))
                            ->label('Customer')
                            ->required()
                            ->searchable()
                            ->helperText('Select the customer for this rental'),
                        DatePicker::make('pickup_date')
                            ->required()
                            ->minDate(now())
                            ->helperText('Date when customer will pick up the item'),
                        DatePicker::make('event_date')
                            ->required()
                            ->minDate(now())
                            ->helperText('Date of the event'),
                        DatePicker::make('return_date')
                            ->required()
                            ->minDate(now()->addDay())
                            ->helperText('Date when customer should return the item'),
                    ])
                    ->columnSpan(1),
                Section::make('Payment Information')
                    ->schema([
                        Select::make('payment_method')
                            ->options([
                                'Cash' => 'Cash',
                                'GCash' => 'GCash',
                                'PayMaya' => 'PayMaya',
                            ])
                            ->label('Payment Method')
                            ->required(),
                        TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->reactive()
                            ->helperText('Amount paid by the customer'),
                        DatePicker::make('payment_date')
                            ->default(now())
                            ->disabled(fn() => true)
                            ->dehydrated(true),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
