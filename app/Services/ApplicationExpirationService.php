<?php

namespace App\Services;

use App\Models\ApplicationNote;
use App\Models\ApplicationStageHistory;
use App\Models\AssignmentHistory;
use App\Models\RequestDocument;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationExpirationService
{
    /**
     * Get the active expiration settings.
     */
    public static function getSettings(): array
    {
        $setting = SystemSetting::first();

        return [
            'draft_expiry_value' => (int) ($setting?->draft_expiry_value ?: 30),
            'draft_expiry_unit' => (string) ($setting?->draft_expiry_unit ?: 'days'),
            'paid_incomplete_expiry_value' => (int) ($setting?->paid_incomplete_expiry_value ?: 3),
            'paid_incomplete_expiry_unit' => (string) ($setting?->paid_incomplete_expiry_unit ?: 'months'),
            'auto_purge_enabled' => (bool) ($setting ? $setting->auto_purge_enabled : true),
            'purge_action' => (string) ($setting?->purge_action ?: 'delete'),
        ];
    }

    /**
     * Calculate cutoff timestamp for draft / halfway applications.
     */
    public static function getDraftCutoff(): Carbon
    {
        $settings = self::getSettings();
        $val = $settings['draft_expiry_value'];
        $unit = $settings['draft_expiry_unit'];

        return $unit === 'months' ? Carbon::now()->subMonths($val) : Carbon::now()->subDays($val);
    }

    /**
     * Calculate cutoff timestamp for paid incomplete applications.
     */
    public static function getPaidIncompleteCutoff(): Carbon
    {
        $settings = self::getSettings();
        $val = $settings['paid_incomplete_expiry_value'];
        $unit = $settings['paid_incomplete_expiry_unit'];

        return $unit === 'months' ? Carbon::now()->subMonths($val) : Carbon::now()->subDays($val);
    }

    /**
     * Query for unfinished halfway applications (Draft, In Progress, Unpaid).
     */
    public static function getUnfinishedDraftsQuery(): Builder
    {
        return ServiceRequest::with(['client', 'service', 'subService'])
            ->where(function ($q) {
                $q->whereIn('status', [ServiceRequest::STATUS_DRAFT, ServiceRequest::STATUS_IN_PROGRESS, 'Draft', 'In Progress', 'Payment Pending'])
                  ->orWhere(function ($sub) {
                      $sub->where('progress_percent', '<', 100)
                          ->whereNotIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CANCELLED, ServiceRequest::STATUS_REJECTED]);
                  });
            })
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'Paid')
                  ->orWhereNull('payment_status');
            })
            ->where(function ($q) {
                $q->whereNull('amount_paid')
                  ->orWhere('amount_paid', '<=', 0);
            });
    }

    /**
     * Query for paid incomplete applications.
     */
    public static function getPaidIncompleteQuery(): Builder
    {
        return ServiceRequest::with(['client', 'service', 'subService'])
            ->where(function ($q) {
                $q->where('payment_status', 'Paid')
                  ->orWhere('amount_paid', '>', 0);
            })
            ->whereNotIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CANCELLED, ServiceRequest::STATUS_REJECTED])
            ->where(function ($q) {
                // Incomplete indicators: progress < 100 or still stuck in draft / unassigned / awaiting steps
                $q->where('progress_percent', '<', 100)
                  ->orWhereIn('status', [ServiceRequest::STATUS_DRAFT, ServiceRequest::STATUS_IN_PROGRESS, 'Draft', 'In Progress', ServiceRequest::STATUS_AWAITING_ASSIGNMENT, 'Payment Confirmed']);
            });
    }

    /**
     * Annotate a ServiceRequest model with dynamic expiry calculations.
     */
    public static function annotateApplication(ServiceRequest $app, string $type = 'draft'): ServiceRequest
    {
        $settings = self::getSettings();
        $lastActivity = $app->last_saved_at ?? $app->updated_at ?? $app->created_at ?? Carbon::now();
        $lastActivity = Carbon::parse($lastActivity);

        if ($type === 'draft') {
            $val = $settings['draft_expiry_value'];
            $unit = $settings['draft_expiry_unit'];
        } else {
            $val = $settings['paid_incomplete_expiry_value'];
            $unit = $settings['paid_incomplete_expiry_unit'];
        }

        $expiresAt = $unit === 'months' ? $lastActivity->copy()->addMonths($val) : $lastActivity->copy()->addDays($val);
        $daysInactive = (int) $lastActivity->diffInDays(Carbon::now());
        $isExpired = Carbon::now()->greaterThanOrEqualTo($expiresAt);
        $daysRemaining = (int) Carbon::now()->diffInDays($expiresAt, false);

        $app->setAttribute('computed_last_activity', $lastActivity);
        $app->setAttribute('computed_days_inactive', $daysInactive);
        $app->setAttribute('computed_expires_at', $expiresAt);
        $app->setAttribute('computed_is_expired', $isExpired);
        $app->setAttribute('computed_days_remaining', $daysRemaining);

        if ($isExpired) {
            $app->setAttribute('computed_badge', 'expired');
        } elseif ($daysRemaining <= 5) {
            $app->setAttribute('computed_badge', 'expiring_soon');
        } else {
            $app->setAttribute('computed_badge', 'active');
        }

        return $app;
    }

    /**
     * Get high-level expiration analytics and summary metrics.
     */
    public static function getDashboardStats(): array
    {
        $draftCutoff = self::getDraftCutoff();
        $paidCutoff = self::getPaidIncompleteCutoff();

        // 5-day warning threshold
        $settings = self::getSettings();
        $draftWarningCutoff = $settings['draft_expiry_unit'] === 'months'
            ? Carbon::now()->subMonths($settings['draft_expiry_value'])->addDays(5)
            : Carbon::now()->subDays($settings['draft_expiry_value'])->addDays(5);

        $paidWarningCutoff = $settings['paid_incomplete_expiry_unit'] === 'months'
            ? Carbon::now()->subMonths($settings['paid_incomplete_expiry_value'])->addDays(7)
            : Carbon::now()->subDays($settings['paid_incomplete_expiry_value'])->addDays(7);

        $draftsQuery = self::getUnfinishedDraftsQuery();
        $totalDrafts = (clone $draftsQuery)->count();
        $expiredDrafts = (clone $draftsQuery)->where(function ($q) use ($draftCutoff) {
            $q->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '<=', $draftCutoff);
        })->count();
        $expiringSoonDrafts = (clone $draftsQuery)->where(function ($q) use ($draftCutoff, $draftWarningCutoff) {
            $q->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '>', $draftCutoff)
              ->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '<=', $draftWarningCutoff);
        })->count();

        $paidQuery = self::getPaidIncompleteQuery();
        $totalPaidIncomplete = (clone $paidQuery)->count();
        $expiredPaidIncomplete = (clone $paidQuery)->where(function ($q) use ($paidCutoff) {
            $q->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '<=', $paidCutoff);
        })->count();
        $expiringSoonPaid = (clone $paidQuery)->where(function ($q) use ($paidCutoff, $paidWarningCutoff) {
            $q->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '>', $paidCutoff)
              ->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '<=', $paidWarningCutoff);
        })->count();

        return [
            'total_drafts' => $totalDrafts,
            'expired_drafts' => $expiredDrafts,
            'expiring_soon_drafts' => $expiringSoonDrafts,
            'total_paid_incomplete' => $totalPaidIncomplete,
            'expired_paid_incomplete' => $expiredPaidIncomplete,
            'expiring_soon_paid' => $expiringSoonPaid,
            'total_expired' => $expiredDrafts + $expiredPaidIncomplete,
            'settings' => $settings,
        ];
    }

    /**
     * Delete all physical files associated with a service request.
     */
    public static function deleteApplicationFiles(ServiceRequest $serviceRequest): void
    {
        $filePaths = [];

        if (!empty($serviceRequest->passport_photo_path)) {
            $filePaths[] = $serviceRequest->passport_photo_path;
        }

        if (!empty($serviceRequest->manual_form_path)) {
            $filePaths[] = $serviceRequest->manual_form_path;
        }

        // Gather document attachments
        $documents = RequestDocument::where('service_request_id', $serviceRequest->id)->get();
        foreach ($documents as $doc) {
            if (!empty($doc->file_path)) {
                $filePaths[] = $doc->file_path;
            }
        }

        // Gather any files uploaded inside form_data
        if (is_array($serviceRequest->form_data)) {
            foreach ($serviceRequest->form_data as $key => $val) {
                if (is_string($val) && (str_contains($val, 'uploads/') || str_contains($val, 'documents/'))) {
                    $filePaths[] = $val;
                }
            }
        }

        // Delete each physical file safely
        foreach ($filePaths as $path) {
            $cleanRel = trim(str_replace(['/storage/', 'storage/'], '', $path), '/\\');
            $storagePath = storage_path('app/public/' . $cleanRel);
            $publicPath = public_path('storage/' . $cleanRel);

            if (file_exists($storagePath) && is_file($storagePath)) {
                @unlink($storagePath);
            }
            if (file_exists($publicPath) && is_file($publicPath)) {
                @unlink($publicPath);
            }
        }
    }

    /**
     * Purge or mark expired a single service request.
     */
    public static function purgeApplication(ServiceRequest $serviceRequest, ?string $action = null): bool
    {
        $settings = self::getSettings();
        $action = $action ?: $settings['purge_action'];

        $ref = $serviceRequest->reference_number;
        $clientName = $serviceRequest->client_name ?: ($serviceRequest->client ? $serviceRequest->client->name : 'Unknown Client');

        if ($action === 'mark_expired') {
            $serviceRequest->update([
                'status' => 'Expired',
            ]);
            $serviceRequest->syncStatusToWorkflowStage('Expired', 'Application marked Expired due to inactivity threshold expiration.');

            AuditLogger::log(
                'application_marked_expired',
                'ServiceRequest',
                (string) $serviceRequest->id,
                $ref,
                ['status' => $serviceRequest->getOriginal('status')],
                ['status' => 'Expired', 'reason' => 'Inactivity period exceeded']
            );

            return true;
        }

        // Default: Delete permanently
        self::deleteApplicationFiles($serviceRequest);

        // Delete related database records to maintain database cleanliness
        RequestDocument::where('service_request_id', $serviceRequest->id)->delete();
        ApplicationStageHistory::where('service_request_id', $serviceRequest->id)->delete();
        ApplicationNote::where('service_request_id', $serviceRequest->id)->delete();
        AssignmentHistory::where('service_request_id', $serviceRequest->id)->delete();

        $serviceRequest->delete();

        AuditLogger::log(
            'application_expired_purged',
            'ServiceRequest',
            (string) $serviceRequest->id,
            $ref,
            ['client' => $clientName, 'ref' => $ref],
            ['action' => 'purged_and_deleted', 'reason' => 'Inactivity expiration cutoff reached']
        );

        return true;
    }

    /**
     * Batch purge all expired draft and paid incomplete applications.
     */
    public static function purgeAllExpired(?string $action = null): array
    {
        $settings = self::getSettings();
        $action = $action ?: $settings['purge_action'];

        $draftCutoff = self::getDraftCutoff();
        $paidCutoff = self::getPaidIncompleteCutoff();

        $expiredDrafts = self::getUnfinishedDraftsQuery()
            ->where(function ($q) use ($draftCutoff) {
                $q->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '<=', $draftCutoff);
            })->get();

        $expiredPaid = self::getPaidIncompleteQuery()
            ->where(function ($q) use ($paidCutoff) {
                $q->where(DB::raw('COALESCE(last_saved_at, updated_at, created_at)'), '<=', $paidCutoff);
            })->get();

        $purgedDraftsCount = 0;
        $purgedPaidCount = 0;

        foreach ($expiredDrafts as $draft) {
            try {
                self::purgeApplication($draft, $action);
                $purgedDraftsCount++;
            } catch (\Throwable $e) {
                Log::error("Failed to purge expired draft [ID: {$draft->id}]: " . $e->getMessage());
            }
        }

        foreach ($expiredPaid as $paidApp) {
            try {
                self::purgeApplication($paidApp, $action);
                $purgedPaidCount++;
            } catch (\Throwable $e) {
                Log::error("Failed to purge expired paid app [ID: {$paidApp->id}]: " . $e->getMessage());
            }
        }

        return [
            'purged_drafts' => $purgedDraftsCount,
            'purged_paid' => $purgedPaidCount,
            'total_purged' => $purgedDraftsCount + $purgedPaidCount,
        ];
    }
}
