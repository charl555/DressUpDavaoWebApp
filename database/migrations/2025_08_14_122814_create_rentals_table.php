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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreignId('customer_id')
                ->constrained('customers', 'customer_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('pickup_date');
            $table->date('event_date');
            $table->date('return_date');
            $table->date('actual_return_date')->nullable();
            $table->string('rental_status')->default('On Going');
            $table->integer('rental_price');
            $table->integer('penalty_amount')->default(0)->nullable();
            $table->boolean('is_returned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
