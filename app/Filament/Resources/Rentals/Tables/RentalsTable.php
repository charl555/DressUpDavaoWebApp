<?php

namespace App\Filament\Resources\Rentals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RentalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Product Name'),
                TextColumn::make('customer.first_name')->label('Customer'),
                TextColumn::make('rental_date'),
                TextColumn::make('return_date'),
                TextColumn::make('rental_price')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
                TextColumn::make('rental_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'On Going' => 'info',
                        'Returned' => 'success',
                    }),
                TextColumn::make('payments.payment_method')->label('Payment Method'),
                TextColumn::make('payments.amount_paid')
                    ->label('Amount Paid')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
                TextColumn::make('payments.payment_date')->label('Payment Date'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
