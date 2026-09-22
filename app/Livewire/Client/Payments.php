<?php

namespace App\Livewire\Client;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.client')]
#[Title('Payments')]
class Payments extends Component
{
    public function render()
    {
        $customer = Customer::query()->where('user_id', auth()->id())->firstOrFail();

        return view('livewire.client.payments', [
            'invoices' => $customer->projects()->with('invoices.items')->get()->pluck('invoices')->flatten(),
            'milestones' => $customer->projects()->with('paymentMilestones')->get()->pluck('paymentMilestones')->flatten(),
        ]);
    }
}
