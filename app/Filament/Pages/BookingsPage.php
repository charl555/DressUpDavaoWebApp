<?php

namespace App\Filament\Pages;

use App\Models\Bookings;
use App\Models\Customers;
use App\Models\Products;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
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

class BookingsPage extends Page implements HasTable, hasSchemas
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
                ->label('Make Reservation')
                ->icon('heroicon-o-calendar-days')
                ->color('primary')
                ->modalHeading('Create New Reservation')
                ->modalDescription('Book a product for a client. Select the client type, choose a product, and pick a reservation date.')
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
                        ->description('Choose a product to reserve. Products under maintenance are not shown.')
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
                                                $product->product_id => "{$product->name} | Size: {$size} | ₱" . number_format($product->rental_price, 2)
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
                    // Date Selection Section
                    Section::make('Reservation Date')
                        ->description('Select a date for the reservation. Dates with existing rentals or bookings are disabled.')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            DatePicker::make('booking_date')
                                ->label('Reservation Date')
                                ->required()
                                ->minDate(now()->addDay())
                                ->native(false)
                                ->displayFormat('F j, Y')
                                ->disabledDates(function (callable $get) {
                                    $productId = $get('product_id');
                                    if (!$productId) {
                                        return [];
                                    }
                                    $product = Products::find($productId);
                                    if (!$product) {
                                        return [];
                                    }
                                    // Get all unavailable dates for this product
                                    $unavailableRanges = $product->getUnavailableDateRanges();
                                    return array_keys($unavailableRanges);
                                }),
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

                    // Check if date is available
                    if (!$product->isDateAvailable($data['booking_date'])) {
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
                        // For customers, check if they have an associated user
                        $customer = Customers::find($data['client_id']);
                        if ($customer?->user_id) {
                            $userId = $customer->user_id;
                        } else {
                            // Customer has no associated user - can't create standard booking
                            Notification::make()
                                ->title('Cannot Create Booking')
                                ->body('Bookings for customers without accounts are not currently supported. Please use "Add New Rental" instead or select a registered user.')
                                ->warning()
                                ->send();
                            return;
                        }
                    }

                    // Create the booking
                    Bookings::create([
                        'user_id' => $userId,
                        'created_by' => Auth::id(),
                        'product_id' => $data['product_id'],
                        'booking_date' => $data['booking_date'],
                        'status' => 'On Going',
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

                    Notification::make()
                        ->title('Reservation Created Successfully')
                        ->body("Booking for {$product->name} has been created for {$clientName} on " . \Carbon\Carbon::parse($data['booking_date'])->format('F j, Y') . '.')
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
                        // Get the first product image's thumbnail
                        return $record->product->product_images->first()?->thumbnail_image;
                    })
                    ->defaultImageUrl(function ($record) {
                        // Fallback to first product image if thumbnail doesn't exist
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
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'On Going' => 'info',
                        'Confirmed' => 'success',
                        'Cancelled' => 'gray',
                        'Completed' => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('exclude_cancelled')
                    ->label('Exclude Cancelled Bookings')
                    ->default()
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Cancelled'))
                    ->toggle()
                    ->indicateUsing(function () {
                        return 'Excluding cancelled bookings';
                    }),
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'On Going' => 'On Going',
                        'Confirmed' => 'Confirmed',
                        'Cancelled' => 'Cancelled',
                        'Completed' => 'Completed',
                    ])
                    ->label('Booking Status')
                    ->placeholder('All Statuses'),
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
                Filter::make('exclude_cancelled')
                    ->label('Exclude Cancelled Bookings')
                    ->default()
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Cancelled'))
                    ->toggle()
                    ->indicateUsing(function () {
                        return 'Excluding cancelled bookings';
                    }),
                Filter::make('exclude_cancelled')
                    ->label('Exclude Cancelled Bookings')
                    ->default()
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Cancelled'))
                    ->toggle()
                    ->indicateUsing(function () {
                        return 'Excluding cancelled bookings';
                    }),
                Filter::make('exclude_completed')
                    ->label('Exclude Completed Bookings')
                    ->query(fn(Builder $query): Builder => $query->where('status', '!=', 'Completed'))
                    ->toggle(),
                Filter::make('upcoming_bookings')
                    ->query(fn(Builder $query): Builder => $query->where('booking_date', '>=', now()))
                    ->toggle()
                    ->label('Upcoming Bookings Only'),
                Filter::make('past_bookings')
                    ->query(fn(Builder $query): Builder => $query->where('booking_date', '<', now()))
                    ->toggle()
                    ->label('Past Bookings Only'),
                Filter::make('today_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereDate('booking_date', now()))
                    ->toggle()
                    ->label("Today's Bookings Only"),
                Filter::make('tomorrow_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereDate('booking_date', now()->addDay()))
                    ->toggle()
                    ->label("Tomorrow's Bookings Only"),
                Filter::make('yesterday_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereDate('booking_date', now()->subDay()))
                    ->toggle()
                    ->label("Yesterday's Bookings Only"),
                Filter::make('this_week_bookings')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle()
                    ->label("This Week's Bookings Only"),
            ])
            ->actions([
                ActionGroup::make([
                    // 👁 View Action — visible only when record is Completed or Cancelled
                    ViewAction::make('viewBooking')
                        ->visible(fn($record) => in_array($record->status, ['Completed', 'Cancelled']))
                        ->label('View Details')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Booking Details')
                        ->modalDescription('Review the details of this booking.')
                        ->form(function (Bookings $record) {
                            return [
                                Section::make('Booking Information')
                                    ->schema([
                                        TextInput::make('user.name')
                                            ->label('Customer')
                                            ->disabled(),
                                        TextInput::make('product.name')
                                            ->label('Product')
                                            ->disabled(),
                                        TextInput::make('status')
                                            ->label('Status')
                                            ->disabled(),
                                        DatePicker::make('booking_date')
                                            ->label('Booking Date')
                                            ->disabled(),
                                    ])
                                    ->columns(2),
                            ];
                        }),
                    // ✏️ Edit Action — hidden when Completed or Cancelled
                    EditAction::make('editBooking')
                        ->visible(fn($record) => !in_array($record->status, ['Completed', 'Cancelled']))
                        ->label('Edit Booking')
                        ->icon('heroicon-o-pencil-square')
                        ->modalHeading('Edit Booking')
                        ->modalDescription('Modify the booking date or details below.')
                        ->form(function (Bookings $record) {
                            return [
                                Section::make('Booking Information')
                                    ->schema([
                                        TextInput::make('user.name')
                                            ->label('Customer')
                                            ->disabled(),
                                        TextInput::make('product.name')
                                            ->label('Product')
                                            ->disabled(),
                                        TextInput::make('status')
                                            ->label('Status')
                                            ->disabled(),
                                        DatePicker::make('booking_date')
                                            ->label('Booking Date')
                                            ->required()
                                            ->minDate(now()->addDay())
                                            ->native(false)
                                            ->displayFormat('F j, Y'),
                                    ])
                                    ->columns(2),
                            ];
                        }),
                    // ✅ Complete Booking — only visible if today == booking_date and not completed/cancelled
                    Action::make('completeBooking')
                        ->hidden(fn($record) => in_array($record->status, ['Completed', 'Cancelled']))
                        ->visible(fn($record) =>
                            $record->booking_date->isSameDay(now()) ||
                            $record->booking_date->isPast())
                        ->label('Complete Booking')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Complete Booking')
                        ->modalDescription('Are you sure you want to complete this booking?')
                        ->action(function ($record) {
                            $record->update(['status' => 'Completed']);

                            if ($record->product) {
                                $record->product->update(['status' => 'Available']);
                            }

                            // Send Filament notification
                            Notification::make()
                                ->success()
                                ->title('Booking Completed')
                                ->body('The booking has been successfully marked as completed.')
                                ->send();
                        }),
                    // ⚠️ Cancel Booking — hidden when Completed or Cancelled
                    Action::make('cancelBooking')
                        ->hidden(fn($record) => in_array($record->status, ['Completed', 'Cancelled']))
                        ->label('Cancel Booking')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Booking')
                        ->modalDescription('Are you sure you want to cancel this booking? This action cannot be undone.')
                        ->action(function ($record) {
                            $record->update(['status' => 'Cancelled']);

                            if ($record->product) {
                                $record->product->update(['status' => 'Available']);
                            }
                            Notification::make()
                                ->success()
                                ->title('Booking Cancelled')
                                ->body('The booking has been successfully cancelled.')
                                ->send();
                        }),
                    Action::make('createRental')
                        ->visible(fn($record) => $record->status === 'Completed')
                        ->label('Create Rental')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->url(fn($record) => route('filament.admin.resources.rentals.create', ['booking_id' => $record->booking_id])),
                ])
                    ->label('Manage')
                    ->button()
                    ->color('primary')
                    ->outlined()
                    ->tooltip('Manage this booking'),
            ]);
    }
}
