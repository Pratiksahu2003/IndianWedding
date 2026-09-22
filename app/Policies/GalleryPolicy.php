<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;
use App\Support\Tenant;

class GalleryPolicy
{
    public function view(User $user, Gallery $gallery): bool
    {
        if ($gallery->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('galleries.manage', Tenant::current())) {
            return true;
        }

        return $gallery->is_released && $gallery->project?->customer?->user_id === $user->id;
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $user->canInOrganization('galleries.manage', Tenant::current())
            && $gallery->organization_id === Tenant::id();
    }
}
