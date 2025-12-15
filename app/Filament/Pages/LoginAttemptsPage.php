<?php

namespace App\Filament\Pages;

use App\Models\LoginAttempt;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class LoginAttemptsPage extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Security Logs';

    protected string $view = 'filament.pages.login-attempts-page';

    protected static ?string $title = 'Login Attempts';

    protected static ?string $navigationLabel = 'Login Attempts';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(LoginAttempt::query()->latest('attempted_at'))
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                IconColumn::make('success')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                TextColumn::make('attempted_at')
                    ->label('Attempted At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('success')
                    ->label('Status')
                    ->options([
                        '1' => 'Successful',
                        '0' => 'Failed',
                    ]),
                Filter::make('today')
                    ->label('Today Only')
                    ->query(fn($query) => $query->whereDate('attempted_at', today())),
                Filter::make('failed_only')
                    ->label('Failed Only')
                    ->query(fn($query) => $query->where('success', false)),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Login Attempt Details')
                    ->modalContent(fn($record) => view('filament.pages.partials.login-attempt-details', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->label('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Collection $records) => $records->each->delete()),
            ])
            ->defaultSort('attempted_at', 'desc')
            ->poll('30s');
    }
}
