<?php

namespace App\Filament\Resources\ProductMaintenances\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductMaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Product Name'),
                TextColumn::make('type')->label('Product Type'),
                TextColumn::make('subtype')->label('Style'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Available' => 'success',
                        'Rented' => 'warning',
                        'Reserved' => 'info',
                        'Maintenance' => 'danger',
                    })
                    ->label('Product Status'),
                TextColumn::make('rental_count')->label('Rental Count'),
                TextColumn::make('maintenance_needed')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Yes' => 'danger',
                        'No' => 'success',
                    })
                    ->label('Maintenance Needed')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('ToggleMaintenance')
                    ->label(fn($record) => $record->maintenance_needed === 'Yes' ? 'Unmark for Maintenance' : 'Mark For Maintenance')
                    ->icon(fn($record) => $record->maintenance_needed === 'Yes' ? 'heroicon-o-check' : 'heroicon-o-exclamation-triangle')
                    ->color(fn($record) => $record->maintenance_needed === 'Yes' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'maintenance_needed' => $record->maintenance_needed === 'Yes' ? 'No' : 'Yes',
                        ]);
                    }),
                // EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
