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
            if (!Schema::hasColumn('system_settings', 'draft_expiry_value')) {
                $table->unsignedInteger('draft_expiry_value')->default(30)->after('privacy_policy');
            }
            if (!Schema::hasColumn('system_settings', 'draft_expiry_unit')) {
                $table->string('draft_expiry_unit', 20)->default('days')->after('draft_expiry_value');
            }
            if (!Schema::hasColumn('system_settings', 'paid_incomplete_expiry_value')) {
                $table->unsignedInteger('paid_incomplete_expiry_value')->default(3)->after('draft_expiry_unit');
            }
            if (!Schema::hasColumn('system_settings', 'paid_incomplete_expiry_unit')) {
                $table->string('paid_incomplete_expiry_unit', 20)->default('months')->after('paid_incomplete_expiry_value');
            }
            if (!Schema::hasColumn('system_settings', 'auto_purge_enabled')) {
                $table->boolean('auto_purge_enabled')->default(true)->after('paid_incomplete_expiry_unit');
            }
            if (!Schema::hasColumn('system_settings', 'purge_action')) {
                $table->string('purge_action', 30)->default('delete')->after('auto_purge_enabled'); // 'delete' or 'mark_expired'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'draft_expiry_value',
                'draft_expiry_unit',
                'paid_incomplete_expiry_value',
                'paid_incomplete_expiry_unit',
                'auto_purge_enabled',
                'purge_action',
            ]);
        });
    }
};
