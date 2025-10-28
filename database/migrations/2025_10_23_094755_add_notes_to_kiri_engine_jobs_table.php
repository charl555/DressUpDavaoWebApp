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
        Schema::table('kiri_engine_jobs', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('url_expiry');
            $table->text('error_message')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kiri_engine_jobs', function (Blueprint $table) {
            $table->dropColumn(['notes', 'error_message']);
        });
    }
};
