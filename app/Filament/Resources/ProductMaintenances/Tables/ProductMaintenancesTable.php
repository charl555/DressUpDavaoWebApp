<?php

namespace App\Filament\Resources\ProductMaintenances\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                SelectFilter::make('type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                SelectFilter::make('status')
                    ->options([
                        'Available' => 'Available',
                        'Rented' => 'Rented',
                        'Reserved' => 'Reserved',
                        'Maintenance' => 'Maintenance',
                    ])
                    ->label('Product Status')
                    ->placeholder('All Statuses'),
                SelectFilter::make('maintenance_needed')
                    ->options([
                        'Yes' => 'Needs Maintenance',
                        'No' => 'No Maintenance Needed',
                    ])
                    ->label('Maintenance Status')
                    ->placeholder('All'),
                SelectFilter::make('subtype')
                    ->label('Product Style')
                    ->placeholder('All Styles'),
                Filter::make('rental_count_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('rental_count_from')
                            ->numeric()
                            ->placeholder('Min Rental Count')
                            ->minValue(0),
                        \Filament\Forms\Components\TextInput::make('rental_count_to')
                            ->numeric()
                            ->placeholder('Max Rental Count')
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['rental_count_from'],
                                fn(Builder $query, $count): Builder => $query->where('rental_count', '>=', $count),
                            )
                            ->when(
                                $data['rental_count_to'],
                                fn(Builder $query, $count): Builder => $query->where('rental_count', '<=', $count),
                            );
                    })
                    ->label('Rental Count Range'),
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
