<?php

namespace App\Filament\Resources\ProductImages\Schemas;

use App\Models\Products;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductImagesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Image')
                    ->schema([
                        Select::make('product_id')
                            ->options(
                                Products::all()->pluck('name', 'product_id')
                            )
                            ->label('Product')
                            ->disabled()
                            ->required(),
                    ]),
                Section::make('Image')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail'),
                        FileUpload::make('image_path')
                            ->label('Image')
                            ->multiple(),
                    ]),
            ]);
    }
}
