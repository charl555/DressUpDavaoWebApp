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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table
                ->foreignId('rental_id')
                ->constrained('rentals', 'rental_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('payment_method');
            $table->string('payment_status')->default('Paid');
            $table->decimal('amount_paid', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_type', [
                'deposit', 'rental', 'penalty', 'refund'
            ])->default('rental');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
