<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'image_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
        ];
    }

    public function vendorServices(): HasMany
    {
        return $this->hasMany(VendorService::class);
    }
}
