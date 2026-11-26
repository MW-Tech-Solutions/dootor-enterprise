<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('promo_video_url', 500)->nullable()->after('youtube_url');
            $table->string('promo_video_title', 255)->nullable()->after('promo_video_url');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['promo_video_url', 'promo_video_title']);
        });
    }
};
