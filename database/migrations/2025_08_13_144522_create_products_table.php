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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table
                ->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('type');
            $table->string('subtype');
            $table->text('description')->nullable();
            $table->text('inclusions')->nullable();
            $table->string('status')->default('Available');
            $table->string('colors');
            $table->string('fabric')->nullable();
            $table->string('size');
            $table->integer('rental_price');
            $table->integer('rental_count')->default(0);
            $table->string('maintenance_needed')->default('No');
            $table->string('visibility')->default('No');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
