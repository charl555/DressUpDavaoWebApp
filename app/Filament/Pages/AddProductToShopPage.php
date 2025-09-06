<?php

namespace App\Filament\Pages;

use App\Models\Products;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AddProductToShopPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $slug = 'add-product-to-shop-page';
    protected string $view = 'filament.pages.add-product-to-shop-page';
    protected static ?string $title = 'Add products to shop';
    protected static bool $shouldRegisterNavigation = false;

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
            Action::make('back')
                ->label('Back to shop')
                ->icon('heroicon-o-arrow-left')
                ->url(ShopPage::getUrl()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Products::query()->where('user_id', auth()->id()))
            ->modifyQueryUsing(fn(Builder $query) => $query->where('visibility', 'No'))
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Product Type')
                    ->searchable(),
                TextColumn::make('subtype')
                    ->label('Style')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                Action::make('addToShop')
                    ->label('Add to shop')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form(fn($record) => [
                        TextInput::make('name')
                            ->label('Product Name')
                            ->default($record->name)
                            ->disabled(),
                        TextInput::make('type')
                            ->label('Product Type')
                            ->default($record->type)
                            ->disabled(),
                        TextInput::make('subtype')
                            ->label('Style')
                            ->default($record->subtype)
                            ->disabled(),
                        TextInput::make('colors')
                            ->label('Colors')
                            ->default($record->colors)
                            ->disabled(),
                        TextInput::make('size')
                            ->label('Size')
                            ->default($record->size)
                            ->disabled(),
                        TextArea::make('description')
                            ->label('Description')
                            ->default($record->description)
                            ->placeholder('Add a description for your product'),
                        TextArea::make('inclusions')
                            ->label('Inclusions')
                            ->default($record->inclusions)
                            ->placeholder('Add any additional inclusions for your product'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Add product to shop')
                    ->modalDescription('Are you sure you want to add this product to your shop? Please review and complete the details before confirming.')
                    ->modalWidth('xl')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'description' => $data['description'],
                            'inclusions' => $data['inclusions'],
                            'visibility' => 'Yes',
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Product added successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
