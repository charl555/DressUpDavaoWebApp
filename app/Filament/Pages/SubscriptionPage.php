<?php

namespace App\Filament\Pages;

use App\Models\Subscriptions;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class SubscriptionPage extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Others';
    protected string $view = 'filament.pages.subscription-page';
    protected static ?string $navigationLabel = 'Subscription';
    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Subscription Page';
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $subscription = Subscriptions::where('user_id', Auth::id())->first();

        $this->form->fill($subscription ? $subscription->toArray() : []);
    }

    // Change the type hint to the correct Form class
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->aside()
                    ->description('Your Subscription Information')
                    ->schema([
                        TextInput::make('user_id')
                            ->hidden()
                            ->disabled(),
                        TextInput::make('subscription_type')
                            ->label('Subscription Type')
                            ->disabled(),
                        TextInput::make('subscription_status')
                            ->label('Subscription Status')
                            ->disabled(),
                        TextInput::make('product_limit')
                            ->label('Product Limit')
                            ->disabled(),
                        TextInput::make('start_date')
                            ->label('Start Date')
                            ->disabled(),
                        TextInput::make('end_date')
                            ->label('End Date')
                            ->disabled(),
                    ])
            ])
            ->statePath('data');
    }
}
