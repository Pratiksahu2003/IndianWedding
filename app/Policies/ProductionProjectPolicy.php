<?php

namespace App\Policies;

use App\Models\ProductionProject;
use App\Models\User;
use App\Support\Tenant;

class ProductionProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('packages.manage', Tenant::current())
            || $user->canInOrganization('packages.view', Tenant::current());
    }

    public function view(User $user, ProductionProject $project): bool
    {
        return $this->viewAny($user) && $project->organization_id === Tenant::id();
    }

    public function create(User $user): bool
    {
        return $user->canInOrganization('packages.manage', Tenant::current());
    }

    public function update(User $user, ProductionProject $project): bool
    {
        return $this->create($user) && $project->organization_id === Tenant::id();
    }

    public function delete(User $user, ProductionProject $project): bool
    {
        return $user->canDeleteInOrganization(Tenant::current())
            && $this->update($user, $project);
    }
}
