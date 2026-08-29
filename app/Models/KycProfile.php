<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nin',
        'id_card_url',
        'proof_of_address_url',
        'personal_image_url',
        'bank_name',
        'account_name',
        'account_number',
        'terms_agreed',
        'privacy_agreed',
        'data_consent_agreed',
        'digital_signature',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'terms_agreed' => 'boolean',
            'privacy_agreed' => 'boolean',
            'data_consent_agreed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
