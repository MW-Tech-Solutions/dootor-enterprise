<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceWorkflowStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'stage_name',
        'description',
        'status_key',
        'sort_order',
        'is_user_visible',
        'notification_enabled',
        'assigned_role_id',
        'assigned_user_id',
        'estimated_days',
    ];

    protected function casts(): array
    {
        return [
            'is_user_visible' => 'boolean',
            'notification_enabled' => 'boolean',
            'sort_order' => 'integer',
            'estimated_days' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'assigned_role_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
