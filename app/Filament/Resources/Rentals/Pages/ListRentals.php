<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalsResource;
use App\Models\Bookings;
use App\Models\Customers;
use App\Models\Products;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;

class ListRentals extends ListRecords
{
    protected static string $resource = RentalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bookProduct')
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
                        // For customers without accounts, we need to handle this differently
                        // The Bookings table requires a user_id, so for customers we'll create
                        // the booking with the admin's ID and note in the status
                        // Actually, checking the Bookings model - it requires user_id
                        // Let's check if customer has an associated user
                        $customer = Customers::find($data['client_id']);
                        if ($customer && $customer->user_id) {
                            // Use the user associated with the customer
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
            CreateAction::make()->label('Add New Rental'),
        ];
    }
}
