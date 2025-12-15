<?php

namespace App\Filament\Pages;

use App\Models\LoginBlock;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class LoginBlocksPage extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|UnitEnum|null $navigationGroup = 'Security Logs';

    protected string $view = 'filament.pages.login-blocks-page';

    protected static ?string $title = 'Login Blocks';

    protected static ?string $navigationLabel = 'Login Blocks';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(LoginBlock::query()->latest())
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('N/A'),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('N/A'),
                TextColumn::make('attempts')
                    ->label('Attempts')
                    ->sortable()
                    ->badge()
                    ->color('danger'),
                TextColumn::make('reason')
                    ->label('Block Reason')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->reason),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->isActive())
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success'),
                TextColumn::make('blocked_until')
                    ->label('Blocked Until')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active_blocks')
                    ->label('Active Blocks Only')
                    ->query(fn($query) => $query->where('blocked_until', '>', now())),
                Filter::make('expired_blocks')
                    ->label('Expired Blocks')
                    ->query(fn($query) => $query->where('blocked_until', '<=', now())),
            ])
            ->actions([
                Action::make('unblock')
                    ->label('Unblock')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Unblock User/IP')
                    ->modalDescription('Are you sure you want to unblock this user/IP? They will be able to attempt login again.')
                    ->visible(fn($record) => $record->isActive())
                    ->action(function ($record) {
                        $record->update(['blocked_until' => now()]);

                        \Log::info('Login block removed by admin', [
                            'block_id' => $record->id,
                            'email' => $record->email,
                            'ip_address' => $record->ip_address,
                            'unblocked_by' => auth()->id(),
                            'unblocked_by_email' => auth()->user()?->email,
                            'unblocked_at' => now()->toDateTimeString(),
                        ]);

                        // Activity log
                        \App\Models\ActivityLog::log(
                            'unblock',
                            "Login block removed for {$record->email} (IP: {$record->ip_address})",
                            'LoginBlock',
                            $record->id
                        );

                        Notification::make()
                            ->title('Block Removed')
                            ->body('The login block has been removed successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('unblock_selected')
                    ->label('Unblock Selected')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update(['blocked_until' => now()]);
                        });

                        Notification::make()
                            ->title('Blocks Removed')
                            ->body('Selected login blocks have been removed.')
                            ->success()
                            ->send();
                    }),
                BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->delete()),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}
