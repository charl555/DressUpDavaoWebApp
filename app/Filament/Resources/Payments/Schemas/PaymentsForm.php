<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Customers;
use App\Models\Rentals;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Information')
                    ->description('Payment details are automatically generated when a rental is created')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Select::make('rental_id')
                            ->options(Rentals::all()->pluck('rental_id', 'rental_id'))
                            ->label('Rental ID')
                            ->helperText('The rental ID associated with this payment')
                            ->disabled(),
                        Select::make('customer_id')
                            ->options(Customers::all()->pluck('first_name', 'customer_id'))
                            ->label('Customer')
                            ->helperText('Customer who made this payment')
                            ->disabled(),
                        TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->helperText('Method used for payment (Cash, GCash, PayMaya)')
                            ->disabled(),
                        TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                            ->helperText('Total amount paid for the rental')
                            ->disabled(),
                        TextInput::make('payment_date')
                            ->label('Payment Date')
                            ->helperText('Date when payment was processed')
                            ->disabled(),
                        TextInput::make('payment_status')
                            ->label('Payment Status')
                            ->helperText('Current status of the payment (Paid/Unpaid)')
                            ->disabled(),
                    ]),
            ]);
    }
}
