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
                Section::make('Product Images')
                    ->schema([
                        Select::make('product_id')
                            ->options(
                                Products::where('user_id', auth()->id())->pluck('name', 'product_id')
                            )
                            ->label('Product')
                            ->disabled()
                            ->required(),
                    ]),
                Section::make('Images')
                    ->schema([
                        FileUpload::make('thumbnail_image')
                            ->label('Thumbnail')
                            ->disk('public')
                            ->directory('product-images')
                            ->image()
                            ->preserveFilenames()
                            ->deletable(true)
                            ->openable(),
                        FileUpload::make('images')
                            ->label('Images')
                            ->disk('public')
                            ->directory('product-images')
                            ->image()
                            ->multiple()
                            ->preserveFilenames()
                            ->deletable(true)
                            ->openable(),
                    ]),
            ]);
    }
}
