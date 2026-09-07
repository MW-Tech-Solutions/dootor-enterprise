<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'stage_id',
        'stage_name',
        'status',
        'changed_by_user_id',
        'notes',
        'is_user_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_user_visible' => 'boolean',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(ServiceWorkflowStage::class, 'stage_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
