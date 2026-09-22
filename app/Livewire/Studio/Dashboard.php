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
        abort_unless(auth()->user()?->is_super_admin || auth()->user()?->roleIn(\App\Support\Tenant::current())?->isStaff(), 403);

        return view('livewire.studio.dashboard', [
            'stats' => $dashboard->studio(),
        ]);
    }
}
