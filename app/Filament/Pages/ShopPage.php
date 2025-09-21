<?php

namespace App\Filament\Pages;

use App\Models\Products;
use App\Models\Shops;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class ShopPage extends Page implements HasTable, hasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $slug = 'shop-page';

    protected string $view = 'filament.pages.shop-page';

    protected static ?string $navigationLabel = 'Shop';

    public static ?string $Title = 'Shop Page';

    public ?Shops $record = null;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function getTableQuery(): Builder
    {
        return Products::query()
            ->where('user_id', auth()->id())
            ->with(['occasions', 'product_images', 'user']);
    }

    public function getHeaderActions(): array
    {
        return [
            EditAction::make('EditShopDetails')
                ->label('Edit Shop Details')
                ->record(fn() => Shops::firstOrNew(['user_id' => auth()->id()]))
                ->form([
                    Select::make('user_id')
                        ->label('User')
                        ->options(fn() => User::where('id', auth()->id())->pluck('name', 'id'))
                        ->default(auth()->id())
                        ->disabled()
                        ->hidden(),
                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    FileUpload::make('shop_logo')
                                        ->label('Shop Logo')
                                        ->disk('public')
                                        ->directory('shop-images')
                                        ->imageEditor()
                                        ->image()
                                        ->deletable(true)
                                        ->openable(),
                                ]),
                            Group::make()
                                ->schema([
                                    TextInput::make('shop_name')->label('Shop Name')->required(),
                                    TextInput::make('shop_address')->label('Shop Address')->required(),
                                ]),
                        ])
                        ->columns(2),
                    TextArea::make('shop_description')->label('Shop Description'),
                    TextArea::make('shop_policy')->label('Shop Policy'),
                ])
                ->using(function (EditAction $action, array $data) {
                    $record = $action->getRecord();
                    $record->fill($data);
                    $record->save();
                }),
            Action::make('viewShop')
                ->label('View Shop Page')
                ->icon('heroicon-o-eye')
                ->url(fn() => route('shop.overview', Shops::where('user_id', auth()->id())->firstOrFail())),
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
            ->description('This table contains all the products that you have added to your shop. You can edit the details of any product by clicking the "Edit" button.')
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
                    ->schema(function (Products $record): array {
                        return [
                            TextEntry::make('name')->label('Product Name')->disabled(),
                            TextEntry::make('type')->label('Product Type')->disabled(),
                            TextEntry::make('subtype')->label('Style')->disabled(),
                            TextEntry::make('description')->label('Description')->disabled(),
                            TextEntry::make('inclusions')->label('Inclusions')->disabled(),
                            ImageEntry::make('product_images.thumbnail_image')->label('Thumbnail Image')->disk('public'),
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

    public function mount(): void
    {
        $this->record = Shops::where('user_id', auth()->id())->firstOrCreate(['user_id' => auth()->id()]);
    }

    public function ShopDetailsInfolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Shop Details')
                ->description('This section contains your shop details. You can edit these details by clicking the "Edit Shop Details" button.')
                ->schema([
                    Group::make()
                        ->schema([
                            TextEntry::make('shop_name')->label('Shop Name'),
                            TextEntry::make('shop_address')->label('Shop Address'),
                        ]),
                    Group::make()
                        ->schema([
                            TextEntry::make('shop_description')->label('Shop Description')->markdown(),
                            TextEntry::make('shop_policy')->label('Shop Policy')->markdown(),
                        ]),
                    Group::make()
                        ->schema([
                            ImageEntry::make('shop_logo')
                                ->disk('public')
                                ->label('Shop Logo'),
                        ]),
                ])
                ->columns(3),
        ])->record($this->record);
    }
}
