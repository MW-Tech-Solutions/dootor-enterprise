<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'gemini_api_key')) {
                $table->string('gemini_api_key', 500)->nullable()->after('auto_purge_enabled');
            }
            if (!Schema::hasColumn('system_settings', 'chatbot_enabled')) {
                $table->boolean('chatbot_enabled')->default(true)->after('gemini_api_key');
            }
            if (!Schema::hasColumn('system_settings', 'chatbot_system_prompt')) {
                $table->text('chatbot_system_prompt')->nullable()->after('chatbot_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumnIfExists(['gemini_api_key', 'chatbot_enabled', 'chatbot_system_prompt']);
        });
    }
};
