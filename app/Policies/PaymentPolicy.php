<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Support\Tenant;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('payments.manage', Tenant::current())
            || $user->canInOrganization('payments.own', Tenant::current());
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($payment->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('payments.manage', Tenant::current())) {
            return true;
        }

        return $payment->customer?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->canInOrganization('payments.manage', Tenant::current());
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->canInOrganization('payments.manage', Tenant::current())
            && $payment->organization_id === Tenant::id();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->canDeleteInOrganization(Tenant::current())
            && $this->update($user, $payment);
    }
}
