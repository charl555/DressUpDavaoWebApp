<?php

namespace App\Filament\Resources\ProductMaintenances;

use App\Filament\Resources\ProductMaintenances\Pages\CreateProductMaintenance;
use App\Filament\Resources\ProductMaintenances\Pages\EditProductMaintenance;
use App\Filament\Resources\ProductMaintenances\Pages\ListProductMaintenances;
use App\Filament\Resources\ProductMaintenances\Schemas\ProductMaintenanceForm;
use App\Filament\Resources\ProductMaintenances\Tables\ProductMaintenancesTable;
use App\Models\Products;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ProductMaintenanceResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Products';

    protected static ?string $navigationLabel = 'Product Maintenance';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', function ($query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductMaintenanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductMaintenancesTable::configure($table);
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
            'index' => ListProductMaintenances::route('/'),
            'create' => CreateProductMaintenance::route('/create'),
            // 'edit' => EditProductMaintenance::route('/{record}/edit'),
        ];
    }
}
