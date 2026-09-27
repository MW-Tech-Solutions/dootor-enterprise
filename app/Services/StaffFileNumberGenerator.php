<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class StaffFileNumberGenerator
{
    /**
     * Generate a unique, immutable Staff File Number in format DE/STF/YYYY/000001
     */
    public static function generate(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $prefix = "DE/STF/{$year}/";

            // Find max sequence for current year
            $latest = User::withTrashed()
                ->where('staff_file_number', 'LIKE', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->value('staff_file_number');

            if ($latest) {
                $parts = explode('/', $latest);
                $sequence = (int) end($parts) + 1;
            } else {
                $sequence = 1;
            }

            $number = $prefix . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);

            // Double check uniqueness
            while (User::withTrashed()->where('staff_file_number', $number)->exists()) {
                $sequence++;
                $number = $prefix . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            }

            return $number;
        });
    }

    /**
     * Ensure user has a staff file number if they are staff/admin.
     */
    public static function ensureForUser(User $user): ?string
    {
        if (in_array($user->role, ['admin', 'staff', 'vendor']) || $user->roles()->exists()) {
            if (empty($user->staff_file_number)) {
                $fileNum = self::generate();
                $user->update(['staff_file_number' => $fileNum]);
                return $fileNum;
            }
            return $user->staff_file_number;
        }
        return null;
    }
}
