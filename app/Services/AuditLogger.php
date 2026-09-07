<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an administrative audit log entry.
     */
    public static function log(
        string $action,
        ?string $targetType = null,
        ?string $targetId = null,
        ?string $referenceNumber = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user ? trim($user->first_name . ' ' . $user->last_name) : 'System',
            'user_role' => $user?->role ?? 'system',
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => (string) $targetId,
            'reference_number' => $referenceNumber,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
