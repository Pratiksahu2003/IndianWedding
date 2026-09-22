<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'whatsapp', 'avatar_path', 'is_super_admin', 'is_active', 'timezone', 'locale', 'notification_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_users')
            ->withPivot(['role', 'is_owner', 'permissions'])
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationUser::class);
    }

    public function roleIn(?Organization $organization): ?Role
    {
        if ($this->is_super_admin) {
            return Role::SuperAdmin;
        }

        if (! $organization) {
            return null;
        }

        $membership = $this->memberships()
            ->where('organization_id', $organization->id)
            ->first();

        if (! $membership) {
            return null;
        }

        return $membership->role instanceof Role
            ? $membership->role
            : Role::from($membership->role);
    }

    public function canInOrganization(string $permission, ?Organization $organization = null): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        $role = $this->roleIn($organization);

        return $role?->can($permission) ?? false;
    }

    public function isClient(?Organization $organization = null): bool
    {
        return $this->roleIn($organization) === Role::Client;
    }
}
