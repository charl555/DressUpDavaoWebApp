<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalsResource;
use App\Models\Products;
use App\Models\Rentals;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRentals extends CreateRecord
{
    protected static string $resource = RentalsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            // Validate that the product is still available
            $product = Products::find($data['product_id']);
            if (!$product) {
                throw new \Exception('Product not found.');
            }

            if ($product->status !== 'Available') {
                throw new \Exception('Product is no longer available for rental.');
            }

            // Validate that the customer exists
            $customer = \App\Models\Customers::find($data['customer_id']);
            if (!$customer) {
                throw new \Exception('Customer not found.');
            }

            // Validate dates
            $pickupDate = \Carbon\Carbon::parse($data['pickup_date']);
            $eventDate = \Carbon\Carbon::parse($data['event_date']);
            $returnDate = \Carbon\Carbon::parse($data['return_date']);

            if ($pickupDate->isPast()) {
                throw new \Exception('Pickup date cannot be in the past.');
            }

            if ($eventDate->lt($pickupDate)) {
                throw new \Exception('Event date cannot be before pickup date.');
            }

            if ($returnDate->lt($eventDate)) {
                throw new \Exception('Return date cannot be before event date.');
            }

            // Step 1: Create rental with all required fields
            $rental = Rentals::create([
                'product_id' => $data['product_id'],
                'customer_id' => $data['customer_id'],
                'pickup_date' => $data['pickup_date'],
                'event_date' => $data['event_date'],
                'return_date' => $data['return_date'],
                'rental_price' => $data['rental_price'],
                'rental_status' => 'On Going',  // Set default status
                'is_returned' => false,  // Set default return status
                'penalty_amount' => 0,  // Set default penalty
            ]);

            // Step 2: Create payment with correct relationship key and all required fields
            $rental->payments()->create([
                'rental_id' => $rental->rental_id,  // Use correct primary key
                'payment_method' => $data['payment_method'],
                'amount_paid' => $data['amount_paid'],
                'payment_date' => $data['payment_date'],
                'payment_status' => 'Paid',  // Set default payment status
            ]);

            // Step 3: Update product status and increment rental count
            $rental->product()->update([
                'status' => 'Rented',
            ]);
            $rental->product()->increment('rental_count');

            // Log successful creation
            \Log::info('Rental created successfully', [
                'rental_id' => $rental->rental_id,
                'product_id' => $rental->product_id,
                'customer_id' => $rental->customer_id,
                'rental_price' => $rental->rental_price
            ]);

            return $rental;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Rental creation failed: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to show user the error
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
