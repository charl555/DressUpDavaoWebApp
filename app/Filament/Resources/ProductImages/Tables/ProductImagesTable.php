<?php

namespace App\Filament\Resources\ProductImages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductImagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Product Name'),
                ImageColumn::make('thumbnail_image')
                    ->label('Thumbnail Image')
                    ->disk('public'),
                ImageColumn::make('images')
                    ->label('Gallery Images')
                    ->disk('public'),
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
                SelectFilter::make('has_thumbnail')
                    ->options([
                        '1' => 'Has Thumbnail',
                        '0' => 'No Thumbnail',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('thumbnail_image');
                        } elseif ($data['value'] === '0') {
                            return $query->whereNull('thumbnail_image');
                        }
                        return $query;
                    })
                    ->label('Thumbnail Status')
                    ->placeholder('All'),
                SelectFilter::make('has_gallery')
                    ->options([
                        '1' => 'Has Gallery Images',
                        '0' => 'No Gallery Images',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === '1') {
                            return $query->whereNotNull('images');
                        } elseif ($data['value'] === '0') {
                            return $query->whereNull('images');
                        }
                        return $query;
                    })
                    ->label('Gallery Status')
                    ->placeholder('All'),
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
