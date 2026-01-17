<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalsResource;
use App\Models\Customers;
use App\Models\Products;
use App\Models\Rentals;
use App\Models\User;
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

            // --- Step 6: Create rental record ---
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

            // --- Step 7: Record initial payment (optional) ---
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

            // --- Step 8: Increment rental count (no longer updating product status) ---
            $rental->product()->increment('rental_count');

            // --- Step 8: Log success ---
            \Log::info('Rental created successfully', [
                'rental_id' => $rental->rental_id,
                'product_id' => $rental->product_id,
                'customer_id' => $rental->customer_id,
                'user_id' => $rental->user_id,
                'rental_price' => $rental->rental_price,
            ]);

            return $rental;
        } catch (\Exception $e) {
            // --- Step 9: Log failure and rethrow ---
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
