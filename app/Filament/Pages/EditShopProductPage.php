<?php

namespace App\Filament\Pages;

use App\Models\Products;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EditShopProductPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Edit Product';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'edit-shop-product-page/{record}';

    protected string $view = 'filament.pages.edit-shop-product-page';

    public Products $record;

    public function mount(Products $record): void
    {
        // Ensure the product belongs to the authenticated user
        if ($record->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this product');
        }

        $this->record = $record;

        // Initialize form data using schema data property
        $this->form->fill([
            'name' => $record->name,
            'type' => $record->type,
            'subtype' => $record->subtype,
            'colors' => $record->colors,
            'size' => $record->size,
            'rental_price' => $record->rental_price,
            'description' => $record->description,
            'inclusions' => $record->inclusions,
            'visibility' => $record->visibility,
        ]);
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('backToShop')
                ->label('Back to shop')
                ->icon('heroicon-o-arrow-left')
                ->url(ShopPage::getUrl()),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Product Information')
                ->description('These are the original product details and cannot be changed from here.')
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
                    Toggle::make('visibility')
                        ->label('Product Visibility')
                        ->onIcon('heroicon-o-eye')
                        ->offIcon('heroicon-o-eye-slash')
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Turn this off to hide the product from your shop listing.')
                        ->formatStateUsing(fn($state) => $state === 'Yes')
                        ->dehydrateStateUsing(fn($state) => $state ? 'Yes' : 'No'),
                ]),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update([
            'description' => $data['description'],
            'inclusions' => $data['inclusions'],
            'visibility' => $data['visibility'] ? 'Yes' : 'No',
        ]);

        Notification::make()
            ->title('Product updated successfully!')
            ->success()
            ->send();

        $this->redirect(ShopPage::getUrl());
    }

    public function cancel(): void
    {
        $this->redirect(ShopPage::getUrl());
    }

    public function getTableQuery(): Builder
    {
        return Products::query()->where('user_id', auth()->id());
    }
}
