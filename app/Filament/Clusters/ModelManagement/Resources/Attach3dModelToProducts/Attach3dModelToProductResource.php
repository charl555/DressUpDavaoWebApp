<?php

namespace App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts;

use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Pages\CreateAttach3dModelToProduct;
use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Pages\EditAttach3dModelToProduct;
use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Pages\ListAttach3dModelToProducts;
use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Schemas\Attach3dModelToProductForm;
use App\Filament\Clusters\ModelManagement\Resources\Attach3dModelToProducts\Tables\Attach3dModelToProductsTable;
use App\Filament\Clusters\ModelManagement\ModelManagementCluster;
use App\Models\Products;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class Attach3dModelToProductResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperClip;

    protected static ?string $cluster = ModelManagementCluster::class;

    protected static ?string $navigationLabel = 'Add 3D Model to Products';

    protected static ?int $navigationSort = 1;

    public static function getTitle(): string
    {
        return 'Attach 3D Model to Product';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->with(['product_images', 'product_3d_models']);
    }

    public static function getTableQuery(): Builder
    {
        return static::getEloquentQuery()
            ->with(['product_images', 'product_3d_models']);
    }

    public static function form(Schema $schema): Schema
    {
        return Attach3dModelToProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Attach3dModelToProductsTable::configure($table);
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
            'index' => ListAttach3dModelToProducts::route('/'),
            'create' => CreateAttach3dModelToProduct::route('/create'),
            'edit' => EditAttach3dModelToProduct::route('/{record}/edit'),
        ];
    }
}
