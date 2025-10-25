<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Attach3dModelToProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->description('Product details for 3D model attachment')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->helperText('The name of the product that will have the 3D model attached')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g., Elegant Evening Gown')
                            ->rules(['required', 'string', 'max:100']),
                        Select::make('type')
                            ->label('Product Type')
                            ->helperText('Select the category of this product')
                            ->options([
                                'Gown' => 'Gown',
                                'Suit' => 'Suit',
                            ])
                            ->required()
                            ->placeholder('Choose product type')
                            ->rules(['required', 'in:Gown,Suit']),
                        TextInput::make('subtype')
                            ->label('Product Subtype')
                            ->helperText('Specific style or category (e.g., Ball Gown, Cocktail Dress)')
                            ->maxLength(50)
                            ->placeholder('e.g., Ball Gown, A-Line')
                            ->rules(['nullable', 'string', 'max:50']),
                    ])
                    ->columns(2),
            ]);
    }
}
