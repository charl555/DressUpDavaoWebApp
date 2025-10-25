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

            $table->unsignedBigInteger('customer_id')->nullable();
            $table
                ->foreign('customer_id')
                ->references('customer_id')
                ->on('customers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('user_id')->nullable();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('pickup_date');
            $table->date('event_date');
            $table->date('return_date');
            $table->date('actual_return_date')->nullable();
            $table->string('rental_status');
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);
            $table->decimal('rental_price', 10, 2);
            $table->decimal('penalty_amount', 10, 2)->default(0)->nullable();
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
