<?php

namespace App\Filament\Resources\ProductMeasurements\Pages;

use App\Filament\Resources\ProductMeasurements\ProductMeasurementsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ListProductMeasurements extends ListRecords
{
    protected static string $resource = ProductMeasurementsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Gowns' => Tab::make()
                ->label('Gowns')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->whereHas('product', fn($q) => $q->where('type', 'Gown'))
                ),
            'Suits' => Tab::make()
                ->label('Suits')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->whereHas('product', fn($q) => $q->where('type', 'Suit'))
                ),
        ];
    }

    public function getTableColumns(): array
    {
        $activeTab = $this->activeTab;

        if ($activeTab === 'Gowns') {
            return [
                TextColumn::make('product.name')->label('Product Name'),
                TextColumn::make('gown_length')->label('Gown Length'),
                TextColumn::make('gown_upper_chest')->label('Gown Upper Chest'),
                TextColumn::make('gown_chest')->label('Gown Chest'),
                TextColumn::make('gown_waist')->label('Gown Waist'),
                TextColumn::make('gown_hips')->label('Gown Hips'),
            ];
        }

        if ($activeTab === 'Suits') {
            return [
                TextColumn::make('product.name')->label('Product Name'),
                TextColumn::make('jacket_chest')->label('Jacket Chest'),
                TextColumn::make('jacket_length')->label('Jacket Length'),
                TextColumn::make('jacket_shoulder')->label('Jacket Shoulder'),
                TextColumn::make('jacket_sleeve_length')->label('Jacket Sleeve Length'),
                TextColumn::make('jacket_sleeve_width')->label('Jacket Sleeve Width'),
                TextColumn::make('jacket_bicep')->label('Jacket Bicep'),
                TextColumn::make('jacket_arm_hole')->label('Jacket Arm Hole'),
                TextColumn::make('jacket_waist')->label('Jacket Waist'),
                TextColumn::make('trouser_waist')->label('Trouser Waist'),
                TextColumn::make('trouser_hip')->label('Trouser Hip'),
                TextColumn::make('trouser_inseam')->label('Trouser Inseam'),
                TextColumn::make('trouser_outseam')->label('Trouser Outseam'),
                TextColumn::make('trouser_thigh')->label('Trouser Thigh'),
                TextColumn::make('trouser_leg_opening')->label('Trouser Leg Opening'),
                TextColumn::make('trouser_crotch')->label('Trouser Crotch'),
            ];
        }

        // Default columns for the 'All' tab
        return [
            TextColumn::make('product.name')->label('Product Name'),
            TextColumn::make('product.type')->label('Product Type'),
            TextColumn::make('gown_length')->label('Gown Length'),
            TextColumn::make('gown_upper_chest')->label('Gown Upper Chest'),
            TextColumn::make('gown_chest')->label('Gown Chest'),
            TextColumn::make('gown_waist')->label('Gown Waist'),
            TextColumn::make('gown_hips')->label('Gown Hips'),
            TextColumn::make('jacket_chest')->label('Jacket Chest'),
            TextColumn::make('jacket_length')->label('Jacket Length'),
            TextColumn::make('jacket_shoulder')->label('Jacket Shoulder'),
            TextColumn::make('jacket_sleeve_length')->label('Jacket Sleeve Length'),
            TextColumn::make('jacket_sleeve_width')->label('Jacket Sleeve Width'),
            TextColumn::make('jacket_bicep')->label('Jacket Bicep'),
            TextColumn::make('jacket_arm_hole')->label('Jacket Arm Hole'),
            TextColumn::make('jacket_waist')->label('Jacket Waist'),
            TextColumn::make('trouser_waist')->label('Trouser Waist'),
            TextColumn::make('trouser_hip')->label('Trouser Hip'),
            TextColumn::make('trouser_inseam')->label('Trouser Inseam'),
            TextColumn::make('trouser_outseam')->label('Trouser Outseam'),
            TextColumn::make('trouser_thigh')->label('Trouser Thigh'),
            TextColumn::make('trouser_leg_opening')->label('Trouser Leg Opening'),
            TextColumn::make('trouser_crotch')->label('Trouser Crotch'),
        ];
    }
}
