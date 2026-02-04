<?php

namespace App\Filament\Resources\Rentals\Schemas;

use App\Models\Bookings;
use App\Models\Customers;
use App\Models\Products;
use App\Models\Rentals;
use App\Models\User;
use App\Services\RentalBusinessRules;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;

class RentalsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rental Information')
                    ->description('Configure the rental details and schedule')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Group::make()
                            ->schema([
                                // Add hidden fields to track booking conversion
                                Hidden::make('from_booking')
                                    ->default(fn() => request()->has('prefill') && request('prefill') === 'true'),
                                Hidden::make('booking_id')
                                    ->default(fn() => request()->has('booking_id') ? request('booking_id') : null),
                                Select::make('product_id')
                                    ->options(function () {
                                        // Show all available products for the current user
                                        return Products::where('user_id', auth()->id())
                                            ->get()
                                            ->mapWithKeys(function ($product) {
                                                $size = $product->size ?? 'N/A';
                                                return [$product->product_id => "{$product->name} | Size: {$size} | ₱" . number_format($product->rental_price, 2)];
                                            });
                                    })
                                    ->label('Product')
                                    ->required()
                                    ->live()
                                    ->searchable()
                                    ->default(function () {
                                        // Prefill product_id from booking if present
                                        if (request()->has('product_id') && request()->has('prefill') && request('prefill') === 'true') {
                                            $product = Products::find(request('product_id'));
                                            // Only prefill if product belongs to current user
                                            if ($product && $product->user_id == auth()->id()) {
                                                return request('product_id');
                                            }
                                        }
                                        return null;
                                    })
                                    ->disabled(function () {
                                        // Disable if prefilling from booking
                                        return request()->has('product_id') && request()->has('prefill') && request('prefill') === 'true';
                                    })
                                    ->afterStateHydrated(function ($component, $state, callable $set) {
                                        // When state is hydrated (loaded), update the rental price
                                        if ($state) {
                                            $product = Products::find($state);
                                            if ($product) {
                                                $set('rental_price', $product->rental_price);
                                                $set('product_name', $product->name);
                                            }
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Products::find($state);
                                        if ($product) {
                                            $set('rental_price', $product->rental_price);
                                            $set('product_name', $product->name);
                                        } else {
                                            $set('rental_price', null);
                                            $set('product_name', null);
                                        }
                                        // Reset payment calculations when product changes
                                        $set('amount_paid', 0);
                                        $set('deposit_amount', 0);
                                    })
                                    ->rules(['required', 'exists:products,product_id']),
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
                                Hidden::make('product_name'),
                                TextInput::make('rental_price')
                                    ->label('Rental Price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('₱')
                                    ->disabled()
                                    ->dehydrated()
                                    ->formatStateUsing(fn($state) => $state ? number_format($state, 2) : '')
                                    ->afterStateHydrated(function ($component, $state, callable $set, callable $get) {
                                        // When rental_price is hydrated, also update from product if needed
                                        if (!$state && $get('product_id')) {
                                            $product = Products::find($get('product_id'));
                                            if ($product) {
                                                $set('rental_price', $product->rental_price);
                                            }
                                        }
                                    }),
                                Select::make('client_type')
                                    ->label('Client Type')
                                    ->options([
                                        'customer' => 'Customer (No Account)',
                                        'user' => 'Registered User (Has Account)',
                                    ])
                                    ->default(function () {
                                        // Always default to 'user' when converting from booking
                                        if (request()->has('user_id') && request()->has('prefill') && request('prefill') === 'true') {
                                            return 'user';
                                        }
                                        return 'customer';
                                    })
                                    ->required()
                                    ->native(false)
                                    ->reactive()
                                    ->disabled(function () {
                                        // Disable if prefilling from booking (bookings are always for registered users)
                                        return request()->has('prefill') && request('prefill') === 'true';
                                    })
                                    ->helperText('Select whether the rental is for a registered user or a customer without an account.'),
                                Select::make('client_id')
                                    ->label('Client')
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search, callable $get) {
                                        if ($get('client_type') === 'user') {
                                            // Lazy search for users (do not preload)
                                            return User::where('role', 'User')
                                                ->where(function ($query) use ($search) {
                                                    $query
                                                        ->where('name', 'like', "%{$search}%")
                                                        ->orWhere('email', 'like', "%{$search}%");
                                                })
                                                ->limit(10)
                                                ->get()
                                                ->mapWithKeys(fn($u) => [$u->id => "{$u->name} - {$u->email}"])
                                                ->toArray();
                                        }

                                        // For customers, return all
                                        return Customers::where('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%")
                                            ->orWhere('phone_number', 'like', "%{$search}%")
                                            ->get()
                                            ->mapWithKeys(fn($c) => [$c->customer_id => "{$c->first_name} {$c->last_name} - {$c->phone_number}"])
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(function ($value, callable $get) {
                                        if ($get('client_type') === 'user') {
                                            $user = User::find($value);
                                            return $user ? "{$user->name} - {$user->email}" : null;
                                        }

                                        $customer = Customers::where('customer_id', $value)->first();
                                        return $customer ? "{$customer->first_name} {$customer->last_name} - {$customer->phone_number}" : null;
                                    })
                                    ->required()
                                    ->default(function () {
                                        // Prefill client_id from booking if present
                                        if (request()->has('user_id') && request()->has('prefill') && request('prefill') === 'true') {
                                            return request('user_id');
                                        }
                                        return null;
                                    })
                                    ->disabled(function () {
                                        // Disable if prefilling from booking
                                        return request()->has('user_id') && request()->has('prefill') && request('prefill') === 'true';
                                    })
                                    ->helperText(function (callable $get) {
                                        if (request()->has('prefill') && request('prefill') === 'true') {
                                            return 'Prefilled from booking record';
                                        }
                                        return 'Search by name, email, or phone number based on client type.';
                                    }),
                            ]),
                        Group::make()
                            ->schema([
                                DatePicker::make('pickup_date')
                                    ->label('Pickup Date')
                                    ->required()
                                    ->minDate(now()->startOfDay())  // CHANGED: Can be today for rentals (not +1 day)
                                    ->native(false)
                                    ->displayFormat('F j, Y')
                                    ->live()
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $productId = $get('product_id');
                                        $returnDate = $get('return_date');

                                        if (!$productId || !$returnDate) {
                                            return;
                                        }

                                        // Auto-adjust event date if pickup is after event
                                        $eventDate = $get('event_date');
                                        if ($eventDate && Carbon::parse($state)->gt(Carbon::parse($eventDate))) {
                                            $set('event_date', Carbon::parse($state)->addDay()->format('Y-m-d'));
                                        }
                                        // Auto-adjust return date if pickup is after return
                                        if ($returnDate && Carbon::parse($state)->gte(Carbon::parse($returnDate))) {
                                            $set('return_date', Carbon::parse($state)->addDays(2)->format('Y-m-d'));
                                        }

                                        // Check for date conflicts
                                        $product = Products::find($productId);
                                        if ($product && $returnDate && $product->hasDateConflict($state, $returnDate)) {
                                            // Clear all date fields
                                            $set('pickup_date', null);
                                            $set('event_date', null);
                                            $set('return_date', null);

                                            // Get conflicting rental details
                                            $conflictingRentals = $product
                                                ->rentals()
                                                ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
                                                ->where('pickup_date', '<=', Carbon::parse($returnDate))
                                                ->where('return_date', '>=', Carbon::parse($state))
                                                ->get();

                                            $conflictDates = $conflictingRentals->map(function ($rental) {
                                                $start = Carbon::parse($rental->pickup_date)->format('M j, Y');
                                                $end = Carbon::parse($rental->return_date)->format('M j, Y');
                                                return "Rental #{$rental->rental_id}: {$start} to {$end}";
                                            })->implode(', ');

                                            // Trigger notification
                                            \Filament\Notifications\Notification::make()
                                                ->title('Date Conflict Detected!')
                                                ->body("The selected date range conflicts with existing rentals: {$conflictDates}. All date fields have been cleared. Please select different dates.")
                                                ->danger()
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->iconColor('danger')
                                                ->duration(8000)
                                                ->send();
                                        }
                                    })
                                    ->helperText('Select pickup date. Can be today or any future date. Red dates are unavailable.')
                                    ->rules(['required', 'date', 'after_or_equal:today']),  // CHANGED: after:today to after_or_equal:today
                                DatePicker::make('event_date')
                                    ->label('Event Date')
                                    ->required()
                                    ->minDate(now()->startOfDay())  // CHANGED: Can be today
                                    ->native(false)
                                    ->displayFormat('F j, Y')
                                    ->live()
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $productId = $get('product_id');
                                        $pickupDate = $get('pickup_date');
                                        $returnDate = $get('return_date');

                                        if (!$productId || !$pickupDate || !$returnDate) {
                                            return;
                                        }

                                        // Auto-adjust return date if event is after return
                                        if ($returnDate && Carbon::parse($state)->gte(Carbon::parse($returnDate))) {
                                            $set('return_date', Carbon::parse($state)->addDay()->format('Y-m-d'));
                                        }

                                        // Check for date conflicts
                                        $product = Products::find($productId);
                                        if ($product && $product->hasDateConflict($pickupDate, $returnDate)) {
                                            // Clear all date fields
                                            $set('pickup_date', null);
                                            $set('event_date', null);
                                            $set('return_date', null);

                                            // Get conflicting rental details
                                            $conflictingRentals = $product
                                                ->rentals()
                                                ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
                                                ->where('pickup_date', '<=', Carbon::parse($returnDate))
                                                ->where('return_date', '>=', Carbon::parse($pickupDate))
                                                ->get();

                                            $conflictDates = $conflictingRentals->map(function ($rental) {
                                                $start = Carbon::parse($rental->pickup_date)->format('M j, Y');
                                                $end = Carbon::parse($rental->return_date)->format('M j, Y');
                                                return "Rental #{$rental->rental_id}: {$start} to {$end}";
                                            })->implode(', ');

                                            // Trigger notification
                                            \Filament\Notifications\Notification::make()
                                                ->title('Date Conflict Detected!')
                                                ->body("The selected date range conflicts with existing rentals: {$conflictDates}. All date fields have been cleared. Please select different dates.")
                                                ->danger()
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->iconColor('danger')
                                                ->duration(8000)
                                                ->send();
                                        }
                                    })
                                    ->helperText('Date of the actual event. Must be on or after pickup date. Red dates are unavailable.')
                                    ->rules(['required', 'date', 'after_or_equal:pickup_date']),
                                DatePicker::make('return_date')
                                    ->label('Return Date')
                                    ->required()
                                    ->minDate(now()->startOfDay())  // CHANGED: Can be today
                                    ->native(false)
                                    ->displayFormat('F j, Y')
                                    ->live()
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
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $productId = $get('product_id');
                                        $pickupDate = $get('pickup_date');

                                        if (!$productId || !$pickupDate) {
                                            return;
                                        }

                                        // Check for date conflicts
                                        $product = Products::find($productId);
                                        if ($product && $product->hasDateConflict($pickupDate, $state)) {
                                            // Clear all date fields
                                            $set('pickup_date', null);
                                            $set('event_date', null);
                                            $set('return_date', null);

                                            // Get conflicting rental details
                                            $conflictingRentals = $product
                                                ->rentals()
                                                ->whereNotIn('rental_status', ['Returned', 'Cancelled'])
                                                ->where('pickup_date', '<=', Carbon::parse($state))
                                                ->where('return_date', '>=', Carbon::parse($pickupDate))
                                                ->get();

                                            $conflictDates = $conflictingRentals->map(function ($rental) {
                                                $start = Carbon::parse($rental->pickup_date)->format('M j, Y');
                                                $end = Carbon::parse($rental->return_date)->format('M j, Y');
                                                return "Rental #{$rental->rental_id}: {$start} to {$end}";
                                            })->implode(', ');

                                            // Trigger notification
                                            \Filament\Notifications\Notification::make()
                                                ->title('Date Conflict Detected!')
                                                ->body("The selected date range conflicts with existing rentals: {$conflictDates}. All date fields have been cleared. Please select different dates.")
                                                ->danger()
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->iconColor('danger')
                                                ->duration(8000)
                                                ->send();
                                        }
                                    })
                                    ->helperText('When the product should be returned. Must be on or after event date. Red dates are unavailable.')
                                    ->rules(['required', 'date', 'after_or_equal:event_date']),
                                Placeholder::make('rental_period')
                                    ->label('Rental Period')
                                    ->content(function (callable $get) {
                                        $pickup = $get('pickup_date');
                                        $return = $get('return_date');

                                        if ($pickup && $return) {
                                            $days = Carbon::parse($pickup)->diffInDays(Carbon::parse($return));
                                            return "{$days} day(s)";
                                        }

                                        return 'Select dates to see rental period';
                                    }),
                                Hidden::make('has_date_conflict')
                                    ->default(false),
                            ]),
                    ])
                    ->columns(2),
                Section::make('Payment')
                    ->description('Simple and flexible: optional deposit, payment, and automatic balance calculation')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        TextInput::make('deposit_amount')
                            ->label('Deposit (optional)')
                            ->numeric()
                            ->prefix('₱')
                            ->placeholder('0')
                            ->helperText("Optional refundable deposit. Leave as 0 if you don't collect deposits."),
                        TextInput::make('amount_paid')
                            ->label('Initial Payment (optional)')
                            ->numeric()
                            ->prefix('₱')
                            ->placeholder('0')
                            ->minValue(0)
                            ->maxValue(function ($get) {
                                return (float) ($get('rental_price') ?? 0);
                            })
                            ->helperText('Enter any amount up to the rental price. You can add more payments later from this rental record. Leave as 0 if you want to collect the full amount later.'),
                        Placeholder::make('balance_preview')
                            ->label('Balance Due')
                            ->content(function ($get) {
                                $price = (float) ($get('rental_price') ?? 0);
                                $paid = (float) ($get('amount_paid') ?? 0);
                                return '₱ ' . number_format(max(0, $price - $paid), 2);
                            })
                            ->helperText('Calculated as Rental Price minus Payment Now. Deposit does not reduce balance.'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
