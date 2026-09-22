<?php

namespace App\Livewire\Studio;

use App\Services\DashboardService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.studio')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render(DashboardService $dashboard)
    {
        $user = auth()->user();
        if (! \App\Support\Tenant::id()) {
            \App\Support\Tenant::set(\App\Support\Tenant::soleOrganizationId());
        }
        abort_unless($user?->roleIn()?->isStaff() || $user?->hasFullStudioAccess(), 403);

        return view('livewire.studio.dashboard', [
            'stats' => $dashboard->studio(),
        ]);
    }
}
