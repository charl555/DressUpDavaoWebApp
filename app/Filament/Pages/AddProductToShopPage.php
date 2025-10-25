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
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
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
            ->description('This table contains all of your products. You can add any of these products to your shop by clicking the "Add to shop" button.')
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
                SelectFilter::make('type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                SelectFilter::make('status')
                    ->options([
                        'Available' => 'Available',
                        'Rented' => 'Rented',
                        'Reserved' => 'Reserved',
                        'Maintenance' => 'Maintenance',
                    ])
                    ->label('Status')
                    ->placeholder('All Statuses'),
                SelectFilter::make('subtype')
                    ->label('Style')
                    ->placeholder('All Styles'),
                SelectFilter::make('size')
                    ->options([
                        'Small' => 'Small',
                        'Medium' => 'Medium',
                        'Large' => 'Large',
                        'XLarge' => 'XLarge',
                        'XXLarge' => 'XXLarge',
                    ])
                    ->label('Size')
                    ->placeholder('All Sizes'),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('addToShop')
                    ->label('Add to shop')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form(fn($record) => [
                        Section::make('Product Information')
                            ->description('Review the product details before adding to your shop')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Group::make()
                                    ->schema([
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
                                    ])
                                    ->columns(3),
                                Group::make()
                                    ->schema([
                                        TextInput::make('colors')
                                            ->label('Available Colors')
                                            ->default($record->colors)
                                            ->disabled(),
                                        TextInput::make('size')
                                            ->label('Size')
                                            ->default($record->size)
                                            ->disabled(),
                                        TextInput::make('rental_price')
                                            ->label('Rental Price')
                                            ->default('₱' . number_format($record->rental_price, 2))
                                            ->disabled(),
                                    ])
                                    ->columns(3),
                            ]),
                        Section::make('Shop Display Information')
                            ->description('Customize how this product appears in your shop')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                TextArea::make('description')
                                    ->label('Product Description')
                                    ->helperText('Write an attractive description that will help customers understand what makes this product special (max 500 characters)')
                                    ->default($record->description)
                                    ->placeholder('Describe the product features, style, occasions, and what makes it unique...')
                                    ->maxLength(500)
                                    ->rows(4)
                                    ->rules(['nullable', 'string', 'max:500']),
                                TextArea::make('inclusions')
                                    ->label("What's Included")
                                    ->helperText('List everything included with this rental (accessories, shoes, etc.) to set clear expectations (max 300 characters)')
                                    ->default($record->inclusions)
                                    ->placeholder('e.g., Dress, matching shoes, jewelry set, hair accessories, garment bag...')
                                    ->maxLength(300)
                                    ->rows(3)
                                    ->rules(['nullable', 'string', 'max:300']),
                            ]),
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
