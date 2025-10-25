<?php

namespace App\Filament\Resources\ProductMeasurements\Schemas;

use App\Models\Products;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductMeasurementsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Gown Measurements (inches)')
                    ->description('Enter precise measurements for gown products. All measurements should be in inches.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('gown_length')
                                    ->label('Gown Length')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 60'),
                                TextInput::make('gown_upper_chest')
                                    ->label('Upper Chest/Bust')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 36'),
                                TextInput::make('gown_bust')
                                    ->label('Bust')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 38'),
                                TextInput::make('gown_chest')
                                    ->label('Chest')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 36'),
                                TextInput::make('gown_shoulder')
                                    ->label('Shoulder')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 16'),
                                TextInput::make('gown_neck')
                                    ->label('Neck')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(20)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 14'),
                            ])
                            ->columns(3),
                        Group::make()
                            ->schema([
                                TextInput::make('gown_waist')
                                    ->label('Waist')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 28'),
                                TextInput::make('gown_hips')
                                    ->label('Hips')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 40'),
                                TextInput::make('gown_back_width')
                                    ->label('Back Width')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 14'),
                                TextInput::make('gown_figure')
                                    ->label('Figure')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 38'),
                                TextInput::make('gown_arm_hole')
                                    ->label('Arm Hole')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 18'),
                                TextInput::make('gown_bust_point')
                                    ->label('Bust Point')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(20)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 10'),
                            ])
                            ->columns(3),
                        Group::make()
                            ->schema([
                                TextInput::make('gown_bust_distance')
                                    ->label('Bust Distance')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(15)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 8'),
                                TextInput::make('gown_sleeve_width')
                                    ->label('Sleeve Width')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(20)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 6'),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn($record) => optional($record?->product)->type === 'Gown'),
                Section::make('Jacket Measurements (inches)')
                    ->description('Enter precise measurements for the jacket/blazer part of the suit')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('jacket_chest')
                                    ->label('Jacket Chest')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 42'),
                                TextInput::make('jacket_bust')
                                    ->label('Jacket Bust')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 40'),
                                TextInput::make('jacket_shoulder')
                                    ->label('Shoulder Width')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 18'),
                                TextInput::make('jacket_sleeve_length')
                                    ->label('Sleeve Length')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(40)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 25'),
                            ])
                            ->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('jacket_sleeve_width')
                                    ->label('Sleeve Width')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(20)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 6'),
                                TextInput::make('jacket_bicep')
                                    ->label('Bicep')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(25)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 14'),
                                TextInput::make('jacket_arm_hole')
                                    ->label('Arm Hole')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 20'),
                                TextInput::make('jacket_waist')
                                    ->label('Jacket Waist')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 36'),
                            ])
                            ->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('jacket_hip')
                                    ->label('Jacket Hip')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 44'),
                                TextInput::make('jacket_back_width')
                                    ->label('Back Width')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 16'),
                                TextInput::make('jacket_length')
                                    ->label('Jacket Length')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 30'),
                                TextInput::make('jacket_figure')
                                    ->label('Jacket Figure')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 42'),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn($record) => optional($record?->product)->type === 'Suit'),
                Section::make('Trouser Measurements (inches)')
                    ->description('Enter precise measurements for the trouser/pants part of the suit')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('trouser_waist')
                                    ->label('Trouser Waist')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 32'),
                                TextInput::make('trouser_hip')
                                    ->label('Trouser Hip')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 40'),
                                TextInput::make('trouser_inseam')
                                    ->label('Inseam')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 32'),
                                TextInput::make('trouser_outseam')
                                    ->label('Outseam')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 42'),
                                TextInput::make('trouser_thigh')
                                    ->label('Thigh')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(40)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 24'),
                            ])
                            ->columns(3),
                        Group::make()
                            ->schema([
                                TextInput::make('trouser_knee')
                                    ->label('Knee')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(30)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 16'),
                                TextInput::make('trouser_bottom')
                                    ->label('Bottom')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(25)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 14'),
                                TextInput::make('trouser_leg_opening')
                                    ->label('Leg Opening')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(20)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 8'),
                                TextInput::make('trouser_crotch')
                                    ->label('Crotch')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(40)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 28'),
                                TextInput::make('trouser_length')
                                    ->label('Trouser Length')
                                    ->numeric()
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->maxValue(60)
                                    ->suffix('inches')
                                    ->placeholder('e.g., 42'),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn($record) => optional($record?->product)->type === 'Suit'),
            ])
            ->columns(1);
    }
}
