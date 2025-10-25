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
        Schema::create('shop_account_requests', function (Blueprint $table) {
            $table->id('shop_account_request_id');
            $table
                ->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreignId('shop_id')
                ->constrained('shops', 'shop_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('document_url')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_account_requests');
    }
};
