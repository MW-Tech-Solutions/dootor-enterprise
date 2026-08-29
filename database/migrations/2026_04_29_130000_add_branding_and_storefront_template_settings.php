<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('platform_name');
            $table->string('brand_template')->default('classic')->after('logo_url');
            $table->string('brand_theme')->default('light')->after('brand_template');
            $table->string('brand_color_palette')->default('default')->after('brand_theme');
            $table->string('brand_primary_color')->default('#2091eb')->after('brand_color_palette');
            $table->string('brand_secondary_color')->default('#c2e59c')->after('brand_primary_color');
            $table->string('brand_gradient_from')->default('#64b3f4')->after('brand_secondary_color');
            $table->string('brand_gradient_to')->default('#c2e59c')->after('brand_gradient_from');
        });

        Schema::table('storefront_settings', function (Blueprint $table) {
            $table->string('storefront_template')->default('classic')->after('storefront_about');
        });
    }

    public function down(): void
    {
        Schema::table('storefront_settings', function (Blueprint $table) {
            $table->dropColumn('storefront_template');
        });

        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'brand_template',
                'brand_theme',
                'brand_color_palette',
                'brand_primary_color',
                'brand_secondary_color',
                'brand_gradient_from',
                'brand_gradient_to',
            ]);
        });
    }
};
