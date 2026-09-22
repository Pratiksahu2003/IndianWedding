<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use App\Support\Tenant;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('clients.manage', Tenant::current());
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->viewAny($user) && $customer->organization_id === Tenant::id();
    }
}
