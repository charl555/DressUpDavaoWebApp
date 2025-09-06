<?php

namespace App\Filament\Resources\Returns\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReturnsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable(),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->customer->first_name . ' ' . $record->customer->last_name;
                    }),
                TextColumn::make('pickup_date')->sortable(),
                TextColumn::make('event_date')->sortable(),
                TextColumn::make('return_date')->sortable(),
                TextColumn::make('actual_return_date'),
                TextColumn::make('rental_status')
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
                Action::make('Returned')
                    ->label('Mark as Returned')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->rental_status !== 'Returned')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_returned' => true,
                            'rental_status' => 'Returned',
                            'actual_return_date' => Carbon::now(),
                        ]);

                        $record->product->update(['status' => 'Available']);
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
