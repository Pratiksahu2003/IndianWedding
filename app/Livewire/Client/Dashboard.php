<?php

namespace App\Livewire\Client;

use App\Support\ClientPortal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Your wedding')]
class Dashboard extends Component
{
    public function render()
    {
        $customer = ClientPortal::customer();
        $project = $customer?->projects()->with(['paymentMilestones', 'package'])->latest()->first();

        return view('livewire.client.dashboard', compact('customer', 'project'));
    }
}
