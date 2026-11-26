<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('paypal_client_id')->nullable()->after('credo_base_url');
            $table->string('paypal_client_secret')->nullable()->after('paypal_client_id');
            $table->string('moneris_store_id')->nullable()->after('paypal_client_secret');
            $table->string('moneris_api_token')->nullable()->after('moneris_store_id');
            $table->string('moneris_checkout_id')->nullable()->after('moneris_api_token');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_client_id',
                'paypal_client_secret',
                'moneris_store_id',
                'moneris_api_token',
                'moneris_checkout_id',
            ]);
        });
    }
};
