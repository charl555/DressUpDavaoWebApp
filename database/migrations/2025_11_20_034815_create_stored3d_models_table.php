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
        Schema::create('stored3d_models', function (Blueprint $table) {
            $table->id('stored_3d_model_id');
            $table
                ->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreignId('kiri_engine_job_id')
                ->constrained('kiri_engine_jobs', 'kiri_engine_job_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('model_name');
            $table->string('model_path');
            $table->string('original_filename')->nullable();
            $table->string('file_size')->nullable();
            $table->json('model_files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stored3d_models');
    }
};
