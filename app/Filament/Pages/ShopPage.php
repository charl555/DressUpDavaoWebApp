<?php

namespace App\Filament\Pages;

use App\Models\Products;
use App\Models\ShopAccountRequests;
use App\Models\Shops;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class ShopPage extends Page implements HasTable, HasSchemas, HasActions
{
    use InteractsWithTable;
    use InteractsWithSchemas;
    use InteractsWithActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $slug = 'shop-page';

    protected string $view = 'filament.pages.shop-page';

    protected static ?string $navigationLabel = 'Shop';

    public static ?string $Title = 'Shop Page';

    public ?Shops $record = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->record = Shops::firstOrCreate(
            ['user_id' => auth()->id()],
            ['shop_status' => 'Not Verified']
        );
    }

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
        $status = $this->record->shop_status;

        // If under review → show message only
        if ($status === 'Under Review') {
            return [
                Action::make('VerificationPending')
                    ->label('Verification in Process')
                    ->icon('heroicon-o-information-circle')
                    ->disabled()
                    ->color('gray')
                    ->extraAttributes(['class' => 'cursor-not-allowed'])
                    ->tooltip('Your verification is currently under review. Please wait for approval.'),
            ];
        }
        return [
            Action::make('VerifyShop')
                ->visible(fn() => $status !== 'Verified')
                ->label('Verify Shop')
                ->icon('heroicon-o-shield-check')
                ->form([
                    Select::make('document_type')
                        ->label('Document Type')
                        ->options([
                            'Business Permit' => 'Business Permit',
                            'DTI Certificate' => 'DTI Certificate',
                            'Other' => 'Other Legal Document',
                        ])
                        ->required(),
                    FileUpload::make('document_url')
                        ->label('Upload Document')
                        ->helperText('Upload a clear copy (PDF or Image, max 5MB).')
                        ->disk('public')
                        ->visibility('public')
                        ->directory('shop-verifications')
                        ->maxSize(5120)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $userId = auth()->id();
                    $shop = Shops::where('user_id', $userId)->first();

                    if ($shop) {
                        // Create the verification request record
                        ShopAccountRequests::create([
                            'user_id' => $userId,
                            'shop_id' => $shop->shop_id,
                            'document_type' => $data['document_type'],
                            'document_url' => $data['document_url'],
                            'status' => 'Pending',
                        ]);

                        // Update shop status to Under Review
                        $shop->update([
                            'shop_status' => 'Under Review',
                        ]);

                        Notification::make()
                            ->title('Shop Verification Submitted')
                            ->body('Your verification document has been submitted and is now under review.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Error')
                            ->body('Shop record not found. Please make sure your shop profile exists.')
                            ->danger()
                            ->send();
                    }
                })
                ->modalHeading('Verify Your Shop')
                ->modalDescription('Submit your business verification documents to start the review process.')
                ->modalSubmitActionLabel('Submit for Review')
                ->modalCancelActionLabel('Cancel'),
            EditAction::make('EditShopDetails')
                ->hidden(fn() => $status !== 'Verified')
                ->label('Edit Shop Details')
                ->icon('heroicon-o-pencil-square')
                ->record(fn() => Shops::firstOrNew(['user_id' => auth()->id()]))
                ->form([
                    Select::make('user_id')
                        ->label('User')
                        ->options(fn() => User::where('id', auth()->id())->pluck('name', 'id'))
                        ->default(auth()->id())
                        ->disabled()
                        ->hidden(),
                    Section::make('Shop Information')
                        ->description('Configure your shop details that will be displayed to customers')
                        ->icon('heroicon-o-building-storefront')
                        ->schema([
                            Group::make()
                                ->schema([
                                    Group::make()
                                        ->schema([
                                            FileUpload::make('shop_logo')
                                                ->label('Shop Logo')
                                                ->helperText('Upload your shop logo. Recommended size: 400x400px. Maximum file size: 2MB.')
                                                ->disk('public')
                                                ->visibility('public')
                                                ->directory('shop-images')
                                                ->imageEditor()
                                                ->image()
                                                ->maxSize(2048)  // 2MB
                                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                ->deletable(true)
                                                ->openable()
                                                ->imageEditorAspectRatios(['1:1'])
                                                ->default(function ($livewire) {
                                                    // Only set default when editing existing shop
                                                    if (method_exists($livewire, 'getRecord') && $record = $livewire->getRecord()) {
                                                        if ($record->shop_logo) {
                                                            return [asset('storage/' . $record->shop_logo)];
                                                        }
                                                    }
                                                    return null;
                                                }),
                                        ]),
                                    Group::make()
                                        ->schema([
                                            TextInput::make('shop_name')
                                                ->label('Shop Name')
                                                ->helperText('Enter your business or shop name as you want it to appear to customers')
                                                ->required()
                                                ->maxLength(100)
                                                ->placeholder('e.g., Elegant Dress Rentals')
                                                ->rules(['required', 'string', 'max:100']),
                                            TextInput::make('shop_address')
                                                ->prefixIcon('heroicon-o-map-pin')
                                                ->label('Shop Address')
                                                ->helperText('Enter your business address')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('e.g., 123 Main Street, Davao City, 8000')
                                                ->rules(['required', 'string', 'max:255']),
                                            TagsInput::make('payment_options')
                                                ->label('Payment Options')
                                                ->helperText('Add payment options accepted by your shop')
                                                ->placeholder('e.g., Cash, GCash, PayMaya, Bank Transfer, Credit Card')
                                                ->separator(',')
                                                ->suggestions([
                                                    'Cash',
                                                    'GCash',
                                                    'PayMaya',
                                                    'Bank Transfer',
                                                    'Credit Card',
                                                ])
                                        ]),
                                ])
                                ->columns(2),
                            Group::make()
                                ->schema([
                                    TextInput::make('facebook_url')
                                        ->label('Facebook Link')
                                        ->helperText('Enter your Facebook page Link')
                                        ->maxLength(255)
                                        ->placeholder('https://www.facebook.com/yourpage')
                                        ->rules(['nullable', 'string', 'max:255', 'url'])
                                        ->prefixIcon('heroicon-o-paper-clip'),
                                    TextInput::make('instagram_url')
                                        ->label('Instagram Link')
                                        ->helperText('Enter your Instagram page Link')
                                        ->maxLength(255)
                                        ->placeholder('https://www.instagram.com/yourpage')
                                        ->rules(['nullable', 'string', 'max:255', 'url'])
                                        ->prefixIcon('heroicon-o-paper-clip'),
                                    TextInput::make('tiktok_url')
                                        ->label('Tiktok Link')
                                        ->helperText('Enter your Tiktok page Link')
                                        ->maxLength(255)
                                        ->placeholder('https://www.tiktok.com/@yourpage')
                                        ->rules(['nullable', 'string', 'max:255', 'url'])
                                        ->prefixIcon('heroicon-o-paper-clip'),
                                ])
                                ->columns(3),
                            TextArea::make('shop_description')
                                ->label('Shop Description')
                                ->helperText('Write a compelling description of your shop, services, and what makes you unique (max 500 characters)')
                                ->maxLength(500)
                                ->rows(4)
                                ->placeholder('Describe your shop, specialties, years of experience, and what sets you apart...')
                                ->rules(['nullable', 'string', 'max:500']),
                            TextArea::make('shop_policy')
                                ->label('Shop Policy')
                                ->helperText('Define your rental policies, terms, conditions, and important guidelines for customers (max 1000 characters)')
                                ->maxLength(1000)
                                ->rows(6)
                                ->placeholder('Include rental duration, damage policies, cancellation terms, payment requirements, etc...')
                                ->rules(['nullable', 'string', 'max:1000']),
                        ]),
                ])
                ->using(function (EditAction $action, array $data) {
                    $record = $action->getRecord();
                    $record->fill($data);
                    $record->save();
                }),
            Action::make('viewShop')
                ->hidden(fn() => $status !== 'Verified')
                ->label('View Shop Page')
                ->icon('heroicon-o-eye')
                ->url(fn() => route('shop.overview', Shops::where('user_id', auth()->id())->firstOrFail())),
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
        if ($this->record->shop_status !== 'Verified') {
            // return a disabled placeholder table when shop is unverified
            return $table
                ->description('You cannot manage products until your shop is verified.')
                ->query(Products::query()->where('product_id', 0))  // empty query
                ->columns([
                    TextColumn::make('notice')
                        ->label('Notice')
                        ->getStateUsing(fn() => 'Shop not verified. Product management is disabled.')
                        ->color('danger'),
                ]);
        }
        return $table
            ->description('This table contains all of your products. You can edit the product details and post them to your shop.')
            ->query(Products::query()->where('user_id', auth()->id()))
            ->columns([
                ImageColumn::make('product_images.thumbnail_image')
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        if ($record->product_images->isEmpty()) {
                            return null;
                        }

                        $firstImage = $record->product_images->first();

                        if (!$firstImage->thumbnail_image) {
                            return null;
                        }

                        return asset('storage/' . $firstImage->thumbnail_image);
                    }),
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
                IconColumn::make('visibility')
                    ->label('Visibility')
                    ->searchable()
                    ->icon(fn($state) => $state === 'Yes' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state) => $state === 'Yes' ? 'success' : 'danger')
                    ->size('lg'),
            ])
            ->defaultSort('created_at', 'desc')
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
                ActionGroup::make([
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
                    EditAction::make('editProduct')
                        ->label('Edit Product')
                        ->icon('heroicon-o-pencil-square')
                        ->modalHeading('Edit Product')
                        ->modalDescription('Modify the product details below and save your changes.')
                        ->form(function (Products $record) {
                            return [
                                Section::make('Product Information')
                                    ->description('These are the original product details and cannot be changed.')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Product Name')
                                                    ->disabled(),
                                                TextInput::make('type')
                                                    ->label('Product Type')
                                                    ->disabled(),
                                                TextInput::make('subtype')
                                                    ->label('Style')
                                                    ->disabled(),
                                            ])
                                            ->columns(3),
                                        Group::make()
                                            ->schema([
                                                TextInput::make('colors')
                                                    ->label('Available Colors')
                                                    ->disabled(),
                                                TextInput::make('size')
                                                    ->label('Size')
                                                    ->disabled(),
                                                TextInput::make('rental_price')
                                                    ->label('Rental Price')
                                                    ->prefix('₱')
                                                    ->disabled(),
                                            ])
                                            ->columns(3),
                                    ]),
                                Section::make('Shop Display Information')
                                    ->description('Update how this product is shown in your shop.')
                                    ->icon('heroicon-o-eye')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Product Description')
                                            ->helperText('Max 500 characters. Write an attractive description for customers.')
                                            ->placeholder('Describe the product features, style, occasions, etc.')
                                            ->maxLength(500)
                                            ->rows(4),
                                        Textarea::make('inclusions')
                                            ->label("What's Included")
                                            ->helperText('Max 300 characters. List accessories, shoes, or other inclusions.')
                                            ->placeholder('e.g., Dress, shoes, jewelry, garment bag...')
                                            ->maxLength(300)
                                            ->rows(3),
                                    ]),
                                Section::make('Visibility')
                                    ->description('Update the product visibility.')
                                    ->icon('heroicon-o-check-circle')
                                    ->schema([
                                        Radio::make('visibility')
                                            ->label('Product Visibility')
                                            ->options([
                                                'Yes' => 'Visible in Shop',
                                                'No' => 'Hidden from Shop',
                                            ])
                                            ->inline()
                                            ->default(fn($record) => $record?->visibility ?? 'Yes')
                                            ->required(),
                                    ]),
                            ];
                        })
                        ->action(function (array $data, Products $record): void {
                            $record->update([
                                'description' => $data['description'],
                                'inclusions' => $data['inclusions'],
                                'visibility' => $data['visibility'],
                            ]);
                        })
                        ->modalSubmitActionLabel('Save Changes')
                        ->modalCancelActionLabel('Cancel'),
                ])
                    ->label('Manage')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this Product'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function ShopDetailsInfolist(Schema $schema): Schema
    {
        if ($this->record->shop_status !== 'Verified') {
            // show disabled infolist placeholder
            return $schema->components([]);
        }
        return $schema->components([
            Section::make('Shop Details')
                ->description('This section contains your shop details. You can edit these details by clicking the "Edit Shop Details" button.')
                ->schema([
                    // Logo and Basic Details side by side
                    Group::make()
                        ->schema([
                            ImageEntry::make('shop_logo')
                                ->label('Shop Logo')
                                ->getStateUsing(function ($record) {
                                    if (!$record->shop_logo) {
                                        return null;
                                    }
                                    return asset('storage/' . $record->shop_logo);
                                })
                                ->columnSpan(1),
                            Group::make()
                                ->schema([
                                    TextEntry::make('shop_name')->label('Shop Name'),
                                    TextEntry::make('shop_address')->label('Shop Address'),
                                ])
                                ->columnSpan(2),
                        ])
                        ->columns(3),
                    // Shop Description and Policy in two columns
                    Section::make('About Your Shop')
                        ->schema([
                            TextEntry::make('shop_description')
                                ->label('Shop Description')
                                ->placeholder('Write a compelling description of your shop, services, and what makes you unique.')
                                ->markdown(),
                            TextEntry::make('shop_policy')
                                ->label('Shop Policy')
                                ->placeholder('Define your rental policies, terms, and important guidelines for customers.')
                                ->markdown(),
                            TextEntry::make('payment_options')
                                ->label('Payment Options')
                                ->placeholder('Cash, GCash, PayMaya, Bank Transfer, Credit Card'),
                        ])
                        ->columns(3),
                    // Social Links neatly aligned in 3 columns
                    Section::make('Social Links')
                        ->description('Your shop’s social media and contact pages.')
                        ->schema([
                            TextEntry::make('facebook_url')
                                ->label('Facebook')
                                ->url(fn($record) => $record->facebook_url)
                                ->icon('heroicon-o-globe-alt')
                                ->color('primary')
                                ->openUrlInNewTab()
                                ->placeholder('No Facebook link provided'),
                            TextEntry::make('instagram_url')
                                ->label('Instagram')
                                ->url(fn($record) => $record->instagram_url)
                                ->icon('heroicon-o-camera')
                                ->color('primary')
                                ->openUrlInNewTab()
                                ->placeholder('No Instagram link provided'),
                            TextEntry::make('tiktok_url')
                                ->label('TikTok')
                                ->url(fn($record) => $record->tiktok_url)
                                ->icon('heroicon-o-musical-note')
                                ->color('primary')
                                ->openUrlInNewTab()
                                ->placeholder('No TikTok link provided'),
                        ])
                        ->columns(3),
                ])
                ->columns(1),
        ])->record($this->record);
    }
}
