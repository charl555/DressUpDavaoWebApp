<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtype')
                    ->label('Style')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('occasions.occasion_name')
                    ->searchable()
                    ->label('Events'),
                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Available' => 'success',
                        'Rented' => 'warning',
                        'Reserved' => 'info',
                        'Maintenance' => 'danger',
                    }),
                TextColumn::make('colors'),
                TextColumn::make('size'),
                TextColumn::make('rental_price')
                    ->label('Rental Price')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
