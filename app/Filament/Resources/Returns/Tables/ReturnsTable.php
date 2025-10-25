<?php

namespace App\Filament\Resources\Returns\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('pickup_date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('event_date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('actual_return_date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Booked' => 'info',
                        'Picked Up' => 'warning',
                        'Returned' => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Booked' => 'Booked',
                        'Picked Up' => 'Picked Up',
                        'Returned' => 'Returned',
                    ])
                    ->label('Return Status')
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
                Filter::make('return_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('return_from')
                            ->label('Return From')
                            ->placeholder('Select start date'),
                        \Filament\Forms\Components\DatePicker::make('return_until')
                            ->label('Return Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['return_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('return_date', '>=', $date),
                            )
                            ->when(
                                $data['return_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('return_date', '<=', $date),
                            );
                    })
                    ->label('Expected Return Date Range'),
                Filter::make('overdue_returns')
                    ->query(fn(Builder $query): Builder => $query->where('return_date', '<', now())->where('rental_status', 'Booked'))
                    ->toggle()
                    ->label('Overdue Returns Only'),
            ])
            ->actions([
                Action::make('markAsReturned')
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
