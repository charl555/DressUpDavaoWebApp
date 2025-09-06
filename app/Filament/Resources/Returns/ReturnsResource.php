<?php

namespace App\Filament\Resources\Returns;

use App\Filament\Resources\Returns\Pages\CreateReturns;
use App\Filament\Resources\Returns\Pages\EditReturns;
use App\Filament\Resources\Returns\Pages\ListReturns;
use App\Filament\Resources\Returns\Schemas\ReturnsForm;
use App\Filament\Resources\Returns\Tables\ReturnsTable;
use App\Models\rentals;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ReturnsResource extends Resource
{
    protected static ?string $model = Rentals::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Rentals';

    protected static ?string $navigationLabel = 'Returns';

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
        return ReturnsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnsTable::configure($table);
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
            'index' => ListReturns::route('/'),
            'create' => CreateReturns::route('/create'),
            // 'edit' => EditReturns::route('/{record}/edit'),
        ];
    }
}
