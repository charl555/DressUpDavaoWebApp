<?php

namespace App\Filament\Pages;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class ContactMessagePage extends Page Implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected string $view = 'filament.pages.contact-message-page';

    protected static string|UnitEnum|null $navigationGroup = 'Records';
    protected static ?string $title = 'Contact Messages';
    protected static ?string $navigationLabel = 'Contact Messages';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function getTableQuery(): Builder
    {
        return ContactMessage::query();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactMessage::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->formatStateUsing(function ($state) {
                        // Mask name: John Doe -> Jo** D**
                        if (empty($state))
                            return 'Unknown';

                        $nameParts = explode(' ', $state, 2);
                        $maskedParts = [];

                        foreach ($nameParts as $part) {
                            if (strlen($part) <= 2) {
                                $maskedParts[] = $part . '**';
                            } else {
                                $maskedParts[] = substr($part, 0, 2) . '**';
                            }
                        }

                        return implode(' ', $maskedParts);
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->formatStateUsing(function ($state) {
                        // Mask email: johndoe@example.com -> jo***@example.com
                        if (!filter_var($state, FILTER_VALIDATE_EMAIL)) {
                            return $state;
                        }
                        list($local, $domain) = explode('@', $state, 2);
                        $maskedLocal = substr($local, 0, 2) . '***';
                        return $maskedLocal . '@' . $domain;
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return 'Not provided';

                        // Mask phone: +1234567890 -> +12*****90
                        $cleaned = preg_replace('/[^0-9+]/', '', $state);
                        $length = strlen($cleaned);

                        if ($length <= 4)
                            return $cleaned;

                        $visibleStart = substr($cleaned, 0, 2);
                        $visibleEnd = substr($cleaned, -2);
                        $maskedLength = $length - 4;

                        return $visibleStart . str_repeat('*', $maskedLength) . $visibleEnd;
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('message')
                    ->label('Message')
                    ->formatStateUsing(function ($state) {
                        // Mask message content in table view
                        $preview = substr($state, 0, 50);
                        if (strlen($state) > 50) {
                            $preview .= '...';
                        }
                        return $preview;
                    })
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        // Show full message in tooltip but with sensitive data masked
                        $maskedMessage = $this->maskSensitiveData($state);
                        return $maskedMessage;
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'warning',
                        'replied' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return 'Not available';

                        // Mask IP: 192.168.1.100 -> 192.168.*.*
                        $parts = explode('.', $state);
                        if (count($parts) === 4) {
                            return $parts[0] . '.' . $parts[1] . '.*.*';
                        }
                        // For IPv6 or other formats, show first 3 chars
                        return substr($state, 0, 3) . '***';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),  // Hidden by default
            ])
            ->actions([
                Action::make('viewMessage')
                    ->label('View Message')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Contact Message Details')
                    ->modalSubmitAction(false)
                    ->form(function (ContactMessage $record) {
                        $statusColor = match ($record->status) {
                            'unread' => 'danger',
                            'read' => 'warning',
                            'replied' => 'info',
                            'resolved' => 'success',
                            default => 'gray'
                        };

                        return [
                            Section::make('Contact Information')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            Placeholder::make('name')
                                                ->label('Name')
                                                ->content($record->name),  // Full name in detailed view
                                            Placeholder::make('email')
                                                ->label('Email')
                                                ->content($record->email),  // Full email in detailed view
                                            Placeholder::make('phone')
                                                ->label('Phone')
                                                ->content($record->phone ?: 'Not provided'),  // Full phone in detailed view
                                        ]),
                                    Placeholder::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->content(ucfirst($record->status))
                                        ->color($statusColor),
                                ]),
                            Section::make('Message Details')
                                ->schema([
                                    Placeholder::make('subject')
                                        ->label('Subject')
                                        ->content($record->subject ?: 'No subject')
                                        ->extraAttributes(['class' => 'font-semibold text-lg']),
                                    Placeholder::make('message')
                                        ->label('Message')
                                        ->content($record->message)  // Full message in detailed view
                                        ->extraAttributes(['class' => 'bg-gray-50 rounded-lg p-4 whitespace-pre-wrap']),
                                ]),
                            // Technical Information
                            Section::make('Technical Information')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('received')
                                                ->label('Received')
                                                ->content($record->created_at->format('F j, Y \a\t g:i A')),
                                            Placeholder::make('last_updated')
                                                ->label('Last Updated')
                                                ->content($record->updated_at->format('F j, Y \a\t g:i A')),
                                        ]),
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('ip_address')
                                                ->label('IP Address')
                                                ->content($record->ip_address ?: 'Not available'),  // Full IP in detailed view
                                            Placeholder::make('message_id')
                                                ->label('Message ID')
                                                ->content('MSG-' . $record->contact_message_id),
                                        ]),
                                ])
                                ->collapsible()
                                ->collapsed(fn() => !$record->ip_address)
                        ];
                    })
                    ->action(function (ContactMessage $record) {
                        if ($record->status === 'unread') {
                            $record->update(['status' => 'read']);
                        }

                        // Log access to sensitive data for audit trail
                        \Log::info('Contact message viewed with full details', [
                            'message_id' => $record->contact_message_id,
                            'viewed_by' => auth()->id(),
                            'viewed_at' => now(),
                        ]);
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unread' => 'Unread',
                        'read' => 'Read',
                        'replied' => 'Replied',
                        'resolved' => 'Resolved',
                    ])
                    ->label('Status')
                    ->placeholder('All Messages'),
            ])
            ->bulkActions([]);
    }

    /**
     * Mask sensitive data in message content
     */
    private function maskSensitiveData(string $text): string
    {
        // Mask email addresses in text
        $text = preg_replace_callback('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', function ($matches) {
            $email = $matches[0];
            list($local, $domain) = explode('@', $email, 2);
            $maskedLocal = substr($local, 0, 2) . '***';
            return $maskedLocal . '@' . $domain;
        }, $text);

        // Mask phone numbers in text
        $text = preg_replace_callback('/[\+]?[0-9]{8,15}/', function ($matches) {
            $phone = $matches[0];
            $length = strlen($phone);
            if ($length <= 4)
                return $phone;

            $visibleStart = substr($phone, 0, 2);
            $visibleEnd = substr($phone, -2);
            $maskedLength = $length - 4;

            return $visibleStart . str_repeat('*', $maskedLength) . $visibleEnd;
        }, $text);

        return $text;
    }
}
