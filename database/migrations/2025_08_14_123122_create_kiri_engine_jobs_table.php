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
        Schema::create('kiri_engine_jobs', function (Blueprint $table) {
            $table->id('kiri_engine_job_id');
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('serialize_id')->unique();
            $table->string('status')->default('pending');
            $table->string('model_url', 2048)->nullable();
            $table->json('kiri_options')->nullable();
            $table->boolean('is_downloaded')->default(false);
            $table->date('url_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiri_engine_jobs');
    }
};
