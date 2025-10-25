<?php

namespace App\Filament\Resources\CustomerRentalRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerRentalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.customer_id')
                    ->label('Customer ID'),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pickup_date')
                    ->label('Pickup Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label('Event Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('rental_status')
                    ->label('Rental Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'On Going' => 'info',
                        'Returned' => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('rental_status')
                    ->options([
                        'On Going' => 'On Going',
                        'Returned' => 'Returned',
                    ])
                    ->label('Rental Status')
                    ->placeholder('All Statuses'),
                SelectFilter::make('product.type')
                    ->relationship('product', 'type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                Filter::make('pickup_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('pickup_from')
                            ->label('Pickup From')
                            ->placeholder('Select start date'),
                        \Filament\Forms\Components\DatePicker::make('pickup_until')
                            ->label('Pickup Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['pickup_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('pickup_date', '>=', $date),
                            )
                            ->when(
                                $data['pickup_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('pickup_date', '<=', $date),
                            );
                    })
                    ->label('Pickup Date Range'),
                Filter::make('event_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('event_from')
                            ->label('Event From')
                            ->placeholder('Select start date'),
                        \Filament\Forms\Components\DatePicker::make('event_until')
                            ->label('Event Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['event_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '>=', $date),
                            )
                            ->when(
                                $data['event_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '<=', $date),
                            );
                    })
                    ->label('Event Date Range'),
                Filter::make('current_rentals')
                    ->query(fn(Builder $query): Builder => $query->where('rental_status', 'On Going'))
                    ->toggle()
                    ->label('Current Rentals Only'),
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
