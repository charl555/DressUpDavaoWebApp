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
        Schema::create('product_measurements', function (Blueprint $table) {
            $table->id('product_measurements_id');
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('gown_upper_chest', 8, 2)->nullable();
            $table->decimal('gown_bust', 8, 2)->nullable();
            $table->decimal('gown_chest', 8, 2)->nullable();
            $table->decimal('gown_shoulder', 8, 2)->nullable();
            $table->decimal('gown_waist', 8, 2)->nullable();
            $table->decimal('gown_hips', 8, 2)->nullable();
            $table->decimal('gown_back_width', 8, 2)->nullable();
            $table->decimal('gown_figure', 8, 2)->nullable();
            $table->decimal('gown_arm_hole', 8, 2)->nullable();
            $table->decimal('gown_neck', 8, 2)->nullable();
            $table->decimal('gown_bust_point', 8, 2)->nullable();
            $table->decimal('gown_bust_distance', 8, 2)->nullable();
            $table->decimal('gown_sleeve_width', 8, 2)->nullable();
            $table->decimal('gown_length', 8, 2)->nullable();

            $table->decimal('jacket_chest', 8, 2)->nullable();
            $table->decimal('jacket_bust', 8, 2)->nullable();
            $table->decimal('jacket_shoulder', 8, 2)->nullable();
            $table->decimal('jacket_sleeve_length', 8, 2)->nullable();
            $table->decimal('jacket_sleeve_width', 8, 2)->nullable();
            $table->decimal('jacket_length', 8, 2)->nullable();
            $table->decimal('jacket_bicep', 8, 2)->nullable();
            $table->decimal('jacket_arm_hole', 8, 2)->nullable();
            $table->decimal('jacket_waist', 8, 2)->nullable();
            $table->decimal('jacket_hip', 8, 2)->nullable();
            $table->decimal('jacket_back_width', 8, 2)->nullable();
            $table->decimal('jacket_figure', 8, 2)->nullable();

            $table->decimal('trouser_waist', 8, 2)->nullable();
            $table->decimal('trouser_hip', 8, 2)->nullable();
            $table->decimal('trouser_inseam', 8, 2)->nullable();
            $table->decimal('trouser_outseam', 8, 2)->nullable();
            $table->decimal('trouser_thigh', 8, 2)->nullable();
            $table->decimal('trouser_knee', 8, 2)->nullable();
            $table->decimal('trouser_bottom', 8, 2)->nullable();
            $table->decimal('trouser_leg_opening', 8, 2)->nullable();
            $table->decimal('trouser_crotch', 8, 2)->nullable();
            $table->decimal('trouser_length', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_measurements');
    }
};
