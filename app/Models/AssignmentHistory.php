<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'previous_staff_id',
        'new_staff_id',
        'assigned_by_user_id',
        'notes',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function previousStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'previous_staff_id')->withTrashed();
    }

    public function newStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_staff_id')->withTrashed();
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id')->withTrashed();
    }
}
