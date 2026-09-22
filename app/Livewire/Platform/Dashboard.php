<?php

namespace App\Livewire\Platform;

use App\Models\Organization;
use App\Models\Payment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.platform')]
#[Title('Platform')]
class Dashboard extends Component
{
    public function render()
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        return view('livewire.platform.dashboard', [
            'organizations' => Organization::query()->with('plan')->latest()->get(),
            'users' => User::query()->count(),
            'revenue' => Payment::withoutTenant()->where('status', 'paid')->sum('amount'),
        ]);
    }
}
