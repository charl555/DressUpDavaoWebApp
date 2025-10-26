<?php

namespace App\Filament\Resources\ProductImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductImagesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Product Images')
                    ->description('Upload high-quality images to showcase your product')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Group::make()
                            ->schema([
                                FileUpload::make('thumbnail_image')
                                    ->label('Main Thumbnail Image')
                                    ->helperText('Upload the main image that will be displayed as the product thumbnail. Recommended size: 800x800px')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('product-images/thumbnails')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                        '4:3',
                                        '16:9',
                                    ])
                                    ->maxSize(5120)  // 5MB
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->deletable(true)
                                    ->openable()
                                    ->required()
                                    ->default(function ($livewire) {
                                        // Only set default when editing
                                        if (method_exists($livewire, 'getRecord') && $record = $livewire->getRecord()) {
                                            if ($record->thumbnail_image) {
                                                return [asset('storage/' . $record->thumbnail_image)];
                                            }
                                        }
                                        return null;
                                    })
                                    ->columnSpan(1),
                            ])
                            ->columns(1),
                        Group::make()
                            ->schema([
                                FileUpload::make('images')
                                    ->label('Additional Gallery Images')
                                    ->helperText('Upload additional images to show different angles and details of your product. Maximum 10 images, 5MB each')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('product-images')
                                    ->image()
                                    ->imageEditor()
                                    ->multiple()
                                    ->maxFiles(10)
                                    ->maxSize(5120)  // 5MB per file
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->deletable(true)
                                    ->openable()
                                    ->reorderable()
                                    ->default(function ($livewire) {
                                        // Only set default when editing
                                        if (method_exists($livewire, 'getRecord') && $record = $livewire->getRecord()) {
                                            if ($record->images && is_array($record->images)) {
                                                return array_map(function ($imagePath) {
                                                    return asset('storage/' . $imagePath);
                                                }, $record->images);
                                            }
                                        }
                                        return null;
                                    })
                                    ->columnSpan(1),
                            ])
                            ->columns(1),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
