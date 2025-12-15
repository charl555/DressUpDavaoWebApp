<?php

namespace App\Filament\Pages;

use App\Models\Payments;
use Filament\Actions\Action;
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
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class PaymentAuditPage extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Security Logs';

    protected string $view = 'filament.pages.payment-audit-page';

    protected static ?string $title = 'Payment Audit';

    protected static ?string $navigationLabel = 'Payment Audit';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()?->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Payments::query()->with('rental.customer', 'rental.user')->latest('payment_date'))
            ->columns([
                TextColumn::make('payment_id')
                    ->label('Payment ID')
                    ->sortable()
                    ->searchable()
                    ->prefix('PAY-'),
                TextColumn::make('rental_id')
                    ->label('Rental ID')
                    ->sortable()
                    ->searchable()
                    ->prefix('RNT-'),
                TextColumn::make('rental.customer.first_name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('N/A'),
                TextColumn::make('rental.user.name')
                    ->label('Shop Owner')
                    ->searchable()
                    ->placeholder('N/A'),
                TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Cash' => 'success',
                        'GCash' => 'info',
                        'PayMaya' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Pending' => 'warning',
                        'Failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'Cash' => 'Cash',
                        'GCash' => 'GCash',
                        'PayMaya' => 'PayMaya',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Status')
                    ->options([
                        'Paid' => 'Paid',
                        'Pending' => 'Pending',
                        'Failed' => 'Failed',
                    ]),
                Filter::make('today')
                    ->label('Today Only')
                    ->query(fn($query) => $query->whereDate('payment_date', today())),
                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn($query) => $query->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])),
                Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn($query) => $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Payment Details')
                    ->modalContent(fn($record) => view('filament.pages.partials.payment-details', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->defaultSort('payment_date', 'desc');
    }
}
