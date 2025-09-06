<?php

namespace App\Filament\Resources\CustomerRentalRecords;

use App\Filament\Resources\CustomerRentalRecords\Pages\CreateCustomerRentalRecords;
use App\Filament\Resources\CustomerRentalRecords\Pages\EditCustomerRentalRecords;
use App\Filament\Resources\CustomerRentalRecords\Pages\ListCustomerRentalRecords;
use App\Filament\Resources\CustomerRentalRecords\Schemas\CustomerRentalRecordsForm;
use App\Filament\Resources\CustomerRentalRecords\Tables\CustomerRentalRecordsTable;
use App\Models\Rentals;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class CustomerRentalRecordsResource extends Resource
{
    protected static ?string $model = Rentals::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Customer Management';

    protected static ?string $navigationLabel = 'Customer Rental Records';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('customer', function ($query) {
                $query->where('user_id', auth()->id());
            });
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerRentalRecordsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerRentalRecordsTable::configure($table);
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
            'index' => ListCustomerRentalRecords::route('/'),
            'create' => CreateCustomerRentalRecords::route('/create'),
            // 'edit' => EditCustomerRentalRecords::route('/{record}/edit'),
        ];
    }
}
