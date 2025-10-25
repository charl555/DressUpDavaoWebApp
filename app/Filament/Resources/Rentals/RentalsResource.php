<?php

namespace App\Filament\Resources\Rentals;

use App\Filament\Resources\Rentals\Pages\CreateRentals;
use App\Filament\Resources\Rentals\Pages\EditRentals;
use App\Filament\Resources\Rentals\Pages\ListRentals;
use App\Filament\Resources\Rentals\Schemas\RentalsForm;
use App\Filament\Resources\Rentals\Tables\RentalsTable;
use App\Models\Rentals;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class RentalsResource extends Resource
{
    protected static ?string $model = Rentals::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Rental Management';

    protected static ?string $navigationLabel = 'Rentals';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('product', function ($query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function getTableQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('product', function ($query) {
                $query->where('user_id', auth()->id());
            })
            // Eager load relationships for the table columns
            ->with(['product', 'customer', 'payments']);
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return RentalsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalsTable::configure($table);
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
            'index' => ListRentals::route('/'),
            'create' => CreateRentals::route('/create'),
            'edit' => EditRentals::route('/{record}/edit'),
        ];
    }
}
