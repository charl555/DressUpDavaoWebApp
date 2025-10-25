<?php

namespace App\Filament\Pages;

use App\Models\ShopAccountRequests;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ShopListPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    protected static string|UnitEnum|null $navigationGroup = 'Shop Accounts';
    protected string $view = 'filament.pages.shop-list-page';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ShopAccountRequests::query())
            ->columns([
                TextColumn::make('user.name')
                    ->label('User Name')
                    ->searchable(),
                TextColumn::make('shop.shop_name')
                    ->label('Shop Name')
                    ->searchable(),
                TextColumn::make('document_type')
                    ->label('Document Type')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Under Review' => 'info',
                        'Verified' => 'success',
                        'Rejected' => 'danger',
                        'Pending' => 'warning',
                        'Disabled' => 'gray',
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('viewShop')
                        ->label('View Shop')
                        ->icon('heroicon-o-eye')
                        ->url(fn($record) => route('shop.overview', $record->shop))
                        ->openUrlInNewTab(),
                    Action::make('manageRequest')
                        ->label('Manage Request')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('primary')
                        ->form(function (ShopAccountRequests $record) {
                            return [
                                Section::make('Shop Information')
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label('Owner Name')
                                            ->disabled(),
                                        TextEntry::make('user.email')
                                            ->label('Owner Email')
                                            ->disabled(),
                                        TextEntry::make('shop.shop_name')
                                            ->label('Shop Name')
                                            ->disabled(),
                                        TextEntry::make('shop.shop_address')
                                            ->label('Shop Address')
                                            ->disabled(),
                                    ])
                                    ->columns(2),
                                Section::make('Verification Document')
                                    ->schema([
                                        TextEntry::make('document_type')
                                            ->label('Document Type')
                                            ->disabled(),
                                        ImageEntry::make('document_url')
                                            ->label('Document')
                                            ->disk('public')
                                            ->height(500)
                                            ->extraImgAttributes([
                                                'class' => 'rounded-lg shadow-md w-full object-contain',
                                                'alt' => 'Verification Document'
                                            ])
                                            ->url(fn($record) => $record->document_url ? asset('storage/' . $record->document_url) : null)
                                            ->openUrlInNewTab()
                                            ->helperText('Click image to open in new tab for closer inspection'),
                                    ]),
                                Textarea::make('rejection_reason')
                                    ->label('Rejection Reason (if rejecting)')
                                    ->placeholder('Provide a clear reason for rejection...')
                                    ->rows(3)
                                    ->helperText('Required only if rejecting the request')
                                    ->hidden(fn($get) => $get('action_type') !== 'reject'),
                                Group::make([
                                    Action::make('activateShop')
                                        ->visible(fn($record) => $record->status !== 'Verified')
                                        ->label('Activate Shop Account')
                                        ->icon('heroicon-o-check-circle')
                                        ->color('primary')
                                        ->requiresConfirmation()
                                        ->modalHeading('Activate Shop Account')
                                        ->modalDescription(fn() => 'Are you sure you want to activate this shop account?')
                                        ->action(function ($record, $set) {
                                            $record->update(['status' => 'Verified']);
                                            $record->shop->update(['shop_status' => 'Verified']);
                                            Notification::make()
                                                ->title('Request Approved')
                                                ->body("Shop verification for {$record->shop->shop_name} has been approved successfully.")
                                                ->success()
                                                ->send();
                                            $set('action_result', 'approved');
                                        }),
                                    Action::make('rejectAction')
                                        ->visible(fn($record) => $record->status !== 'Disabled')
                                        ->label('Disable Shop Account')
                                        ->icon('heroicon-o-x-circle')
                                        ->color('gray')
                                        ->requiresConfirmation()
                                        ->modalHeading('Disable Shop Account')
                                        ->modalDescription(fn() => 'Are you sure you want to disable this shop account?')
                                        ->form([
                                            Textarea::make('rejection_reason')
                                                ->label('Reason for Disabling')
                                                ->required()
                                                ->placeholder('Please provide a detailed reason for disabling this shop account...')
                                                ->rows(4)
                                                ->helperText('This reason will be communicated to the shop owner.'),
                                        ])
                                        ->action(function ($record, $data, $set) {
                                            $record->update([
                                                'status' => 'Disabled',
                                                'rejection_reason' => $data['rejection_reason']
                                            ]);
                                            $record->shop->update(['shop_status' => 'Disabled']);

                                            Notification::make()
                                                ->title('Shop Account Disabled')
                                                ->body("Shop account for {$record->shop->shop_name} has been disabled.")
                                                ->warning()
                                                ->send();

                                            $set('action_result', 'rejected');
                                        }),
                                ])->columns(2),
                            ];
                        })
                        ->modalHeading('Manage Verification Request')
                        ->modalWidth('5xl')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                ])
                    ->label('Actions')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this Request'),
            ]);
    }
}
