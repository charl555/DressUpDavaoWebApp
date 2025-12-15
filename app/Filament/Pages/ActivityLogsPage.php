<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
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

class ActivityLogsPage extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Security Logs';

    protected string $view = 'filament.pages.activity-logs-page';

    protected static ?string $title = 'Activity Logs';

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ActivityLog::query()->with('user')->latest())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date/Time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('System'),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn($record) => $record->getActionColor()),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->description)
                    ->searchable(),
                TextColumn::make('model_type')
                    ->label('Entity Type')
                    ->badge()
                    ->color('gray')
                    ->placeholder('N/A'),
                TextColumn::make('model_id')
                    ->label('Entity ID')
                    ->placeholder('N/A'),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'approve' => 'Approve',
                        'reject' => 'Reject',
                        'block' => 'Block',
                        'unblock' => 'Unblock',
                    ]),
                SelectFilter::make('model_type')
                    ->label('Entity Type')
                    ->options(fn() => ActivityLog::distinct()->whereNotNull('model_type')->pluck('model_type', 'model_type')->toArray()),
                Filter::make('today')
                    ->label('Today Only')
                    ->query(fn($query) => $query->whereDate('created_at', today())),
                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn($query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Activity Log Details')
                    ->modalContent(fn($record) => view('filament.pages.partials.activity-log-details', ['record' => $record]))
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
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}
