<?php

namespace App\Policies;

use App\Models\ProjectTask;
use App\Models\User;
use App\Support\Tenant;

class ProjectTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('projects.manage', Tenant::current())
            || $user->canInOrganization('tasks.assigned', Tenant::current());
    }

    public function view(User $user, ProjectTask $task): bool
    {
        if ($task->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('projects.manage', Tenant::current())) {
            return true;
        }

        return $user->canInOrganization('tasks.assigned', Tenant::current())
            && $task->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->canInOrganization('projects.manage', Tenant::current());
    }

    public function update(User $user, ProjectTask $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, ProjectTask $task): bool
    {
        return $user->canDeleteInOrganization(Tenant::current())
            && $user->canInOrganization('projects.manage', Tenant::current())
            && $task->organization_id === Tenant::id();
    }
}
