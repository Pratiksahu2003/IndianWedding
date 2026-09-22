<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Support\Tenant;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('invoices.manage', Tenant::current())
            || $user->canInOrganization('invoices.own', Tenant::current());
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($invoice->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('invoices.manage', Tenant::current())) {
            return true;
        }

        return $invoice->customer?->user_id === $user->id;
    }
}
