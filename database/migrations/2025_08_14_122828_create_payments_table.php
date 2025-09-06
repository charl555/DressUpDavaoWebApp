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
            $table->integer('amount_paid');
            $table->date('payment_date');
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
