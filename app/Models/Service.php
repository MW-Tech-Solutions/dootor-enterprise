<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'service_fee',
        'processing_fee',
        'processing_days',
        'description',
        'image_url',
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
            'required_documents' => 'array',
            'custom_fields' => 'array',
        ];
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
