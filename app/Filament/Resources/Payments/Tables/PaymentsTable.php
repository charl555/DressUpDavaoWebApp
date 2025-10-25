<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rental_id')
                    ->label('Rental ID'),
                TextColumn::make('payment_method')
                    ->label('Payment Method'),
                TextColumn::make('amount_paid')
                    ->label('Amount Paid')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
                TextColumn::make('payment_date')
                    ->date('F j, Y')
                    ->label('Payment Date'),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Unpaid' => 'danger',
                    })
                    ->label('Payment Status'),
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->options([
                        'Cash' => 'Cash',
                        'GCash' => 'GCash',
                        'PayMaya' => 'PayMaya',
                        'Bank Transfer' => 'Bank Transfer',
                        'Credit Card' => 'Credit Card',
                    ])
                    ->label('Payment Method')
                    ->placeholder('All Payment Methods'),
                SelectFilter::make('payment_status')
                    ->options([
                        'Paid' => 'Paid',
                        'Unpaid' => 'Unpaid',
                        'Pending' => 'Pending',
                        'Refunded' => 'Refunded',
                    ])
                    ->label('Payment Status')
                    ->placeholder('All Statuses'),
                Filter::make('payment_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('payment_from')
                            ->label('Payment From')
                            ->placeholder('Select start date'),
                        \Filament\Forms\Components\DatePicker::make('payment_until')
                            ->label('Payment Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['payment_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    })
                    ->label('Payment Date Range'),
                Filter::make('amount_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_from')
                            ->numeric()
                            ->placeholder('Min Amount')
                            ->prefix('₱'),
                        \Filament\Forms\Components\TextInput::make('amount_to')
                            ->numeric()
                            ->placeholder('Max Amount')
                            ->prefix('₱'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn(Builder $query, $amount): Builder => $query->where('amount_paid', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn(Builder $query, $amount): Builder => $query->where('amount_paid', '<=', $amount),
                            );
                    })
                    ->label('Amount Range'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
