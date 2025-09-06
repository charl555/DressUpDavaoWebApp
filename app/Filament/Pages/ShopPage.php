<?php

namespace App\Filament\Pages;

use App\Models\Products;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class ShopPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $slug = 'shop-page';

    protected string $view = 'filament.pages.shop-page';

    public static ?string $Title = 'Shop Page';

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function getTableQuery(): Builder
    {
        return Products::query()->where('user_id', auth()->id());
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('viewStore')
                ->label('View Store Page')
                ->icon('heroicon-o-eye'),
            Action::make('addProduct')
                ->label('Add products to shop')
                ->icon('heroicon-o-plus')
                ->url(AddProductToShopPage::getUrl()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Gowns' => Tab::make()
                ->label('Gowns')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('type', 'Gown')
                ),
            'Suits' => Tab::make()
                ->label('Suits')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('type', 'Suit')
                ),
        ];
    }

    public function getTitle(): string
    {
        return 'Shop Products';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Products::query()->where('user_id', auth()->id()))
            ->modifyQueryUsing(fn(Builder $query) => $query->where('visibility', 'Yes'))
            ->columns([
                TextColumn::make('name')->label('Product Name')->searchable(),
                TextColumn::make('type')->label('Product Type')->searchable(),
                TextColumn::make('subtype')->label('Style')->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Available' => 'success',
                        'Rented' => 'warning',
                        'Reserved' => 'info',
                        'Maintenance' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->form(function (Products $record): array {
                        return [
                            TextInput::make('name')->label('Product Name')->disabled(),
                            TextInput::make('type')->label('Product Type')->disabled(),
                            TextInput::make('subtype')->label('Style')->disabled(),
                            TextArea::make('description')->label('Description'),
                            TextArea::make('inclusions')->label('Inclusions'),
                        ];
                    }),
                ViewAction::make()
                    ->form(function (Products $record): array {
                        return [
                            TextInput::make('name')->label('Product Name'),
                            TextInput::make('type')->label('Product Type'),
                            TextInput::make('subtype')->label('Style'),
                            TextArea::make('description')->label('Description'),
                            TextArea::make('inclusions')->label('Inclusions'),
                        ];
                    }),
                Action::make('Remove from shop')
                    ->label('Remove from shop')
                    ->icon('heroicon-o-minus')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to remove this product from your shop?')
                    ->action(function ($record) {
                        $record->update([
                            'visibility' => 'No',
                        ]);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
