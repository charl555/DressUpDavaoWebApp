<?php

namespace App\Filament\Pages;

use App\Models\Bookings;
use App\Models\Customers;
use App\Models\Products;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class BookingsPage extends Page implements HasTable, HasSchemas
{
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmark;

    protected string $view = 'filament.pages.bookings-page';

    protected static ?string $navigationLabel = 'Bookings & Reservations';

    protected static string|UnitEnum|null $navigationGroup = 'Rental Management';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin();
    }

    public function getTableQuery(): Builder
    {
        return Bookings::query()
            ->whereHas('product', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['user', 'product.product_images']);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('BookProduct')
                ->label('Create Booking Request')
                ->icon('heroicon-o-calendar-days')
                ->color('primary')
                ->modalHeading('Create New Booking Request')
                ->modalDescription('Create a booking request. Product will not be reserved until confirmed.')
                ->form([
                    // Client Selection Section
                    Section::make('Client Information')
                        ->description('Select whether the client is a registered user or an unregistered customer.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Select::make('client_type')
                                ->label('Client Type')
                                ->options([
                                    'user' => 'Registered User (Has Account)',
                                    'customer' => 'Customer (No Account)',
                                ])
                                ->default('user')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(fn(callable $set) => $set('client_id', null))
                                ->helperText('Choose whether the client has an account or is a walk-in customer.'),
                            Select::make('client_id')
                                ->label('Select Client')
                                ->searchable()
                                ->required()
                                ->live()
                                ->getSearchResultsUsing(function (string $search, callable $get) {
                                    if ($get('client_type') === 'user') {
                                        return User::where('role', 'User')
                                            ->where(function ($query) use ($search) {
                                                $query
                                                    ->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%")
                                                    ->orWhere('phone_number', 'like', "%{$search}%");
                                            })
                                            ->limit(15)
                                            ->get()
                                            ->mapWithKeys(fn($u) => [$u->id => "{$u->name} - {$u->email}"])
                                            ->toArray();
                                    }

                                    // For customers, search by name or phone
                                    return Customers::where('user_id', Auth::id())
                                        ->where(function ($query) use ($search) {
                                            $query
                                                ->where('first_name', 'like', "%{$search}%")
                                                ->orWhere('last_name', 'like', "%{$search}%")
                                                ->orWhere('phone_number', 'like', "%{$search}%");
                                        })
                                        ->limit(15)
                                        ->get()
                                        ->mapWithKeys(fn($c) => [$c->customer_id => "{$c->first_name} {$c->last_name} - {$c->phone_number}"])
                                        ->toArray();
                                })
                                ->getOptionLabelUsing(function ($value, callable $get) {
                                    if ($get('client_type') === 'user') {
                                        $user = User::find($value);
                                        return $user ? "{$user->name} - {$user->email}" : null;
                                    }

                                    $customer = Customers::find($value);
                                    return $customer ? "{$customer->first_name} {$customer->last_name} - {$customer->phone_number}" : null;
                                })
                                ->helperText(fn(callable $get) => $get('client_type') === 'user'
                                    ? 'Search by name, email, or phone number.'
                                    : 'Search by name or phone number from your customer list.'),
                        ])
                        ->columns(2),
                    // Product Selection Section
                    Section::make('Product Selection')
                        ->description('Choose a product to request booking for.')
                        ->icon('heroicon-o-shopping-bag')
                        ->schema([
                            Select::make('product_id')
                                ->label('Select Product')
                                ->options(function () {
                                    return Products::where('user_id', Auth::id())
                                        ->whereNotIn('status', Products::MAINTENANCE_STATUSES)
                                        ->get()
                                        ->mapWithKeys(function ($product) {
                                            $size = $product->size ?? 'N/A';
                                            return [
                                                $product->product_id => "{$product->name} | Size: {$size} | Status: {$product->status}"
                                            ];
                                        });
                                })
                                ->required()
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn(callable $set) => $set('booking_date', null)),
                            Placeholder::make('product_details')
                                ->label('Product Details')
                                ->content(function (callable $get) {
                                    $productId = $get('product_id');
                                    if (!$productId) {
                                        return 'Select a product to see details.';
                                    }
                                    $product = Products::find($productId);
                                    if (!$product) {
                                        return 'Product not found.';
                                    }
                                    return new \Illuminate\Support\HtmlString(
                                        "<div class='space-y-1'>
                                            <p><strong>Name:</strong> {$product->name}</p>
                                            <p><strong>Type:</strong> {$product->type}" . ($product->subtype ? " - {$product->subtype}" : '') . '</p>
                                            <p><strong>Size:</strong> ' . ($product->size ?? 'N/A') . '</p>
                                            <p><strong>Rental Price:</strong> ₱' . number_format($product->rental_price, 2) . "</p>
                                            <p><strong>Status:</strong> {$product->status}</p>
                                        </div>"
                                    );
                                })
                                ->visible(fn(callable $get) => !empty($get('product_id'))),
                        ]),
                    // Date Selection Section - FIXED
                    Section::make('Booking Date')
                        ->description('Select desired booking date. Booking date must be at least 2 days from today to allow time for confirmation.')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            DatePicker::make('booking_date')
                                ->label('Desired Booking Date')
                                ->required()
                                ->minDate(now()->addDays(2)->startOfDay())
                                ->native(false)
                                ->displayFormat('F j, Y')
                                ->disabledDates(function (callable $get) {
                                    $productId = $get('product_id');
                                    if (!$productId) {
                                        return [];
                                    }

                                    try {
                                        // OPTIMIZED: Direct database query instead of model method
                                        $unavailableDates = [];

                                        // Get rental dates
                                        $rentalDates = \DB::table('rentals')
                                            ->where('product_id', $productId)
                                            ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
                                            ->select('pickup_date', 'return_date')
                                            ->get();

                                        foreach ($rentalDates as $rental) {
                                            $start = Carbon::parse($rental->pickup_date);
                                            $end = Carbon::parse($rental->return_date);

                                            for ($date = $start; $date->lte($end); $date->addDay()) {
                                                $unavailableDates[] = $date->format('Y-m-d');
                                            }
                                        }

                                        // Get confirmed booking dates
                                        $bookingDates = \DB::table('bookings')
                                            ->where('product_id', $productId)
                                            ->where('status', 'Confirmed')
                                            ->pluck('booking_date')
                                            ->map(function ($date) {
                                                return Carbon::parse($date)->format('Y-m-d');
                                            })
                                            ->toArray();

                                        return array_merge($unavailableDates, $bookingDates);
                                    } catch (\Exception $e) {
                                        \Log::error('Error getting disabled dates: ' . $e->getMessage());
                                        return [];
                                    }
                                })
                                ->helperText('Select the date the customer wants to use the product. Earliest available date: '
                                    . now()->addDays(2)->startOfDay()->format('F j, Y')),
                        ]),
                ])
                ->action(function (array $data): void {
                    // Validate product exists and belongs to the admin
                    $product = Products::find($data['product_id']);

                    if (!$product || $product->user_id !== Auth::id()) {
                        Notification::make()
                            ->title('Error')
                            ->body('You can only book your own products.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Check if product is rentable
                    if (!$product->isRentable()) {
                        Notification::make()
                            ->title('Product Unavailable')
                            ->body('This product is currently under maintenance and cannot be booked.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Check if date is available (considering confirmed bookings and rentals)
                    $bookingDate = Carbon::parse($data['booking_date'])->startOfDay();  // FIXED: added startOfDay()
                    $today = now()->startOfDay();  // For comparison
                    $minimumDate = $today->copy()->addDays(2);  // Earliest allowed date

                    // Validate booking date is at least 2 days from now
                    if ($bookingDate->lt($minimumDate)) {  // FIXED: Use lt() (less than) instead of diffInDays()
                        Notification::make()
                            ->title('Invalid Booking Date')
                            ->body('Booking date must be at least 2 days from today to allow time for confirmation. Earliest allowed date: '
                                . $minimumDate->format('F j, Y'))
                            ->danger()
                            ->send();
                        return;
                    }

                    if (!$product->isDateAvailable($bookingDate, true)) {  // true = check confirmed bookings too
                        Notification::make()
                            ->title('Date Unavailable')
                            ->body('The selected date is not available for this product. Please choose another date.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Determine the user_id for the booking
                    $userId = null;
                    if ($data['client_type'] === 'user') {
                        $userId = $data['client_id'];
                    } else {
                        $customer = Customers::find($data['client_id']);
                        if ($customer?->user_id) {
                            $userId = $customer->user_id;
                        } else {
                            Notification::make()
                                ->title('Cannot Create Booking')
                                ->body('Bookings for customers without accounts are not currently supported.')
                                ->warning()
                                ->send();
                            return;
                        }
                    }

                    // Create the booking with Pending status
                    Bookings::create([
                        'user_id' => $userId,
                        'created_by' => Auth::id(),
                        'product_id' => $data['product_id'],
                        'booking_date' => $bookingDate,
                        'status' => 'Pending',
                        'notes' => 'Booking request created. Awaiting confirmation.',
                    ]);

                    // Get client name for notification
                    $clientName = '';
                    if ($data['client_type'] === 'user') {
                        $user = User::find($data['client_id']);
                        $clientName = $user ? $user->name : 'Unknown';
                    } else {
                        $customer = Customers::find($data['client_id']);
                        $clientName = $customer ? "{$customer->first_name} {$customer->last_name}" : 'Unknown';
                    }

                    // Calculate confirmation deadline
                    $confirmationDeadline = $bookingDate->copy()->subDay()->format('F j, Y');

                    Notification::make()
                        ->title('Booking Request Created')
                        ->body("Booking request for {$product->name} has been created for {$clientName} on "
                            . $bookingDate->format('F j, Y') . ". Please confirm booking by {$confirmationDeadline}.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product.firstProductImage')
                    ->label('Product Image')
                    ->disk('public')
                    ->getStateUsing(function ($record) {
                        return $record->product->product_images->first()?->thumbnail_image;
                    })
                    ->defaultImageUrl(function ($record) {
                        return $record->product->product_images->first()?->image_path;
                    }),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('booking_date')
                    ->label('Booking Date')
                    ->date('F j, Y')
                    ->sortable()
                    ->description(function ($record) {
                        if ($record->status === 'Pending') {
                            $daysUntilBooking = Carbon::parse($record->booking_date)->diffInDays(now());
                            $confirmationDeadline = Carbon::parse($record->booking_date)->subDay();

                            if ($confirmationDeadline->isToday()) {
                                return 'Confirm by END OF TODAY';
                            } elseif ($confirmationDeadline->isPast()) {
                                return 'OVERDUE: Should have been confirmed by ' . $confirmationDeadline->format('M j');
                            } else {
                                return 'Confirm by ' . $confirmationDeadline->format('M j');
                            }
                        }
                        return null;
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Confirmed' => 'success',
                        'Completed' => 'info',
                        'Cancelled' => 'danger',
                        'No Show' => 'gray',
                        default => 'info',
                    })
                    ->tooltip(function ($record) {
                        if ($record->status === 'Pending') {
                            $confirmationDeadline = Carbon::parse($record->booking_date)->subDay();
                            return 'Needs confirmation by ' . $confirmationDeadline->format('F j, Y');
                        }
                        return null;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending - Needs Confirmation',
                        'Confirmed' => 'Confirmed - Product Reserved',
                        'Completed' => 'Completed - Converted to Rental',
                        'Cancelled' => 'Cancelled',
                        'No Show' => 'No Show',
                    ])
                    ->label('Booking Status')
                    ->placeholder('All Statuses'),
                Filter::make('needs_confirmation')
                    ->label('Needs Confirmation Today')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('status', 'Pending')
                        ->whereDate('booking_date', '<=', now()->addDay()))
                    ->toggle(),
                Filter::make('urgent_confirmation')
                    ->label('URGENT: Confirm Today')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('status', 'Pending')
                        ->whereDate('booking_date', '=', now()->addDay()))
                    ->toggle(),
                Filter::make('overdue_confirmation')
                    ->label('Overdue Confirmation')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('status', 'Pending')
                        ->whereDate('booking_date', '=', now()))
                    ->toggle(),
                Filter::make('pending_bookings')
                    ->label('All Pending Bookings')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'Pending'))
                    ->toggle(),
                Filter::make('upcoming_confirmed')
                    ->label('Upcoming Confirmed')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('status', 'Confirmed')
                        ->whereDate('booking_date', '>=', now()))
                    ->toggle(),
                Filter::make('booking_date')
                    ->form([
                        DatePicker::make('booking_from')
                            ->label('Booking From')
                            ->placeholder('Select start date'),
                        DatePicker::make('booking_until')
                            ->label('Booking Until')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['booking_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['booking_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    })
                    ->label('Booking Date Range'),
            ])
            ->actions([
                ActionGroup::make([
                    // View Action
                    ViewAction::make('viewBooking')
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Booking Details')
                        ->modalDescription('Review the details of this booking.')
                        ->form(function (Bookings $record) {
                            $confirmationDeadline = Carbon::parse($record->booking_date)->subDay();
                            $isOverdue = $record->status === 'Pending' && $confirmationDeadline->isPast();
                            $needsConfirmationToday = $record->status === 'Pending' && $confirmationDeadline->isToday();

                            return [
                                Section::make('Booking Information')
                                    ->schema([
                                        Placeholder::make('customer_info')
                                            ->label('Customer')
                                            ->content($record->user?->name ?? 'Unknown'),
                                        Placeholder::make('product_info')
                                            ->label('Product')
                                            ->content($record->product?->name ?? 'Unknown'),
                                        Placeholder::make('status_info')
                                            ->label('Status')
                                            ->content($record->status)
                                            ->badge()
                                            ->color(match ($record->status) {
                                                'Pending' => $isOverdue ? 'danger' : 'warning',
                                                'Confirmed' => 'success',
                                                'Completed' => 'info',
                                                'Cancelled' => 'danger',
                                                default => 'info'
                                            }),
                                        Placeholder::make('booking_date_info')
                                            ->label('Booking Date')
                                            ->content($record->booking_date->format('F j, Y')),
                                        Placeholder::make('confirmation_deadline')
                                            ->label('Confirmation Deadline')
                                            ->content($confirmationDeadline->format('F j, Y'))
                                            ->badge()
                                            ->color($isOverdue ? 'danger' : ($needsConfirmationToday ? 'warning' : 'info'))
                                            ->visible($record->status === 'Pending'),
                                        Placeholder::make('confirmation_status')
                                            ->label('Confirmation Status')
                                            ->content($isOverdue
                                                ? 'OVERDUE: Confirmation missed!'
                                                : ($needsConfirmationToday
                                                    ? 'URGENT: Confirm TODAY!'
                                                    : ($record->status === 'Pending' ? 'Awaiting confirmation' : 'Confirmed')))
                                            ->badge()
                                            ->color($isOverdue ? 'danger' : ($needsConfirmationToday ? 'warning' : 'info'))
                                            ->visible($record->status !== 'Cancelled'),
                                    ])
                                    ->columns(2),
                            ];
                        }),
                    // Confirm Booking Action - only for Pending bookings
                    // Confirm Booking Action - only for Pending bookings
                    Action::make('confirmBooking')
                        ->label('Confirm Booking')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn($record) =>
                            $record->status === 'Pending' &&
                            Carbon::parse($record->booking_date)->subDay()->isToday())
                        ->modalHeading('Confirm Booking')
                        ->modalDescription('Confirm this booking and reserve the product for the customer.')
                        ->form([
                            Placeholder::make('confirmation_deadline_info')
                                ->label('Confirmation Deadline')
                                ->content(function ($record) {
                                    $deadline = Carbon::parse($record->booking_date)->subDay();
                                    return 'Today is the confirmation deadline! Must be confirmed by end of today.';
                                }),
                            Textarea::make('confirmation_notes')
                                ->label('Confirmation Notes (Optional)')
                                ->placeholder('Add any notes about the confirmation...')
                                ->rows(3),
                        ])
                        ->action(function ($record, array $data) {
                            // Check if it's still the confirmation day (day before booking)
                            if (!Carbon::parse($record->booking_date)->subDay()->isToday()) {
                                Notification::make()
                                    ->title('Confirmation Deadline Passed')
                                    ->body('The confirmation deadline was yesterday. This booking can no longer be confirmed.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Check if booking is too late to confirm (past booking date)
                            if ($record->booking_date->isPast()) {
                                Notification::make()
                                    ->title('Cannot Confirm Past Booking')
                                    ->body('Booking date has already passed. Please cancel this booking.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Check if product is still available (excluding this booking)
                            // Check for conflicting CONFIRMED bookings (excluding this one)
                            $conflictingBookings = Bookings::where('product_id', $record->product_id)
                                ->where('booking_date', $record->booking_date)
                                ->where('status', 'Confirmed')  // Only check CONFIRMED bookings
                                ->where('booking_id', '!=', $record->booking_id)  // Exclude this booking
                                ->exists();

                            if ($conflictingBookings) {
                                Notification::make()
                                    ->title('Product No Longer Available')
                                    ->body('Another customer has already confirmed this product for this date. Please cancel this booking.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Check if product is rented on this date using your existing method
                            // Use isDateAvailable() but exclude this booking ID
                            if (!$record->product->isDateAvailable($record->booking_date, null, $record->booking_id)) {
                                Notification::make()
                                    ->title('Product No Longer Available')
                                    ->body('The product is already rented or has another booking for this date. Please cancel this booking.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Update booking status
                            $record->update([
                                'status' => 'Confirmed',
                                'notes' => 'Booking confirmed on ' . now()->format('Y-m-d')
                                    . ($data['confirmation_notes'] ? "\nNotes: " . $data['confirmation_notes'] : ''),
                            ]);

                            // Reserve the product
                            $record->product->update([
                                'status' => 'Reserved',
                            ]);

                            Notification::make()
                                ->title('Booking Confirmed')
                                ->body("Booking for {$record->product->name} has been confirmed and product is now reserved for "
                                    . $record->booking_date->format('F j, Y'))
                                ->success()
                                ->send();
                        }),
                    // Cancel Booking Action
                    Action::make('cancelBooking')
                        ->label('Cancel Booking')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn($record) => in_array($record->status, ['Pending', 'Confirmed']))
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Booking')
                        ->modalDescription('Are you sure you want to cancel this booking?')
                        ->form([
                            Select::make('cancellation_reason')
                                ->label('Cancellation Reason')
                                ->options([
                                    'customer_cancelled' => 'Customer Cancelled',
                                    'no_response' => 'No Response from Customer',
                                    'product_unavailable' => 'Product Unavailable',
                                    'other' => 'Other',
                                ])
                                ->required(),
                            Textarea::make('cancellation_notes')
                                ->label('Cancellation Notes')
                                ->placeholder('Add details about the cancellation...')
                                ->rows(3),
                        ])
                        ->action(function ($record, array $data) {
                            // If booking was confirmed, make product available again
                            if ($record->status === 'Confirmed') {
                                $record->product->update([
                                    'status' => 'Available',
                                ]);
                            }

                            $record->update([
                                'status' => 'Cancelled',
                                'notes' => 'Cancelled on ' . now()->format('Y-m-d')
                                    . "\nReason: " . $data['cancellation_reason']
                                    . ($data['cancellation_notes'] ? "\nNotes: " . $data['cancellation_notes'] : ''),
                            ]);

                            Notification::make()
                                ->title('Booking Cancelled')
                                ->body('The booking has been cancelled successfully.')
                                ->success()
                                ->send();
                        }),
                    // Mark as No Show Action - for Confirmed bookings after booking date
                    Action::make('markNoShow')
                        ->label('Mark as No Show')
                        ->icon('heroicon-o-user-minus')
                        ->color('warning')
                        ->visible(fn($record) =>
                            $record->status === 'Confirmed' &&
                            $record->booking_date->isPast())
                        ->requiresConfirmation()
                        ->modalHeading('Mark as No Show')
                        ->modalDescription('Customer did not show up for their confirmed booking?')
                        ->action(function ($record) {
                            // Make product available again
                            $record->product->update([
                                'status' => 'Available',
                            ]);

                            $record->update([
                                'status' => 'No Show',
                                'notes' => ($record->notes ? $record->notes . "\n" : '')
                                    . 'Marked as No Show on ' . now()->format('Y-m-d'),
                            ]);

                            Notification::make()
                                ->title('Marked as No Show')
                                ->body('Booking has been marked as No Show. Product is now available again.')
                                ->warning()
                                ->send();
                        }),
                    Action::make('convertToRental')
                        ->label('Convert to Rental')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('primary')
                        ->visible(fn($record) =>
                            $record->status === 'Confirmed' &&
                            ($record->booking_date->isToday() || $record->booking_date->isPast()))
                        ->url(fn($record) => route('filament.admin.resources.rentals.create', [
                            'booking_id' => $record->booking_id,
                            'product_id' => $record->product_id,
                            'user_id' => $record->user_id,
                            'prefill' => 'true',
                        ])),
                ])
                    ->label('Manage')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this booking'),
            ]);
    }
}
