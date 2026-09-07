<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'country',
        'state',
        'role',
        'status',
        'avatar_url',
        'registered_by_vendor_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function registeredByVendor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'registered_by_vendor_id');
    }

    public function registeredClients(): HasMany
    {
        return $this->hasMany(self::class, 'registered_by_vendor_id');
    }

    public function kycProfile(): HasOne
    {
        return $this->hasOne(KycProfile::class);
    }

    public function storefrontSetting(): HasOne
    {
        return $this->hasOne(StorefrontSetting::class);
    }

    public function vendorServices(): HasMany
    {
        return $this->hasMany(VendorService::class, 'vendor_id');
    }

    public function vendorRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'vendor_id');
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'client_id');
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function directPermissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function hasPermission(string $permissionSlug): bool
    {
        // Global Super Admin access
        if ($this->role === 'admin') {
            return true;
        }

        // Check if user has direct permission override
        if ($this->directPermissions()->where('slug', $permissionSlug)->exists()) {
            return true;
        }

        // Check if user has permission through any assigned role
        foreach ($this->roles as $role) {
            if ($role->slug === 'super-admin') {
                return true;
            }
            if ($role->permissions()->where('slug', $permissionSlug)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->roles()->where('slug', 'super-admin')->exists();
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}
