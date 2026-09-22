<?php

namespace App\Livewire\Client;

use App\Models\Customer;
use App\Support\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Your wedding')]
class Dashboard extends Component
{
    public function render()
    {
        $customer = Customer::query()->where('user_id', auth()->id())->firstOrFail();
        $project = $customer->projects()->with(['paymentMilestones', 'events', 'gallery', 'package'])->latest()->first();

        return view('livewire.client.dashboard', compact('customer', 'project'));
    }
}
