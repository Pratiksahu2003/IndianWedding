<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Support\Tenant;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('projects.manage', Tenant::current())
            || $user->canInOrganization('projects.assigned', Tenant::current())
            || $user->canInOrganization('portal.view', Tenant::current());
    }

    public function view(User $user, Project $project): bool
    {
        if ($project->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('projects.manage', Tenant::current())) {
            return true;
        }

        if ($user->canInOrganization('portal.view', Tenant::current())) {
            return $project->customer?->user_id === $user->id;
        }

        return $project->team()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->canInOrganization('projects.manage', Tenant::current());
    }

    public function update(User $user, Project $project): bool
    {
        return $user->canInOrganization('projects.manage', Tenant::current())
            && $project->organization_id === Tenant::id();
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->canDeleteInOrganization(Tenant::current())
            && $this->update($user, $project);
    }

    public function assign(User $user, Project $project): bool
    {
        return $user->canInOrganization('team.assign', Tenant::current())
            && $project->organization_id === Tenant::id();
    }
}
