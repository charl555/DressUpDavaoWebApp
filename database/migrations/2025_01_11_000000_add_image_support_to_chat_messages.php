<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('message_type')->default('text')->after('message'); // 'text', 'image', 'inquiry'
            $table->string('image_path')->nullable()->after('message_type');
            $table->json('metadata')->nullable()->after('image_path'); // For storing additional data like product info
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'image_path', 'metadata']);
        });
    }
};
