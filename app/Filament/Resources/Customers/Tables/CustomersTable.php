<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->copyMessageDuration(1500),
                TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('rentals_count')
                    ->label('Total Rentals')
                    ->counts('rentals')
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Filter::make('has_rentals')
                    ->query(fn(Builder $query): Builder => $query->has('rentals'))
                    ->toggle()
                    ->label('Has Rentals'),
                Filter::make('active_rentals')
                    ->query(fn(Builder $query): Builder => $query->whereHas('rentals', fn(Builder $query) => $query->where('rental_status', 'On Going')))
                    ->toggle()
                    ->label('Has Active Rentals'),
                Filter::make('phone_area')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('area_code')
                            ->label('Area Code')
                            ->placeholder('e.g., 09')
                            ->maxLength(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['area_code'],
                            fn(Builder $query, $code): Builder => $query->where('phone_number', 'like', $code . '%')
                        );
                    })
                    ->label('Phone Area Filter'),
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
