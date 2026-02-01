<?php

namespace App\Filament\Resources\Rentals\Tables;

use App\Models\Products;
use App\Models\Rentals;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
                TextColumn::make('rented_by')
                    ->label('Rented By')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->name;
                        } elseif ($record->customer) {
                            return $record->customer->first_name . ' ' . $record->customer->last_name;
                        }
                        return 'N/A';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('customer', function ($q) use ($search) {
                            $q
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    }),
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

                        $returnDateCarbon = Carbon::parse($state)->startOfDay();
                        $today = now()->startOfDay();

                        $returnDate = $returnDateCarbon->format('M j, Y');
                        $actual = $record->actual_return_date
                            ? Carbon::parse($record->actual_return_date)->format('M j, Y')
                            : null;

                        $isOverdue = !$record->is_returned && $today->gt($returnDateCarbon);
                        $overdueDays = $isOverdue ? $today->diffInDays($returnDateCarbon) : 0;

                        if ($actual) {
                            return "<div>{$returnDate}<br>
                                <span class='text-xs text-gray-500'>(Actual: {$actual})</span>
                            </div>";
                        } elseif ($isOverdue) {
                            return "<div>{$returnDate}<br>
                                <span class='text-xs text-red-500 font-semibold'>
                                    (Overdue: {$overdueDays} day(s))
                                </span>
                            </div>";
                        }

                        return "<div>{$returnDate}</div>";
                    })
                    ->tooltip(function ($record) {
                        if ($record->actual_return_date) {
                            return 'Returned on ' . $record->actual_return_date;
                        } elseif (!$record->is_returned && Carbon::parse($record->return_date)->isPast()) {
                            $overdueDays = Carbon::parse($record->return_date)->diffInDays(now());
                            return 'Overdue by ' . $overdueDays . ' day(s)';
                        }
                        return 'Not yet returned';
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
                // Penalty amount column - using existing field from model
                TextColumn::make('penalty_amount')
                    ->label('Penalty')
                    ->formatStateUsing(fn($state) => $state ? '₱' . number_format($state, 2) : '-')
                    ->sortable(),
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
                ActionGroup::make(
                    [
                        EditAction::make()
                            ->visible(fn($record) => $record->status !== 'Returned' || $record->balance_due > 0),
                        Action::make('viewRental')
                            ->label('View Details')
                            ->icon('heroicon-o-eye')
                            ->color('gray')
                            ->modalHeading('Rental Details')
                            ->modalSubmitAction(false)
                            ->form(function ($record) {
                                return self::getRentalDetailsFormSchema($record);
                            }),
                        Action::make('rentalReceipt')
                            ->label('Rental Receipt')
                            ->icon('heroicon-o-printer')
                            ->color('gray')
                            ->modalHeading('Rental Receipt')
                            ->modalSubmitAction(false)
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
                        <td style='text-align: right;'>₱" . number_format($record->rental_price, 2) . '</td>
                    </tr>
                    ' . ($record->penalty_amount > 0 ? "
                    <tr>
                        <td style='padding: 5px;'>Penalty Amount</td>
                        <td style='text-align: right;'>₱" . number_format($record->penalty_amount, 2) . '</td>
                    </tr>' : '') . "
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
                        // Add Penalty action - only for overdue rentals
                        Action::make('addPenalty')
                            ->label('Add Penalty')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->color('danger')
                            ->visible(fn($record) => !$record->is_returned && Carbon::parse($record->return_date)->isPast())
                            ->modalHeading(fn($record) => 'Add Penalty - ' . $record->product->name)
                            ->form(function ($record) {
                                $overdueDays = Carbon::parse($record->return_date)->diffInDays(now());

                                return [
                                    Section::make('Overdue Information')
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    Placeholder::make('overdue_days')
                                                        ->label('Days Overdue')
                                                        ->content("{$overdueDays} day(s)")
                                                        ->badge()
                                                        ->color('danger'),
                                                    Placeholder::make('current_penalty')
                                                        ->label('Current Penalty')
                                                        ->content('₱' . number_format($record->penalty_amount ?? 0, 2))
                                                        ->badge()
                                                        ->color('warning'),
                                                ]),
                                            Placeholder::make('return_date_info')
                                                ->label('Return Date')
                                                ->content('Was due on ' . Carbon::parse($record->return_date)->format('M j, Y'))
                                                ->extraAttributes(['class' => 'text-sm text-gray-600']),
                                        ]),
                                    Section::make('Penalty Amount')
                                        ->schema([
                                            TextInput::make('penalty_amount')
                                                ->label('Penalty Amount (₱)')
                                                ->numeric()
                                                ->minValue(0)
                                                ->step(0.01)
                                                ->required()
                                                ->default($record->penalty_amount ?? 0)
                                                ->helperText('Enter the penalty amount for overdue return.'),
                                        ]),
                                ];
                            })
                            ->action(function ($record, array $data) {
                                $oldPenalty = $record->penalty_amount ?? 0;
                                $newPenalty = (float) $data['penalty_amount'];

                                $record->update([
                                    'penalty_amount' => $newPenalty,
                                ]);

                                Notification::make()
                                    ->title('Penalty Updated')
                                    ->body('Penalty amount updated to ₱' . number_format($newPenalty, 2))
                                    ->success()
                                    ->send();
                            })
                            ->closeModalByClickingAway(false)
                            ->modalCancelActionLabel('Cancel')
                            ->modalSubmitActionLabel('Update Penalty'),
                        Action::make('markAsPickedUp')
                            ->label('Mark as Picked Up')
                            ->icon('heroicon-o-truck')
                            ->visible(fn($record) => $record->rental_status === 'Rented')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update([
                                    'rental_status' => 'Picked Up',
                                ]);
                                // Note: Product status is now determined dynamically based on rental dates
                                // No need to manually update product status
                            }),
                        Action::make('addPayment')
                            ->label('Add Payment')
                            ->icon('heroicon-o-banknotes')
                            ->color('success')
                            ->modalHeading('Add Payment - Rental Details')
                            ->visible(fn($record) => !$record->is_returned && ($record->balance_due ?? 0) > 0)
                            ->form(function ($record) {
                                $isOverdue = !$record->is_returned && Carbon::parse($record->return_date)->isPast();
                                $daysOverdue = $isOverdue ? Carbon::parse($record->return_date)->diffInDays(now()) : 0;

                                return array_merge(
                                    // Rental Information Sections
                                    self::getRentalSummaryFormSchema($record),
                                    // Payment Form
                                    [
                                        Section::make('Payment Information')
                                            ->schema([
                                                Placeholder::make('current_balance')
                                                    ->label('Current Balance Due')
                                                    ->content('₱ ' . number_format($record->balance_due ?? 0, 2))
                                                    ->extraAttributes(['class' => 'text-lg font-bold text-center ' . ($record->balance_due > 0 ? 'text-red-600' : 'text-green-600')]),
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('amount_paid')
                                                            ->label('Payment Amount')
                                                            ->numeric()
                                                            ->prefix('₱')
                                                            ->minValue(1)
                                                            ->maxValue($record->balance_due ?? 0)
                                                            ->required()
                                                            ->default(min($record->balance_due, $record->balance_due))
                                                            ->helperText('Enter the amount the customer is paying now.'),
                                                        Select::make('payment_method')
                                                            ->label('Payment Method')
                                                            ->options([
                                                                'Cash' => 'Cash',
                                                                'GCash' => 'GCash',
                                                                'PayMaya' => 'PayMaya',
                                                                'Bank Transfer' => 'Bank Transfer',
                                                                'Credit Card' => 'Credit Card',
                                                                'Debit Card' => 'Debit Card',
                                                            ])
                                                            ->default('Cash')
                                                            ->required()
                                                            ->helperText('Payment method used by customer.'),
                                                    ]),
                                                Placeholder::make('remaining_balance')
                                                    ->label('Remaining Balance After Payment')
                                                    ->content(function ($get) use ($record) {
                                                        $amount = (float) ($get('amount_paid') ?? 0);
                                                        $remaining = max(0, ($record->balance_due ?? 0) - $amount);
                                                        return '₱ ' . number_format($remaining, 2);
                                                    })
                                                    ->visible(fn($get) => !empty($get('amount_paid')))
                                            ])
                                    ]
                                );
                            })
                            ->action(function ($record, array $data) {
                                $amount = (float) ($data['amount_paid'] ?? 0);
                                $paymentMethod = $data['payment_method'] ?? 'Cash';

                                if ($amount <= 0) {
                                    Notification::make()
                                        ->title('Invalid Amount')
                                        ->body('Please enter a valid payment amount.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                if ($amount > $record->balance_due) {
                                    Notification::make()
                                        ->title('Amount Too High')
                                        ->body('Payment amount cannot exceed the current balance due.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $payment = $record->payments()->create([
                                    'rental_id' => $record->rental_id,
                                    'payment_method' => $paymentMethod,
                                    'amount_paid' => $amount,
                                    'payment_date' => now(),
                                    'payment_status' => 'Paid',
                                    'payment_type' => 'rental',
                                ]);

                                $record->update(['balance_due' => max(0, ($record->balance_due ?? 0) - $amount)]);

                                // Log payment transaction
                                \Log::info('Payment recorded', [
                                    'payment_id' => $payment->payment_id,
                                    'rental_id' => $record->rental_id,
                                    'amount' => $amount,
                                    'payment_method' => $paymentMethod,
                                    'remaining_balance' => $record->balance_due,
                                    'recorded_by' => auth()->id(),
                                    'recorded_by_email' => auth()->user()?->email,
                                    'ip_address' => request()->ip(),
                                    'recorded_at' => now()->toDateTimeString(),
                                ]);

                                Notification::make()
                                    ->title('Payment Added Successfully')
                                    ->body('Payment of ₱' . number_format($amount, 2) . ' has been recorded. Remaining balance: ₱' . number_format($record->balance_due, 2))
                                    ->success()
                                    ->send();
                            }),
                        Action::make('markAsReturned')
                            ->label('Mark as Returned')
                            ->icon('heroicon-o-arrow-uturn-left')
                            ->color('warning')
                            ->modalHeading('Mark as Returned - Rental Details')
                            ->modalWidth('4xl')
                            ->visible(fn($record) => !$record->is_returned && \in_array($record->rental_status, ['Picked Up', 'Overdue']))
                            ->form(function ($record) {
                                $hasBalance = ($record->balance_due ?? 0) > 0;
                                $returnDate = Carbon::parse($record->return_date)->startOfDay();
                                $today = Carbon::now()->startOfDay();
                                $isOverdue = $today->gt($returnDate);
                                $daysOverdue = $isOverdue ? $today->diffInDays($returnDate) : 0;

                                $returnInfo = match (true) {
                                    $isOverdue => "Overdue by {$daysOverdue} day(s). Due " . $returnDate->format('M j, Y') . '.',
                                    $today->lt($returnDate) => 'Early return (due ' . $returnDate->format('M j, Y') . ').',
                                    default => 'On-time return (due ' . $returnDate->format('M j, Y') . ').'
                                };

                                return array_merge(
                                    // Rental Information Sections
                                    self::getRentalSummaryFormSchema($record),
                                    // Return Form
                                    [
                                        Section::make('Return Information')
                                            ->schema([
                                                Placeholder::make('return_status')
                                                    ->label('Return Status')
                                                    ->content($returnInfo)
                                                    ->extraAttributes(['class' => 'text-center p-3 rounded-lg ' . ($isOverdue ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700')]),
                                                Grid::make(2)
                                                    ->schema([
                                                        Placeholder::make('current_balance')
                                                            ->label('Current Balance Due')
                                                            ->content('₱ ' . number_format($record->balance_due, 2))
                                                            ->extraAttributes(['class' => 'text-lg font-bold text-center ' . ($hasBalance ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700') . ' rounded-lg p-3']),
                                                        Placeholder::make('penalty_amount_display')
                                                            ->label('Penalty Amount')
                                                            ->content('₱ ' . number_format($record->penalty_amount ?? 0, 2))
                                                            ->badge()
                                                            ->color($record->penalty_amount > 0 ? 'danger' : 'success')
                                                            ->extraAttributes(['class' => 'text-center rounded-lg p-3']),
                                                    ]),
                                                Grid::make(2)
                                                    ->schema([
                                                        Placeholder::make('current_product_status')
                                                            ->label('Current Product Status')
                                                            ->content($record->product->status ?? 'Unknown')
                                                            ->extraAttributes(['class' => 'text-lg font-bold text-center bg-gray-50 text-gray-700 rounded-lg p-3']),
                                                    ]),
                                                $hasBalance
                                                    ? TextInput::make('payment_amount')
                                                        ->label('Final Payment Amount')
                                                        ->numeric()
                                                        ->prefix('₱')
                                                        ->default(min($record->balance_due, $record->balance_due))
                                                        ->minValue(0)
                                                        ->maxValue($record->balance_due)
                                                        ->helperText('Collect any remaining balance now. Leave as 0 if no payment is needed.')
                                                    : Placeholder::make('no_balance')
                                                        ->label('Balance Status')
                                                        ->content('No balance due - ready to return')
                                                        ->badge()
                                                        ->color('success')
                                                        ->extraAttributes(['class' => 'text-center']),
                                            ]),
                                        Section::make('Product Condition Summary')
                                            ->description('Document the condition of the returned product')
                                            ->schema([
                                                Textarea::make('condition_notes')
                                                    ->label('Condition Notes')
                                                    ->placeholder('Describe the condition of the product upon return (e.g., any stains, tears, missing accessories, etc.)')
                                                    ->rows(3)
                                                    ->helperText('Optional: Write any notes about the condition of the product.'),
                                                Select::make('product_status')
                                                    ->label('Set Product Status')
                                                    ->options([
                                                        'Available' => 'Available - No issues, ready for next rental',
                                                        'Pending Cleaning' => 'Pending Cleaning',
                                                        'In Cleaning' => 'In Cleaning',
                                                        'Steamed & Pressed' => 'Steamed & Pressed',
                                                        'Quality Check' => 'Quality Check',
                                                        'Needs Repair' => 'Needs Repair',
                                                        'In Alteration' => 'In Alteration',
                                                        'Damaged – Not Rentable' => 'Damaged – Not Rentable',
                                                    ])
                                                    ->default('Available')
                                                    ->helperText('If the product has damage or needs maintenance, select the appropriate status. Leave as "Available" if no issues.')
                                                    ->native(false),
                                            ]),
                                    ]
                                );
                            })
                            ->requiresConfirmation()
                            ->modalSubmitActionLabel('Confirm Return')
                            ->color('primary')
                            ->action(function ($record, array $data) {
                                $today = Carbon::today();
                                $hasBalance = ($record->balance_due ?? 0) > 0;
                                $paymentAmount = (float) ($data['payment_amount'] ?? 0);
                                $conditionNotes = $data['condition_notes'] ?? null;
                                $productStatus = $data['product_status'] ?? 'Available';

                                // Validate payment amount
                                if ($hasBalance && $paymentAmount > $record->balance_due) {
                                    Notification::make()
                                        ->title('Invalid Payment Amount')
                                        ->body('Payment amount cannot exceed the current balance due.')
                                        ->send();
                                    return;
                                }

                                // Record final payment if any
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
                                $record->condition_notes = $conditionNotes;
                                $record->save();

                                // Update product status if specified
                                if ($record->product) {
                                    $record->product->update(['status' => $productStatus]);

                                    // Log the condition notes if provided
                                    if ($conditionNotes) {
                                        \Log::info('Product return condition notes', [
                                            'rental_id' => $record->rental_id,
                                            'product_id' => $record->product_id,
                                            'condition_notes' => $conditionNotes,
                                            'new_product_status' => $productStatus,
                                            'recorded_by' => auth()->id(),
                                            'recorded_at' => now()->toDateTimeString(),
                                        ]);
                                    }
                                }

                                $message = 'Rental marked as returned successfully.';
                                if ($paymentAmount > 0) {
                                    $message .= ' Final payment of ₱' . number_format($paymentAmount, 2) . ' recorded.';
                                }
                                if ($record->balance_due > 0) {
                                    $message .= ' Note: Balance of ₱' . number_format($record->balance_due, 2) . ' still remains.';
                                }
                                if ($productStatus !== 'Available') {
                                    $message .= ' Product status set to: ' . $productStatus . '.';
                                }

                                Notification::make()
                                    ->title('Rental Returned')
                                    ->body($message)
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

    /**
     * Get the rental details form schema for view action
     */
    private static function getRentalDetailsFormSchema($record): array
    {
        $customerName = $record->customer
            ? "{$record->customer->first_name} {$record->customer->last_name}"
            : ($record->user?->name ?? 'Unknown');

        $customerContact = $record->customer?->phone_number ?? $record->user?->phone_number ?? 'N/A';
        $customerEmail = $record->customer?->email ?? $record->user?->email ?? 'N/A';

        $returnDate = Carbon::parse($record->return_date)->startOfDay();
        $today = Carbon::today();

        $isOverdue = !$record->is_returned && $today->gt($returnDate);
        $daysOverdue = $isOverdue ? $today->diffInDays($returnDate) : 0;

        $statusColor = match ($record->rental_status) {
            'Rented' => 'warning',
            'Picked Up' => 'info',
            'Returned' => 'success',
            default => 'gray'
        };

        $productStatusColor = match ($record->product->current_status) {
            'Available' => 'success',
            'Rented' => 'warning',
            'Reserved' => 'info',
            'Overdue' => 'danger',
            default => 'gray'
        };

        return [
            // Header Section
            Section::make('Rental Information')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Placeholder::make('rental_id')
                                ->label('Rental ID')
                                ->content('RNT-' . $record->rental_id),
                            Placeholder::make('status')
                                ->label('Status')
                                ->badge()
                                ->content($record->rental_status)
                                ->color($statusColor),
                            Placeholder::make('created_at')
                                ->label('Created Date')
                                ->content($record->created_at->format('M j, Y g:i A')),
                            Placeholder::make('overdue_status')
                                ->label('Overdue Status')
                                ->content($isOverdue ? "Overdue by {$daysOverdue} day(s)" : 'On Time')
                                ->badge()
                                ->color($isOverdue ? 'danger' : 'success')
                                ->visible($isOverdue || $record->rental_status !== 'Returned'),
                        ]),
                ]),
            // Customer & Product Information
            Grid::make(2)
                ->schema([
                    Section::make('Customer Information')
                        ->schema([
                            Placeholder::make('customer_name')
                                ->label('Name')
                                ->content($customerName),
                            Placeholder::make('customer_contact')
                                ->label('Contact')
                                ->content($customerContact),
                            Placeholder::make('customer_email')
                                ->label('Email')
                                ->content($customerEmail),
                        ]),
                    Section::make('Product Information')
                        ->schema([
                            Placeholder::make('product_name')
                                ->label('Product')
                                ->content($record->product->name),
                            Placeholder::make('product_type')
                                ->label('Type')
                                ->content($record->product->type),
                            Placeholder::make('product_status')
                                ->label('Current Status')
                                ->badge()
                                ->content($record->product->current_status)
                                ->color($productStatusColor),
                        ]),
                ]),
            // Rental Dates
            Section::make('Rental Period')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Placeholder::make('pickup_date')
                                ->label('Pickup Date')
                                ->content(Carbon::parse($record->pickup_date)->format('M j, Y'))
                                ->extraAttributes(['class' => 'text-center bg-blue-50 rounded-lg p-3']),
                            Placeholder::make('return_date')
                                ->label('Return Date')
                                ->content(Carbon::parse($record->return_date)->format('M j, Y'))
                                ->extraAttributes(['class' => 'text-center bg-green-50 rounded-lg p-3']),
                            Placeholder::make('event_date')
                                ->label('Event Date')
                                ->content($record->event_date ? Carbon::parse($record->event_date)->format('M j, Y') : 'N/A')
                                ->extraAttributes(['class' => 'text-center bg-purple-50 rounded-lg p-3']),
                        ]),
                    Placeholder::make('actual_return_date')
                        ->label('Actual Return Date')
                        ->content($record->actual_return_date ? Carbon::parse($record->actual_return_date)->format('M j, Y') : 'Not returned yet')
                        ->visible(fn() => $record->actual_return_date !== null)
                        ->extraAttributes(['class' => 'text-center bg-yellow-50 rounded-lg p-3']),
                ]),
            // Penalty Information Section
            Section::make('Penalty Information')
                ->schema([
                    Placeholder::make('penalty_amount')
                        ->label('Penalty Amount')
                        ->content($record->penalty_amount ? '₱' . number_format($record->penalty_amount, 2) : 'No Penalty')
                        ->badge()
                        ->color($record->penalty_amount ? 'danger' : 'success')
                        ->extraAttributes(['class' => 'text-center p-3 rounded-lg']),
                ])
                ->visible($record->penalty_amount > 0),
            Section::make('Return Condition Notes')
                ->schema([
                    Placeholder::make('condition_notes')
                        ->label('Condition Notes')
                        ->content($record->condition_notes ?: 'No condition notes recorded')
                        ->visible(fn() => $record->condition_notes !== null && $record->condition_notes !== '')
                        ->extraAttributes(['class' => 'bg-amber-50 rounded-lg p-3']),
                ])
                ->visible(fn() => $record->condition_notes !== null && $record->condition_notes !== ''),
            // Financial Information
            Section::make('Financial Summary')
                ->schema([
                    Grid::make(4)
                        ->schema([
                            Placeholder::make('rental_price')
                                ->label('Rental Price')
                                ->content('₱' . number_format($record->rental_price, 2))
                                ->extraAttributes(['class' => 'text-center bg-gray-50 rounded-lg p-3']),
                            Placeholder::make('deposit_amount')
                                ->label('Deposit')
                                ->content('₱' . number_format($record->deposit_amount, 2))
                                ->extraAttributes(['class' => 'text-center bg-yellow-50 rounded-lg p-3']),
                            Placeholder::make('total_paid')
                                ->label('Total Paid')
                                ->content('₱' . number_format($record->total_paid, 2))
                                ->extraAttributes(['class' => 'text-center bg-green-50 rounded-lg p-3']),
                            Placeholder::make('balance_due')
                                ->label('Balance Due')
                                ->content('₱' . number_format($record->balance_due, 2))
                                ->extraAttributes(['class' => 'text-center ' . ($record->balance_due > 0 ? 'bg-red-50' : 'bg-green-50') . ' rounded-lg p-3']),
                        ]),
                ]),
            // Additional Information
            Section::make('Additional Information')
                ->schema([
                    Placeholder::make('notes')
                        ->label('Notes')
                        ->content($record->notes ?: 'No notes')
                        ->visible(fn() => !empty($record->notes))
                        ->extraAttributes(['class' => 'bg-gray-50 rounded-lg p-3']),
                    Placeholder::make('special_instructions')
                        ->label('Special Instructions')
                        ->content($record->special_instructions ?: 'No special instructions')
                        ->visible(fn() => !empty($record->special_instructions))
                        ->extraAttributes(['class' => 'bg-blue-50 rounded-lg p-3']),
                ])
                ->visible(fn() => !empty($record->notes) || !empty($record->special_instructions)),
            // Payment History
            Section::make('Payment History')
                ->schema([
                    ViewField::make('payment_history')
                        ->view('filament.tables.custom.payment-history', ['payments' => $record->payments])
                        ->visible(fn() => $record->payments && $record->payments->count() > 0),
                    Placeholder::make('no_payments')
                        ->label('No Payments')
                        ->content('No payment history available')
                        ->visible(fn() => !$record->payments || $record->payments->count() === 0),
                ]),
        ];
    }

    /**
     * Get a condensed rental summary form schema for action modals
     */
    private static function getRentalSummaryFormSchema($record): array
    {
        $customerName = $record->customer
            ? "{$record->customer->first_name} {$record->customer->last_name}"
            : ($record->user?->name ?? 'Unknown');

        $returnDate = Carbon::parse($record->return_date)->startOfDay();
        $today = Carbon::today();

        $isOverdue = !$record->is_returned && $today->gt($returnDate);
        $daysOverdue = $isOverdue ? $today->diffInDays($returnDate) : 0;

        $statusColor = match ($record->rental_status) {
            'Rented' => 'warning',
            'Picked Up' => 'info',
            'Returned' => 'success',
            default => 'gray'
        };

        return [
            Section::make('Rental Summary')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Placeholder::make('rental_id')
                                ->label('Rental ID')
                                ->content('RNT-' . $record->rental_id),
                            Placeholder::make('status')
                                ->label('Status')
                                ->badge()
                                ->content($record->rental_status)
                                ->color($statusColor),
                            Placeholder::make('customer_name')
                                ->label('Customer')
                                ->content($customerName),
                            Placeholder::make('product_name')
                                ->label('Product')
                                ->content($record->product->name),
                        ]),
                ]),
            Section::make('Rental Period & Financials')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    Placeholder::make('pickup_date')
                                        ->label('Pickup Date')
                                        ->content(Carbon::parse($record->pickup_date)->format('M j, Y')),
                                    Placeholder::make('return_date')
                                        ->label('Return Date')
                                        ->content(Carbon::parse($record->return_date)->format('M j, Y'))
                                        ->extraAttributes(['class' => $isOverdue ? 'text-red-600 font-semibold' : '']),
                                    $isOverdue
                                        ? Placeholder::make('overdue_days')
                                            ->label('Overdue Days')
                                            ->content("{$daysOverdue} day(s) overdue")
                                            ->badge()
                                            ->color('danger')
                                        : Placeholder::make('return_status')
                                            ->label('Return Status')
                                            ->content($isOverdue ? 'Overdue' : 'On Time')
                                            ->badge()
                                            ->color($isOverdue ? 'danger' : 'success'),
                                ]),
                            Grid::make(1)
                                ->schema([
                                    Placeholder::make('rental_price')
                                        ->label('Rental Price')
                                        ->content('₱' . number_format($record->rental_price, 2)),
                                    Placeholder::make('penalty_summary')
                                        ->label('Penalty')
                                        ->content('₱' . number_format($record->penalty_amount ?? 0, 2))
                                        ->extraAttributes(['class' => $record->penalty_amount > 0 ? 'text-red-600 font-semibold' : '']),
                                    Placeholder::make('total_paid')
                                        ->label('Total Paid')
                                        ->content('₱' . number_format($record->total_paid, 2)),
                                    Placeholder::make('balance_due')
                                        ->label('Balance Due')
                                        ->content('₱' . number_format($record->balance_due, 2))
                                        ->extraAttributes(['class' => $record->balance_due > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold']),
                                ]),
                        ]),
                ]),
        ];
    }
}
