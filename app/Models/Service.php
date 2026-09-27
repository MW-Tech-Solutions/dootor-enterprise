<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'category',
        'price',
        'service_fee',
        'processing_fee',
        'processing_days',
        'description',
        'short_description',
        'image_url',
        'icon',
        'is_primary',
        'required_documents',
        'custom_fields',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'service_fee' => 'float',
            'processing_fee' => 'float',
            'processing_days' => 'integer',
            'is_primary' => 'boolean',
            'required_documents' => 'array',
            'custom_fields' => 'array',
        ];
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subServices(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 'Active');
    }

    public function vendorServices(): HasMany
    {
        return $this->hasMany(VendorService::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ServiceField::class)->orderBy('sort_order', 'asc');
    }

    public function workflowStages(): HasMany
    {
        return $this->hasMany(ServiceWorkflowStage::class)->orderBy('sort_order', 'asc');
    }
}
