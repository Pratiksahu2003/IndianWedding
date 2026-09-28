<?php

namespace App\Livewire\Client;

use App\Support\ClientPortal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Payments')]
class Payments extends Component
{
    public function render()
    {
        $customer = ClientPortal::customer();

        return view('livewire.client.payments', [
            'invoices' => $customer
                ? $customer->projects()->with('invoices.items')->get()->pluck('invoices')->flatten()
                : collect(),
            'milestones' => $customer
                ? $customer->projects()->with('paymentMilestones')->get()->pluck('paymentMilestones')->flatten()
                : collect(),
        ]);
    }
}
