<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProducts;
use App\Filament\Resources\Products\Pages\EditProducts;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductsForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Products;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['occasions', 'product_images', 'product_measurements', 'user']);
    }

    public static function getTableQuery(): Builder
    {
        return static::getEloquentQuery()
            ->with(['occasions', 'product_images', 'product_measurements', 'user']);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProducts::route('/create'),
            'edit' => EditProducts::route('/{record}/edit'),
        ];
    }
}
