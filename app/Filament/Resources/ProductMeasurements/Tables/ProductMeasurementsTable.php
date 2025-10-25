<?php

namespace App\Filament\Resources\ProductMeasurements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductMeasurementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Product Name'),
                TextColumn::make('product.type')->label('Product Type'),
                TextColumn::make('product.subtype')->label('Style'),
                // Gown combined column
                TextColumn::make('gown')
                    ->label('Gown Measurements')
                    ->getStateUsing(function ($record) {
                        return collect([
                            'Length' => $record->gown_length,
                            'Upper Chest' => $record->gown_upper_chest,
                            'Chest' => $record->gown_chest,
                            'Waist' => $record->gown_waist,
                            'Hips' => $record->gown_hips,
                        ])
                            ->filter()  // remove null values
                            ->map(fn($v, $k) => "$k: $v")
                            ->implode(', ');
                    })
                    ->wrap()
                    ->toggleable(),
                // Jacket combined column
                TextColumn::make('jacket')
                    ->label('Jacket Measurements')
                    ->getStateUsing(function ($record) {
                        return collect([
                            'Chest' => $record->jacket_chest,
                            'Length' => $record->jacket_length,
                            'Shoulder' => $record->jacket_shoulder,
                            'Sleeve Length' => $record->jacket_sleeve_length,
                            'Sleeve Width' => $record->jacket_sleeve_width,
                            'Bicep' => $record->jacket_bicep,
                            'Arm Hole' => $record->jacket_arm_hole,
                            'Waist' => $record->jacket_waist,
                        ])
                            ->filter()
                            ->map(fn($v, $k) => "$k: $v")
                            ->implode(', ');
                    })
                    ->wrap()
                    ->toggleable(),
                // Trouser combined column
                TextColumn::make('trouser')
                    ->label('Trouser Measurements')
                    ->getStateUsing(function ($record) {
                        return collect([
                            'Waist' => $record->trouser_waist,
                            'Hip' => $record->trouser_hip,
                            'Inseam' => $record->trouser_inseam,
                            'Outseam' => $record->trouser_outseam,
                            'Thigh' => $record->trouser_thigh,
                            'Leg Opening' => $record->trouser_leg_opening,
                            'Crotch' => $record->trouser_crotch,
                        ])
                            ->filter()
                            ->map(fn($v, $k) => "$k: $v")
                            ->implode(', ');
                    })
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('product.type')
                    ->relationship('product', 'type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                SelectFilter::make('product.status')
                    ->relationship('product', 'status')
                    ->options([
                        'Available' => 'Available',
                        'Rented' => 'Rented',
                        'Reserved' => 'Reserved',
                        'Maintenance' => 'Maintenance',
                    ])
                    ->label('Product Status')
                    ->placeholder('All Statuses'),
                SelectFilter::make('product.subtype')
                    ->relationship('product', 'subtype')
                    ->label('Product Style')
                    ->placeholder('All Styles'),
                SelectFilter::make('has_measurements')
                    ->options([
                        'complete' => 'Complete Measurements',
                        'partial' => 'Partial Measurements',
                        'none' => 'No Measurements',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'complete') {
                            return $query->where(function ($q) {
                                $q->where(function ($gownQuery) {
                                    $gownQuery
                                        ->whereHas('product', fn($productQuery) => $productQuery->where('type', 'Gown'))
                                        ->whereNotNull('gown_length')
                                        ->whereNotNull('gown_chest')
                                        ->whereNotNull('gown_waist')
                                        ->whereNotNull('gown_hips');
                                })->orWhere(function ($suitQuery) {
                                    $suitQuery
                                        ->whereHas('product', fn($productQuery) => $productQuery->where('type', 'Suit'))
                                        ->whereNotNull('jacket_chest')
                                        ->whereNotNull('jacket_length')
                                        ->whereNotNull('trouser_waist')
                                        ->whereNotNull('trouser_inseam');
                                });
                            });
                        } elseif ($data['value'] === 'none') {
                            return $query->where(function ($q) {
                                $q
                                    ->whereNull('gown_length')
                                    ->whereNull('gown_chest')
                                    ->whereNull('jacket_chest')
                                    ->whereNull('trouser_waist');
                            });
                        }
                        return $query;
                    })
                    ->label('Measurement Status')
                    ->placeholder('All'),
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
