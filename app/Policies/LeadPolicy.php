<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use App\Support\Tenant;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('leads.manage', Tenant::current());
    }

    public function view(User $user, Lead $lead): bool
    {
        return $this->viewAny($user) && $lead->organization_id === Tenant::id();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Lead $lead): bool
    {
        return $this->view($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $this->view($user, $lead);
    }
}
