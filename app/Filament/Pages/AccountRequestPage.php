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
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class AccountRequestPage extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Shop Accounts';

    public static ?string $Title = 'Shop Verification Requests';

    protected static ?string $navigationLabel = 'Shop Verification Requests';

    protected string $view = 'filament.pages.account-request-page';

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function Table(Table $table): Table
    {
        return $table
            ->query(ShopAccountRequests::query()->whereIn('status', ['Pending', 'Under Review']))
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
                    }),
            ])
            ->actions([
                ActionGroup::make([
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
                                            ->url(fn($record) => $record->document_url ? asset('uploads/' . $record->document_url) : null)
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
                                    Action::make('approveAction')
                                        ->label('Approve Request')
                                        ->icon('heroicon-o-check-circle')
                                        ->color('primary')
                                        ->requiresConfirmation()
                                        ->modalHeading('Approve Verification Request')
                                        ->modalDescription(fn() => 'Are you sure you want to approve this verification request?')
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
                                        ->label('Reject Request')
                                        ->icon('heroicon-o-x-circle')
                                        ->color('gray')
                                        ->requiresConfirmation()
                                        ->modalHeading('Reject Verification Request')
                                        ->modalDescription(fn() => 'Are you sure you want to reject this verification request?')
                                        ->form([
                                            Textarea::make('rejection_reason')
                                                ->label('Reason for Rejection')
                                                ->required()
                                                ->placeholder('Please provide a detailed reason for rejecting this verification request...')
                                                ->rows(4)
                                                ->helperText('This reason will be communicated to the shop owner.'),
                                        ])
                                        ->action(function ($record, $data, $set) {
                                            $record->update([
                                                'status' => 'Rejected',
                                                'rejection_reason' => $data['rejection_reason']
                                            ]);
                                            $record->shop->update(['shop_status' => 'Rejected']);

                                            Notification::make()
                                                ->title('Request Rejected')
                                                ->body("Shop verification for {$record->shop->shop_name} has been rejected.")
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
