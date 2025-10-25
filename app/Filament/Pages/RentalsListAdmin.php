<?php

namespace App\Filament\Pages;

use App\Models\Rentals;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class RentalsListAdmin extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected static ?string $title = 'Rentals List';

    protected string $view = 'filament.pages.rentals-list-admin';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Rentals::query()->with(['product', 'customer']))
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($record) =>
                        $record->customer
                            ? ($record->customer->first_name . ' ' . ($record->customer->last_name ?? ''))
                            : 'No Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pickup_date')
                    ->label('Pickup Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label('Event Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return Date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('rental_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Booked' => 'info',
                        'Picked Up' => 'warning',
                        'Returned' => 'success',
                        default => 'gray',
                    }),
            ]);
    }
}
