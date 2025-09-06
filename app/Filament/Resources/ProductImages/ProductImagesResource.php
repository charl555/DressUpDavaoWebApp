<?php

namespace App\Filament\Resources\ProductImages;

use App\Filament\Resources\ProductImages\Pages\CreateProductImages;
use App\Filament\Resources\ProductImages\Pages\EditProductImages;
use App\Filament\Resources\ProductImages\Pages\ListProductImages;
use App\Filament\Resources\ProductImages\Schemas\ProductImagesForm;
use App\Filament\Resources\ProductImages\Tables\ProductImagesTable;
use App\Models\ProductImages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ProductImagesResource extends Resource
{
    protected static ?string $model = ProductImages::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Products';

    protected static ?string $navigationLabel = 'Product Image';

    protected static ?int $navigationSort = 3;

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
        return ProductImagesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductImagesTable::configure($table);
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
            'index' => ListProductImages::route('/'),
            'create' => CreateProductImages::route('/create'),
            'edit' => EditProductImages::route('/{record}/edit'),
        ];
    }
}
