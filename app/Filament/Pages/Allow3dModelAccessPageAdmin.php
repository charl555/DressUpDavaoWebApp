<?php

namespace App\Filament\Pages;

use App\Models\Shops;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class Allow3dModelAccessPageAdmin extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected static ?string $navigationLabel = 'Allow 3D Model Access';
    protected static ?string $title = 'Allow 3D Model Access';
    protected static string|UnitEnum|null $navigationGroup = 'Shop Accounts';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.allow3d-model-access-page-admin';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Shops::query())
            ->columns([
                TextColumn::make('shop_name')
                    ->label('Shop Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('allow_3d_model_access')
                    ->label('3D Model Access')
                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->size('lg'),
            ])
            ->actions([
                Action::make('toggleAccess')
                    ->label('Toggle Access')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Toggle 3D Model Access')
                    ->color('primary')
                    ->modalDescription('Are you sure you want to toggle the 3D model access for this shop?')
                    ->action(function ($record) {
                        $record->update(['allow_3d_model_access' => !$record->allow_3d_model_access]);
                    }),
            ]);
    }
}
