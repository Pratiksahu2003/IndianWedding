<?php

namespace App\Livewire\Studio\Packages\Concerns;

use App\Models\Package;
use App\Support\PackageOptions;

trait ManagesPackageIncludes
{
    public bool $includes_album = true;

    public bool $includes_video = false;

    public bool $includes_pre_wedding = false;

    public bool $includes_drone = false;

    public bool $includes_candid = false;

    public bool $includes_instagram_reels = false;

    public bool $includes_same_day_edit = false;

    public bool $includes_live_streaming = false;

    public bool $includes_invitation_video = false;

    public bool $includes_destination = false;

    /** @return array<string, array<int, string>> */
    protected function includeValidationRules(): array
    {
        $rules = [];

        foreach (PackageOptions::includeKeys() as $key) {
            $rules[$key] = ['boolean'];
        }

        return $rules;
    }

    /** @return array<string, bool> */
    protected function includeAttributes(): array
    {
        $attributes = [];

        foreach (PackageOptions::includeKeys() as $key) {
            $attributes[$key] = (bool) $this->{$key};
        }

        return $attributes;
    }

    protected function loadIncludesFromPackage(Package $package): void
    {
        foreach (PackageOptions::includeKeys() as $key) {
            $this->{$key} = (bool) $package->{$key};
        }
    }
}
