<?php

namespace App\Filament\Resources\ProductMeasurements;

use App\Filament\Resources\ProductMeasurements\Pages\CreateProductMeasurements;
use App\Filament\Resources\ProductMeasurements\Pages\EditProductMeasurements;
use App\Filament\Resources\ProductMeasurements\Pages\ListProductMeasurements;
use App\Filament\Resources\ProductMeasurements\Schemas\ProductMeasurementsForm;
use App\Filament\Resources\ProductMeasurements\Tables\ProductMeasurementsTable;
use App\Models\ProductMeasurements;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ProductMeasurementsResource extends Resource
{
    protected static ?string $model = ProductMeasurements::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Products';

    protected static ?string $navigationLabel = 'Product Measurements';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('product', function ($query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductMeasurementsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductMeasurementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductMeasurements::route('/'),
            'create' => CreateProductMeasurements::route('/create'),
            'edit' => EditProductMeasurements::route('/{record}/edit'),
        ];
    }
}
