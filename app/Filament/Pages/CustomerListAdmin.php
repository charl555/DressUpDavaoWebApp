<?php

namespace App\Filament\Pages;

use App\Models\Rentals;
use App\Models\User;
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

class CustomerListAdmin extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Records';

    protected string $view = 'filament.pages.customer-list-admin';

    protected static ?string $title = 'Customer List';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('address')
                //     ->label('Address')
                //     ->searchable()
                //     ->limit(50)
                //     ->tooltip(function (TextColumn $column): ?string {
                //         $state = $column->getState();
                //         if (strlen($state) <= 50) {
                //             return null;
                //         }
                //         return $state;
                //     }),
                TextColumn::make('rentals_count')
                    ->label('Total Rentals')
                    ->counts('rentals')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('overdue_returns')
                    ->label('Overdue Returns')
                    ->counts('rentals')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
            ]);
    }
}
