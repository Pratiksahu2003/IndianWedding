<?php

namespace App\Livewire\Studio\Settings\Concerns;

use App\Support\Tenant;

trait ManagesStudioSettings
{
    protected function ensureSettingsAdmin(): void
    {
        $org = Tenant::current();
        abort_unless($org, 404);
        abort_unless(auth()->user()?->canInOrganization('settings.manage', $org), 403);
    }
}
