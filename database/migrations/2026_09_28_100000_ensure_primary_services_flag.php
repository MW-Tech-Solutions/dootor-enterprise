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
            $primaryNames = [
                'Passport Services',
                'NIN Services',
                'Emergency Travel Certificate',
                'Authorization Letter / Power of Attorney',
                'Waiver / Appointment Reschedule',
                'Same Day Collection',
            ];

            // 1. Set is_primary = 1 for designated primary services
            \DB::table('services')
                ->whereIn('name', $primaryNames)
                ->update(['is_primary' => true]);

            // 2. Set is_primary = 0 for non-primary services
            \DB::table('services')
                ->whereNotIn('name', $primaryNames)
                ->update(['is_primary' => false]);
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
