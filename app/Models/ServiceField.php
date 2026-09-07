<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceField extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'field_label',
        'field_name',
        'field_type',
        'placeholder',
        'help_text',
        'is_required',
        'options',
        'allowed_file_types',
        'max_file_size',
        'sort_order',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_enabled' => 'boolean',
            'options' => 'array',
            'sort_order' => 'integer',
            'max_file_size' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
