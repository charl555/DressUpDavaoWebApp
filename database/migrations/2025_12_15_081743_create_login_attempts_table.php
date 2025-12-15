<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('ip_address', 45)->index();
            $table->timestamp('attempted_at');
            $table->boolean('success')->default(false);
            $table->timestamps();
        });

        Schema::create('login_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->index();
            $table->string('ip_address', 45)->index();
            $table->integer('attempts')->default(1);
            $table->timestamp('blocked_until');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_blocks');
        Schema::dropIfExists('login_attempts');
    }
};
