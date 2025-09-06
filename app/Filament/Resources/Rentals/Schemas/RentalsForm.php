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
                            ->afterStateUpdated(function ($state, callable $set) {
                                $product = Products::find($state);
                                if ($product) {
                                    $set('rental_price', $product->rental_price);
                                } else {
                                    $set('rental_price', null);
                                }
                            }),
                        TextInput::make('rental_price')
                            ->label('Rental Price')
                            ->numeric()
                            ->required()
                            ->prefix('₱')
                            ->disabled(fn() => true)
                            ->dehydrated(true),
                        Select::make('customer_id')
                            ->options(Customers::all()->pluck('first_name', 'customer_id'))
                            ->label('Customer')
                            ->required(),
                        DatePicker::make('pickup_date')->required(),
                        DatePicker::make('event_date')->required(),
                        DatePicker::make('return_date')->required(),
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
                            ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                            ->required(),
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
