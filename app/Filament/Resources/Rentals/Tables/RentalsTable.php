<?php

namespace App\Filament\Resources\Rentals\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RentalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Product')->searchable(),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn($state, $record) => ($record->customer?->first_name . ' ' . ($record->customer?->last_name ?? '')))
                    ->searchable(),
                TextColumn::make('pickup_date')
                    ->label('Pickup')
                    ->date('M j, Y')
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return Date')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state)
                            return '-';

                        $returnDate = Carbon::parse($state)->format('M j, Y');
                        $actual = $record->actual_return_date
                            ? Carbon::parse($record->actual_return_date)->format('M j, Y')
                            : null;

                        return $actual
                            ? "<div>{$returnDate}<br><span class='text-xs text-gray-500'>(Actual: {$actual})</span></div>"
                            : "<div>{$returnDate}</div>";
                    })
                    ->tooltip(function ($record) {
                        return $record->actual_return_date
                            ? 'Returned on ' . $record->actual_return_date
                            : 'Not yet returned';
                    })
                    ->sortable(),
                TextColumn::make('rental_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->is_returned && Carbon::parse($record->return_date)->isPast() && $state !== 'Returned') {
                            return 'Overdue';
                        }
                        return $state;
                    })
                    ->color(function ($state, $record) {
                        $label = (!$record->is_returned && Carbon::parse($record->return_date)->isPast() && $state !== 'Returned') ? 'Overdue' : $state;
                        return match ($label) {
                            'Rented' => 'warning',
                            'Picked Up' => 'info',
                            'Overdue' => 'danger',
                            'Returned' => 'success',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('rental_price')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('deposit_amount')
                    ->label('Deposit')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
                TextColumn::make('total_paid')
                    ->label('Total Paid')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('balance_due')
                    ->label('Balance Due')
                    ->formatStateUsing(fn($state) => '₱' . number_format($state, 2))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('rental_status')
                    ->options([
                        'Rented' => 'Rented',
                        'Picked Up' => 'Picked Up',
                        'Returned' => 'Returned',
                    ])
                    ->label('Rental Status')
                    ->placeholder('All Statuses'),
                SelectFilter::make('product.type')
                    ->relationship('product', 'type')
                    ->options([
                        'Gown' => 'Gown',
                        'Suit' => 'Suit',
                    ])
                    ->label('Product Type')
                    ->placeholder('All Types'),
                Filter::make('pickup_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('pickup_from')
                            ->label('Pickup From')
                            ->placeholder('Select start date'),
                        \Filament\Forms\Components\DatePicker::make('pickup_until')
                            ->label('Pickup Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['pickup_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('pickup_date', '>=', $date),
                            )
                            ->when(
                                $data['pickup_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('pickup_date', '<=', $date),
                            );
                    })
                    ->label('Pickup Date Range'),
                Filter::make('event_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('event_from')
                            ->label('Event From')
                            ->placeholder('Select start date'),
                        \Filament\Forms\Components\DatePicker::make('event_until')
                            ->label('Event Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['event_from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '>=', $date),
                            )
                            ->when(
                                $data['event_until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '<=', $date),
                            );
                    })
                    ->label('Event Date Range'),
                Filter::make('overdue_returns')
                    ->query(fn(Builder $query): Builder => $query->whereDate('return_date', '<', now())->where('is_returned', false))
                    ->toggle()
                    ->label('Overdue Returns Only'),
                Filter::make('current_rentals')
                    ->query(fn(Builder $query): Builder => $query->where('rental_status', 'Rented'))
                    ->toggle()
                    ->label('Current Rentals Only'),
            ])
            ->actions([
                Action::make('rentalReceipt')
                    ->label('Rental Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->modalHeading('Rental Receipt')
                    ->modalSubmitAction(false)  // disables default submit button
                    ->modalContent(function ($record) {
                        $customerName = $record->customer
                            ? "{$record->customer->first_name} {$record->customer->last_name}"
                            : ($record->user?->name ?? 'Unknown');

                        $receipt = "
            <div style='font-family: Arial, sans-serif; padding: 1rem; width: 100%; max-width: 600px; margin: auto;'>
                <h2 style='text-align: center; margin-bottom: 1rem;'>Rental Receipt</h2>
                <p><strong>Receipt No:</strong> RNT-{$record->rental_id}</p>
                <p><strong>Date Issued:</strong> " . now()->format('M d, Y') . "</p>
                <hr style='margin: 1rem 0;'>
                <p><strong>Customer:</strong> {$customerName}</p>
                <p><strong>Product:</strong> {$record->product->name}</p>
                <p><strong>Pickup Date:</strong> " . Carbon::parse($record->pickup_date)->format('M d, Y') . '</p>
                <p><strong>Return Date:</strong> ' . Carbon::parse($record->return_date)->format('M d, Y') . "</p>
                <hr style='margin: 1rem 0;'>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 5px;'>Rental Price</td>
                        <td style='text-align: right;'>₱" . number_format($record->rental_price, 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 5px;'>Deposit</td>
                        <td style='text-align: right;'>₱" . number_format($record->deposit_amount, 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 5px;'>Total Paid</td>
                        <td style='text-align: right;'>₱" . number_format($record->total_paid, 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 5px;'>Balance Due</td>
                        <td style='text-align: right;'>₱" . number_format($record->balance_due, 2) . "</td>
                    </tr>
                </table>
                <hr style='margin: 1rem 0;'>
                <p><strong>Status:</strong> {$record->rental_status}</p>
                <p><strong>Handled By:</strong> " . (auth()->user()->name ?? 'System') . "</p>
                <hr style='margin: 1rem 0;'>
                <p style='text-align: center; font-size: 0.9rem; color: #666;'>Thank you for renting with us!</p>
            </div>
        ";

                        return new \Illuminate\Support\HtmlString($receipt);
                    })
                    ->extraModalFooterActions([
                        Action::make('downloadReceipt')
                            ->label('Download Receipt')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('primary')
                            ->action(function ($record) {
                                $pdf = PDF::loadView('pdf.receipt', ['record' => $record]);
                                return response()->streamDownload(
                                    fn() => print ($pdf->output()),
                                    "Rental_Receipt_{$record->rental_id}.pdf"
                                );
                            }),
                    ]),
                ActionGroup::make(
                    [
                        ViewAction::make(),
                        EditAction::make()
                            ->visible(fn($record) => $record->status !== 'Returned' || $record->balance_due > 0),
                        Action::make('markAsPickedUp')
                            ->label('Mark as Picked Up')
                            ->icon('heroicon-o-truck')
                            ->visible(fn($record) => $record->rental_status === 'Rented')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update([
                                    'rental_status' => 'Picked Up',
                                ]);

                                $record->product->update([
                                    'status' => 'Rented',
                                ]);
                            }),
                        Action::make('addPayment')
                            ->label('Add Payment')
                            ->icon('heroicon-o-banknotes')
                            ->visible(fn($record) => !$record->is_returned && ($record->balance_due ?? 0) > 0)
                            ->form(function ($record) {
                                return [
                                    Placeholder::make('balance_info')
                                        ->label('Current Balance Due')
                                        ->content('₱ ' . number_format($record->balance_due ?? 0, 2)),
                                    TextInput::make('amount_paid')
                                        ->label('Amount')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->minValue(1)
                                        ->maxValue($record->balance_due ?? 0)
                                        ->helperText('Enter the amount the customer is paying now. You can repeat this anytime until the balance is zero.'),
                                ];
                            })
                            ->action(function ($record, array $data) {
                                $amount = (float) ($data['amount_paid'] ?? 0);
                                if ($amount <= 0) {
                                    return;
                                }

                                $record->payments()->create([
                                    'rental_id' => $record->rental_id,
                                    'payment_method' => 'Cash',
                                    'amount_paid' => $amount,
                                    'payment_date' => now(),
                                    'payment_status' => 'Paid',
                                    'payment_type' => 'rental',
                                ]);

                                $record->update(['balance_due' => max(0, ($record->balance_due ?? 0) - $amount)]);

                                Notification::make()
                                    ->title('Payment Added')
                                    ->success()
                                    ->send();
                            }),
                        Action::make('markAsReturned')
                            ->label('Mark as Returned')
                            ->icon('heroicon-o-arrow-uturn-left')
                            ->visible(fn($record) => !$record->is_returned && in_array($record->rental_status, ['Picked Up', 'Overdue']))
                            ->form(function ($record) {
                                $hasBalance = ($record->balance_due ?? 0) > 0;
                                $returnDate = Carbon::parse($record->return_date)->startOfDay();
                                $today = Carbon::now()->startOfDay();

                                if ($today->gt($returnDate)) {
                                    $daysOverdue = $today->diffInDays($returnDate);
                                    $message = "Overdue by {$daysOverdue} day(s). Due " . $returnDate->format('M j, Y') . '.';
                                    $message .= 'This rental record is overdue and may be subject to penalties depending on shop policy.';
                                } elseif ($today->lt($returnDate)) {
                                    $message = 'This rental is not yet due for return (due ' . $returnDate->format('M j, Y') . ').';
                                } else {
                                    $message = 'On-time return (due ' . $returnDate->format('M j, Y') . ').';
                                }

                                // Create form components
                                $form = [
                                    Placeholder::make('return_info')
                                        ->label('Return Info')
                                        ->content($message),
                                ];

                                // Balance info display
                                if ($hasBalance) {
                                    $form[] = Placeholder::make('balance_info')
                                        ->label('Current Balance Due')
                                        ->content('₱ ' . number_format($record->balance_due, 2));
                                } else {
                                    $form[] = Placeholder::make('balance_info')
                                        ->label('Current Balance Status')
                                        ->badge()
                                        ->content('Fully Paid')
                                        ->color('success');
                                }

                                // Payment input only if balance remains
                                if ($hasBalance) {
                                    $form[] = TextInput::make('payment_amount')
                                        ->label('Payment Now')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->default(min($record->balance_due, $record->balance_due))
                                        ->minValue(0)
                                        ->maxValue($record->balance_due)
                                        ->helperText('Collect any remaining balance now. This will be added as a payment.');
                                }

                                return $form;
                            })
                            ->action(function ($record, array $data) {
                                $today = Carbon::today();
                                $hasBalance = ($record->balance_due ?? 0) > 0;
                                $paymentAmount = (float) ($data['payment_amount'] ?? 0);

                                // Record rental payment if any and only if there is balance
                                if ($hasBalance && $paymentAmount > 0) {
                                    $record->payments()->create([
                                        'rental_id' => $record->rental_id,
                                        'payment_method' => 'Cash',
                                        'amount_paid' => $paymentAmount,
                                        'payment_date' => now(),
                                        'payment_status' => 'Paid',
                                        'payment_type' => 'rental',
                                    ]);

                                    $record->balance_due = max(0, ($record->balance_due ?? 0) - $paymentAmount);
                                }

                                // Finalize return
                                $record->rental_status = 'Returned';
                                $record->actual_return_date = $today;
                                $record->is_returned = true;
                                $record->save();

                                // Update product back to available
                                $record->product->update(['status' => 'Available']);

                                Notification::make()
                                    ->title('Rental Returned')
                                    ->success()
                                    ->send();
                            }),
                    ]
                )
                    ->label('Manage')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this Rental'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
