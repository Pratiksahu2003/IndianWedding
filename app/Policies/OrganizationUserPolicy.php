<?php

namespace App\Policies;

use App\Models\OrganizationUser;
use App\Models\User;
use App\Support\Tenant;

class OrganizationUserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasFullStudioAccess()
            || $user->canInOrganization('users.manage', Tenant::current());
    }

    public function view(User $user, OrganizationUser $member): bool
    {
        return $member->organization_id === Tenant::id();
    }

    public function create(User $user): bool
    {
        return $user->hasFullStudioAccess()
            || $user->canInOrganization('users.manage', Tenant::current());
    }

    public function update(User $user, OrganizationUser $member): bool
    {
        if ($member->organization_id !== Tenant::id()) {
            return false;
        }

        return $user->hasFullStudioAccess()
            || $user->canInOrganization('users.manage', Tenant::current());
    }

    public function delete(User $user, OrganizationUser $member): bool
    {
        if ($member->organization_id !== Tenant::id()) {
            return false;
        }

        if ($member->is_owner || $member->user_id === $user->id) {
            return false;
        }

        if (! $user->canDeleteInOrganization(Tenant::current())) {
            return false;
        }

        return $user->hasFullStudioAccess()
            || $user->canInOrganization('users.manage', Tenant::current());
    }
}
