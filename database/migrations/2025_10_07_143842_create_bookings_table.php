<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');

            // The customer (user who inquired)
            $table
                ->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // The shop owner (admin who manages the booking)
            $table
                ->foreignId('created_by')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Product being reserved
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Booking details
            $table->date('booking_date');
            $table->string('status')->default('Pending');  // Pending, Confirmed, Cancelled, Completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
