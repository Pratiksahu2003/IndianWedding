<?php

namespace App\Policies;

use App\Models\MediaFile;
use App\Models\User;
use App\Support\Tenant;

class MediaFilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canInOrganization('files.manage', Tenant::current())
            || $user->canInOrganization('files.upload', Tenant::current())
            || $user->canInOrganization('files.raw', Tenant::current());
    }

    public function view(User $user, MediaFile $file): bool
    {
        if ($file->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('files.manage', Tenant::current()) || $user->canInOrganization('files.raw', Tenant::current())) {
            return true;
        }

        if ($user->canInOrganization('files.upload', Tenant::current())) {
            return $file->project?->team()->where('user_id', $user->id)->exists();
        }

        if ($user->canInOrganization('files.download', Tenant::current())) {
            $gallery = $file->project?->gallery;

            return $file->project?->customer?->user_id === $user->id
                && $gallery?->is_released
                && $gallery?->allow_download;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->canInOrganization('files.upload', Tenant::current())
            || $user->canInOrganization('files.manage', Tenant::current());
    }

    public function update(User $user, MediaFile $file): bool
    {
        if ($file->organization_id !== Tenant::id()) {
            return false;
        }

        if ($user->canInOrganization('files.manage', Tenant::current())) {
            return true;
        }

        if ($user->canInOrganization('files.upload', Tenant::current()) || $user->canInOrganization('files.raw', Tenant::current())) {
            return $file->project?->team()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    public function delete(User $user, MediaFile $file): bool
    {
        return $user->canDeleteInOrganization(Tenant::current())
            && $this->update($user, $file);
    }
}
