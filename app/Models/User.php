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

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone',
        'country',
        'country_applying_from',
        'country_service_requested',
        'state',
        'city',
        'role',
        'status',
        'avatar_url',
        'staff_file_number',
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
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
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

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_staff_id');
    }

    public function assignmentHistories(): HasMany
    {
        return $this->hasMany(AssignmentHistory::class, 'new_staff_id');
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
        $assignedRoles = $this->relationLoaded('roles')
            ? $this->roles
            : $this->roles()->where('is_active', true)->get();

        // 1. Super Admin role check - unrestricted system access
        foreach ($assignedRoles as $role) {
            if (isset($role->is_active) && !$role->is_active) {
                continue;
            }
            if ($role->slug === 'super-admin') {
                return true;
            }
        }

        // 2. Base Admin fallback only if NO RBAC roles have been assigned to user yet
        if ($assignedRoles->isEmpty() && $this->role === 'admin') {
            return true;
        }

        if ($assignedRoles->isEmpty()) {
            return false;
        }

        // 3. Compute active role permissions set (Union of all assigned active roles' permissions)
        $rolePermissionSlugs = [];
        foreach ($assignedRoles as $role) {
            if (isset($role->is_active) && !$role->is_active) {
                continue;
            }

            // Always query database directly to ensure database is authoritative source of truth (Requirement 15)
            $perms = $role->permissions()->pluck('slug')->toArray();

            foreach ($perms as $slug) {
                $rolePermissionSlugs[$slug] = true;
            }
        }

        // Enforce Requirement 9: Effective User Permissions ⊆ Role Permissions.
        // A user CANNOT receive a permission that their assigned role itself is not authorized to have.
        if (!isset($rolePermissionSlugs[$permissionSlug])) {
            return false;
        }

        // 4. Handle direct user permissions restriction (if direct permissions are explicitly assigned)
        $directPermsCount = $this->relationLoaded('directPermissions')
            ? $this->directPermissions->count()
            : $this->directPermissions()->count();

        if ($directPermsCount > 0) {
            $hasDirectPermission = $this->relationLoaded('directPermissions')
                ? $this->directPermissions->contains('slug', $permissionSlug)
                : $this->directPermissions()->where('slug', $permissionSlug)->exists();

            return $hasDirectPermission;
        }

        return true;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->roles()->whereIn('slug', ['super-admin', 'administrator'])->exists();
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
