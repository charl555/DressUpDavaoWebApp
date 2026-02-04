<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalsResource;
use App\Models\Bookings;
use App\Models\Customers;
use App\Models\Products;
use App\Models\Rentals;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRentals extends CreateRecord
{
    protected static string $resource = RentalsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            // --- Step 1: Validate product existence ---
            $product = Products::find($data['product_id']);
            if (!$product) {
                throw new \Exception('Product not found.');
            }

            // --- Step 2: Validate client type and ID ---
            $clientType = $data['client_type'] ?? null;
            $clientId = $data['client_id'] ?? null;

            if (!$clientType || !$clientId) {
                throw new \Exception('Please select a client type and client.');
            }

            $customerId = null;
            $userId = null;

            if ($clientType === 'customer') {
                $customer = Customers::find($clientId);
                if (!$customer) {
                    throw new \Exception('Selected customer not found.');
                }
                $customerId = $clientId;
            } elseif ($clientType === 'user') {
                $user = User::where('role', 'User')->find($clientId);
                if (!$user) {
                    throw new \Exception('Selected user not found.');
                }
                $userId = $clientId;
            } else {
                throw new \Exception('Invalid client type selected.');
            }

            // --- Step 3: Validate date logic ---
            $pickupDate = \Carbon\Carbon::parse($data['pickup_date']);
            $eventDate = \Carbon\Carbon::parse($data['event_date']);
            $returnDate = \Carbon\Carbon::parse($data['return_date']);

            if ($pickupDate->isBefore(today())) {
                throw new \Exception('Pickup date cannot be in the past.');
            }

            if ($eventDate->lt($pickupDate)) {
                throw new \Exception('Event date cannot be before pickup date.');
            }

            if ($returnDate->lt($eventDate)) {
                throw new \Exception('Return date cannot be before event date.');
            }

            // --- Step 4: Check product availability for the requested dates ---
            if (!$product->isRentable()) {
                throw new \Exception('Product is currently under maintenance and not available for rental.');
            }

            if ($product->hasDateConflict($pickupDate, $returnDate)) {
                throw new \Exception('Product is not available for the selected dates. Please check the availability calendar and choose different dates.');
            }

            // --- Step 5: Prepare financial data ---
            $amountPaid = (float) ($data['amount_paid'] ?? 0);
            $deposit = (float) ($data['deposit_amount'] ?? 0);

            // --- Step 6: Check if we're converting from a booking ---
            $bookingId = $data['booking_id'] ?? null;
            $isFromBooking = !empty($bookingId);
            $booking = null;

            if ($isFromBooking) {
                $booking = Bookings::find($bookingId);
                if (!$booking) {
                    throw new \Exception('Booking not found.');
                }

                // Verify that the booking matches the product and client
                if ($booking->product_id != $data['product_id']) {
                    throw new \Exception('Booking product does not match selected product.');
                }

                if ($booking->user_id != $userId) {
                    throw new \Exception('Booking client does not match selected client.');
                }

                // Check if booking is in Confirmed status
                if ($booking->status !== 'Confirmed') {
                    throw new \Exception('Only Confirmed bookings can be converted to rentals.');
                }
            }

            // --- Step 7: Create rental record ---
            $rental = Rentals::create([
                'product_id' => $data['product_id'],
                'customer_id' => $customerId,
                'user_id' => $userId,
                'pickup_date' => $data['pickup_date'],
                'event_date' => $data['event_date'],
                'return_date' => $data['return_date'],
                'rental_price' => $data['rental_price'],
                'deposit_amount' => $deposit,
                'balance_due' => max(0, (float) $data['rental_price'] - $amountPaid),
                'rental_status' => 'Rented',
                'is_returned' => false,
                'penalty_amount' => 0,
            ]);

            // --- Step 8: Record initial payment (optional) ---
            if ($amountPaid > 0) {
                $payment = $rental->payments()->create([
                    'rental_id' => $rental->rental_id,
                    'payment_method' => 'Cash',
                    'amount_paid' => $amountPaid,
                    'payment_date' => now(),
                    'payment_status' => 'Paid',
                    'payment_type' => 'rental',
                ]);

                // Log initial payment
                \Log::info('Initial payment recorded for rental', [
                    'payment_id' => $payment->payment_id,
                    'rental_id' => $rental->rental_id,
                    'amount' => $amountPaid,
                    'payment_method' => 'Cash',
                    'recorded_by' => auth()->id(),
                    'recorded_at' => now()->toDateTimeString(),
                ]);
            }

            // --- Step 9: Update product rental count ---
            $rental->product()->increment('rental_count');

            // --- Step 10: Handle booking conversion if coming from booking ---
            if ($isFromBooking && $booking) {
                // Update booking status to Completed
                $booking->update([
                    'status' => 'Completed',
                    'notes' => ($booking->notes ? $booking->notes . "\n" : '')
                        . 'Converted to rental #' . $rental->rental_id . ' on ' . now()->format('Y-m-d')
                ]);

                // Update product status from Reserved to Available
                $product->update([
                    'status' => 'Available'
                ]);

                // Log the booking conversion
                \Log::info('Booking converted to rental', [
                    'booking_id' => $bookingId,
                    'rental_id' => $rental->rental_id,
                    'product_id' => $product->product_id,
                    'old_product_status' => $product->status,
                    'new_product_status' => 'Available',
                    'old_booking_status' => 'Confirmed',
                    'new_booking_status' => 'Completed',
                    'converted_by' => auth()->id(),
                    'converted_at' => now()->toDateTimeString(),
                ]);

                // Send notification about booking conversion
                Notification::make()
                    ->title('Booking Converted to Rental')
                    ->body("Booking #{$bookingId} has been successfully converted to Rental #{$rental->rental_id}. Product status has been updated from Reserved to Available.")
                    ->success()
                    ->send();
            } else {
                // For regular rentals (not from booking), keep product status as is (Available)
                // Don't change product status for regular rentals
                \Log::info('Regular rental created', [
                    'rental_id' => $rental->rental_id,
                    'product_id' => $product->product_id,
                    'product_status' => $product->status,
                ]);
            }

            // --- Step 11: Log success ---
            \Log::info('Rental created successfully', [
                'rental_id' => $rental->rental_id,
                'product_id' => $rental->product_id,
                'customer_id' => $rental->customer_id,
                'user_id' => $rental->user_id,
                'rental_price' => $rental->rental_price,
                'from_booking' => $isFromBooking,
                'booking_id' => $bookingId,
            ]);

            return $rental;
        } catch (\Exception $e) {
            // --- Step 12: Log failure and rethrow ---
            \Log::error('Rental creation failed: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rental created successfully!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
