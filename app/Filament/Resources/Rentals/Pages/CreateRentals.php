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
        // Step 1: Create a new rental record using the submitted data
        $rental = Rentals::create([
            'product_id' => $data['product_id'],
            'customer_id' => $data['customer_id'],
            'pickup_date' => $data['pickup_date'],
            'event_date' => $data['event_date'],
            'return_date' => $data['return_date'],
            'rental_price' => $data['rental_price'],
        ]);
        $rental->payments()->create([
            'rental_id' => $rental->rental_id,
            'payment_method' => $data['payment_method'],
            'amount_paid' => $data['amount_paid'],
            'payment_date' => $data['payment_date'],
        ]);

        // Step 2: Update the product's status to "Rented"
        Products::where('product_id', $data['product_id'])->update(['status' => 'Rented']);
        Products::where('product_id', $data['product_id'])->increment('rental_count');

        return $rental;
    }
}
