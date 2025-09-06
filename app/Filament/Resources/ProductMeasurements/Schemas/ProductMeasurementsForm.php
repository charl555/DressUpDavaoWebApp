<?php

namespace App\Filament\Resources\ProductMeasurements\Schemas;

use App\Models\Products;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductMeasurementsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Product Information')
                    ->schema([
                        Select::make('product_id')
                            ->options(
                                Products::all()->pluck('name', 'product_id')
                            )
                            ->label('Product')
                            ->required(),
                    ]),
                Section::make('Gown Measurements in inches')
                    ->schema([
                        TextInput::make('gown_length')->numeric(),
                        TextInput::make('gown_upper_chest')->numeric(),
                        TextInput::make('gown_chest')->numeric(),
                        TextInput::make('gown_waist')->numeric(),
                        TextInput::make('gown_hips')->numeric(),
                    ])
                    ->visible(function ($get, $record) {
                        return optional($record?->product)->type === 'Gown';
                    }),
                Section::make('Jacket Measurements in inches')
                    ->schema([
                        TextInput::make('jacket_chest')->numeric(),
                        TextInput::make('jacket_length')->numeric(),
                        TextInput::make('jacket_shoulder')->numeric(),
                        TextInput::make('jacket_sleeve_length')->numeric(),
                        TextInput::make('jacket_sleeve_width')->numeric(),
                        TextInput::make('jacket_bicep')->numeric(),
                        TextInput::make('jacket_arm_hole')->numeric(),
                        TextInput::make('jacket_waist')->numeric(),
                    ])
                    ->visible(function ($get, $record) {
                        return optional($record?->product)->type === 'Suit';
                    }),
                Section::make('Trouser Measurements in inches')
                    ->schema([
                        TextInput::make('trouser_waist')->numeric(),
                        TextInput::make('trouser_hip')->numeric(),
                        TextInput::make('trouser_inseam')->numeric(),
                        TextInput::make('trouser_outseam')->numeric(),
                        TextInput::make('trouser_thigh')->numeric(),
                        TextInput::make('trouser_leg_opening')->numeric(),
                        TextInput::make('trouser_crotch')->numeric(),
                    ])
                    ->visible(function ($get, $record) {
                        return optional($record?->product)->type === 'Suit';
                    }),
            ]);
    }
}
