<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorefrontSetting extends Model
{
    protected $fillable = [
        'user_id',
        'storefront_name',
        'storefront_about',
        'storefront_template',
        'theme',
        'color_palette',
        'background_image_url',
        'gradient',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
