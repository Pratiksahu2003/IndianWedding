<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;
use App\Support\Tenant;

class PackagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('packages.manage', Tenant::current())
            || $user->canInOrganization('packages.view', Tenant::current());
    }

    public function view(User $user, Package $package): bool
    {
        return $this->viewAny($user) && $package->organization_id === Tenant::id();
    }

    public function create(User $user): bool
    {
        return $user->canInOrganization('packages.manage', Tenant::current());
    }

    public function update(User $user, Package $package): bool
    {
        return $this->create($user) && $package->organization_id === Tenant::id();
    }
}
