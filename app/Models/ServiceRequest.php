<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'service_id',
        'vendor_service_id',
        'service_name',
        'price',
        'vendor_id',
        'vendor_name',
        'client_id',
        'client_name',
        'client_email',
        'status',
        'payment_reference',
        'payment_gateway',
        'payment_status',
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'documents' => 'array',
        ];
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
