<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Product Information')
                    ->schema([
                        TextInput::make('name')->required(),
                        Select::make('type')
                            ->options([
                                'Gown' => 'Gown',
                                'Suit' => 'Suit',
                            ])
                            ->required()
                            ->reactive(),
                        Select::make('subtype')
                            ->label('Style')
                            ->options(function ($get) {
                                if ($get('type') === 'Gown') {
                                    return [
                                        'Wedding Gown' => 'Wedding Gown',
                                        'Ball Gown' => 'Ball Gown',
                                        'Night Gown' => 'Night Gown',
                                        'Cocktail Gown' => 'Cocktail Gown',
                                        'A-line Gown' => 'A-line Gown',
                                        'Sheath Gown' => 'Sheath Gown',
                                        'Mermaid Gown' => 'Mermaid Gown',
                                        'Off-shoulder Gown' => 'Off-shoulder Gown',
                                        'Princess Gown' => 'Princess Gown',
                                        'Empire Waist Gown' => 'Empire Waist Gown',
                                        'V-neck Gown' => 'V-neck Gown',
                                        'Trumpet Gown' => 'Trumpet Gown',
                                        'Other' => 'Other',
                                    ];
                                } elseif ($get('type') === 'Suit') {
                                    return [
                                        'Tuxedo' => 'Tuxedo',
                                        'Two Piece Suit' => 'Two Piece Suit',
                                        'Three Piece Suit' => 'Three Piece Suit',
                                        'Italian Suit' => 'Italian Suit',
                                        'Single Breasted Suit' => 'Single Breasted Suit',
                                        'Double Breasted Suit' => 'Double Breasted Suit',
                                        'Other' => 'Other',
                                    ];
                                }
                            })
                            ->required(),
                        TagsInput::make('occasions')
                            ->label('Events')
                            ->separator(',')
                            ->suggestions([
                                'Wedding',
                                'Debut',
                                'Prom',
                                'Formal',
                                'Casual',
                                'Corporate',
                                'Birthday',
                            ])
                            ->required(),
                        TextInput::make('colors')->required(),
                        Select::make('size')
                            ->options([
                                'Small' => 'Small',
                                'Medium' => 'Medium',
                                'Large' => 'Large',
                                'XLarge' => 'XLarge',
                                'XXLarge' => 'XXLarge',
                            ])
                            ->required(),
                        TextInput::make('rental_price')
                            ->numeric()
                            ->required()
                            ->label('Rental Price')
                            ->prefix('₱'),
                    ])
                    ->columns(1),
                Section::make('Product Images')
                    ->hiddenOn('edit')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail Image')
                            ->disk('public')
                            ->directory('product-images'),
                        FileUpload::make('image_path')
                            ->label('Product images')
                            ->multiple()
                            ->disk('public')
                            ->directory('product-images'),
                    ]),
                Section::make('Product Measurements')
                    ->hiddenOn('edit')
                    ->schema([
                        Section::make('Gown Measurements in inches')
                            ->visible(fn($get) => $get('type') === 'Gown')
                            ->schema([
                                TextInput::make('gown_length')->numeric(),
                                TextInput::make('gown_upper_chest')->numeric(),
                                TextInput::make('gown_chest')->numeric(),
                                TextInput::make('gown_waist')->numeric(),
                                TextInput::make('gown_hips')->numeric(),
                            ]),
                        Section::make('Jacket Measurements in inches')
                            ->visible(fn($get) => $get('type') === 'Suit')
                            ->schema([
                                TextInput::make('jacket_chest')->numeric(),
                                TextInput::make('jacket_length')->numeric(),
                                TextInput::make('jacket_shoulder')->numeric(),
                                TextInput::make('jacket_sleeve_length')->numeric(),
                                TextInput::make('jacket_sleeve_width')->numeric(),
                                TextInput::make('jacket_bicep')->numeric(),
                                TextInput::make('jacket_arm_hole')->numeric(),
                                TextInput::make('jacket_waist')->numeric(),
                            ]),
                        Section::make('Trouser Measurements in inches')
                            ->visible(fn($get) => $get('type') === 'Suit')
                            ->schema([
                                TextInput::make('trouser_waist')->numeric(),
                                TextInput::make('trouser_hip')->numeric(),
                                TextInput::make('trouser_inseam')->numeric(),
                                TextInput::make('trouser_outseam')->numeric(),
                                TextInput::make('trouser_thigh')->numeric(),
                                TextInput::make('trouser_leg_opening')->numeric(),
                                TextInput::make('trouser_crotch')->numeric(),
                            ]),
                    ]),
            ]);
    }
}
