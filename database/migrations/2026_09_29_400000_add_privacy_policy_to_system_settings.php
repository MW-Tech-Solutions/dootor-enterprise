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
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'privacy_policy')) {
                $table->longText('privacy_policy')->nullable()->after('refund_policy');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (Schema::hasColumn('system_settings', 'privacy_policy')) {
                $table->dropColumn('privacy_policy');
            }
        });
    }
};
