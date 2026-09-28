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
        if (Schema::hasTable('services')) {
            \DB::table('services')
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('is_primary', false)->orWhere('is_primary', 0)->orWhereNull('is_primary');
                })
                ->update(['is_primary' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
