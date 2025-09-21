<?php

namespace App\Filament\Pages;

use App\Models\Shops;
use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShopDetailsPage extends Page
{
    protected string $view = 'filament.pages.shop-details-page';
    protected static bool $shouldRegisterNavigation = false;

    public ?Shops $record = null;

    public function getheaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to shop')
                ->icon('heroicon-o-arrow-left')
                ->url(ShopPage::getUrl()),
        ];
    }

    public function mount(): void
    {
        // Load the shop for the current authenticated user
        $this->record = Shops::where('user_id', auth()->id())->firstOrFail();
    }

    public function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Shop Details')
                ->schema([
                    TextEntry::make('shop_name')->label('Shop Name'),
                    TextEntry::make('shop_address')->label('Shop Address'),
                    TextEntry::make('shop_description')->label('Shop Description'),
                    TextEntry::make('shop_slug')->label('Shop Slug'),
                    ImageEntry::make('shop_logo')->label('Shop Logo'),
                    TextEntry::make('shop_policy')->label('Shop Policy'),
                ]),
        ])->record($this->record);
    }
}
