<?php

namespace App\Filament\Resources\CustomerRentalRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerRentalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.customer_id')->label('Customer ID'),
                TextColumn::make('customer.first_name')->label('Customer')->searchable(),
                TextColumn::make('product.name')->label('Product Name')->searchable(),
                TextColumn::make('pickup_date')->label('Pickup Date')->sortable(),
                TextColumn::make('event_date')->label('Event Date')->sortable(),
                TextColumn::make('return_date')->label('Return Date')->sortable(),
                TextColumn::make('rental_status')
                    ->label('Rental Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'On Going' => 'info',
                        'Returned' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
