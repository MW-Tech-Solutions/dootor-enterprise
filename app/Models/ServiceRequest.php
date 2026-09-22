<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    protected $fillable = [
        'reference_number',
        'application_method',
        'service_id',
        'vendor_service_id',
        'service_name',
        'price',
        'amount_paid',
        'outstanding_balance',
        'vendor_id',
        'assigned_staff_id',
        'assigned_role_id',
        'current_stage_id',
        'current_stage_name',
        'vendor_name',
        'client_id',
        'client_name',
        'client_email',
        'status',
        'payment_reference',
        'payment_gateway',
        'payment_status',
        'documents',
        'form_data',
        'passport_photo_path',
        'manual_form_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'amount_paid' => 'float',
            'outstanding_balance' => 'float',
            'documents' => 'array',
            'form_data' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->reference_number)) {
                $request->reference_number = \App\Services\ReferenceNumberGenerator::generate($request->service_id);
            }
        });

        static::saved(function ($request) {
            if ($request->wasChanged('status') || $request->wasChanged('payment_status')) {
                $request->syncStatusToWorkflowStage();
            }
        });
    }

    /**
     * Automatically sync current_stage_id, current_stage_name, and log stage history whenever status changes.
     */
    public function syncStatusToWorkflowStage(?string $newStatus = null, ?string $notes = null, ?int $changedByUserId = null): void
    {
        $statusToSync = $newStatus ?? $this->status;
        if (empty($statusToSync)) {
            return;
        }

        $matchingStage = null;
        if ($this->service_id) {
            $matchingStage = ServiceWorkflowStage::where('service_id', $this->service_id)
                ->where(function ($q) use ($statusToSync) {
                    $q->where('stage_name', $statusToSync)
                      ->orWhere('status_key', $statusToSync);
                })->first();

            if (!$matchingStage) {
                $stages = ServiceWorkflowStage::where('service_id', $this->service_id)->get();
                foreach ($stages as $stg) {
                    if (strcasecmp($stg->stage_name, $statusToSync) === 0 || strcasecmp($stg->status_key, $statusToSync) === 0) {
                        $matchingStage = $stg;
                        break;
                    }
                }
            }
        }

        $updateData = ['status' => $statusToSync];
        if ($matchingStage) {
            $updateData['current_stage_id'] = $matchingStage->id;
            $updateData['current_stage_name'] = $matchingStage->stage_name;
        } else {
            $updateData['current_stage_name'] = $statusToSync;
        }

        $this->updateQuietly($updateData);

        // Ensure ApplicationStageHistory entry exists
        $alreadyLogged = ApplicationStageHistory::where('service_request_id', $this->id)
            ->where(function ($q) use ($matchingStage, $statusToSync) {
                if ($matchingStage) {
                    $q->where('stage_id', $matchingStage->id);
                }
                $q->orWhere('stage_name', $statusToSync)
                  ->orWhere('status', $statusToSync);
            })->exists();

        if (!$alreadyLogged) {
            ApplicationStageHistory::create([
                'service_request_id' => $this->id,
                'stage_id' => $matchingStage?->id ?? $this->current_stage_id,
                'stage_name' => $matchingStage?->stage_name ?? $statusToSync,
                'status' => $statusToSync,
                'changed_by_user_id' => $changedByUserId ?? auth()->id() ?? $this->client_id,
                'notes' => $notes ?? ("Status updated to " . $statusToSync . "."),
                'is_user_visible' => true,
            ]);
        }
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function vendorService(): BelongsTo
    {
        return $this->belongsTo(VendorService::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'assigned_role_id');
    }

    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(ServiceWorkflowStage::class, 'current_stage_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function requestDocuments(): HasMany
    {
        return $this->hasMany(RequestDocument::class, 'service_request_id');
    }

    public function stageHistories(): HasMany
    {
        return $this->hasMany(ApplicationStageHistory::class, 'service_request_id')->orderBy('created_at', 'asc');
    }

    public function applicationNotes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class, 'service_request_id')->orderBy('created_at', 'desc');
    }
}
