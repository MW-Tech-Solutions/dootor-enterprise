<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('hero_badge_text')->nullable()->after('brand_gradient_to');
            $table->string('hero_title')->nullable()->after('hero_badge_text');
            $table->text('hero_subtitle')->nullable()->after('hero_title');
            $table->string('hero_bg_image')->nullable()->after('hero_subtitle');
            $table->string('hero_glass_style')->default('light_glass')->after('hero_bg_image');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_badge_text',
                'hero_title',
                'hero_subtitle',
                'hero_bg_image',
                'hero_glass_style',
            ]);
        });
    }
};
